<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use App\Models\Outlet;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CreateTransaction extends Component
{
    #[Validate('required|exists:outlets,id')]
    public $outletId;

    #[Validate('required|exists:products,id')]
    public $productId;

    #[Validate('required|integer|min:1')]
    public $quantity = 1;

    #[Validate('required|numeric|min:0')]
    public $price;

    #[Validate('nullable|numeric|min:0')]
    public $discount = 0;

    #[Validate('required|in:cash,credit_card,debit_card,e_wallet')]
    public $paymentMethod = 'cash';

    #[Validate('required|numeric|min:0')]
    public $cashReceived = 0;

    #[Validate('nullable|string|max:255')]
    public $notes;

    public $cart = [];
    public $products = [];
    public $outlets = [];
    public $subtotal = 0;
    public $tax = 0;
    public $grandTotal = 0;
    public $change = 0;

    public function mount()
    {
        $this->outlets = Outlet::all();
        $this->loadProducts();

        // Set default outlet for cashier
        if (Auth::user()->outlet) {
            $this->outletId = Auth::user()->outlet->id;
        }
    }

    protected function loadProducts()
    {
        if ($this->outletId) {
            $this->products = Product::whereHas('inventories', function($q) {
                $q->where('outlet_id', $this->outletId);
            })->get();
        } else {
            $this->products = Product::all();
        }
    }

    public function updatedOutletId()
    {
        $this->loadProducts();
        $this->reset(['productId', 'price']);
    }

    public function updatedProductId()
    {
        if ($this->productId && $this->outletId) {
            $product = Product::find($this->productId);
            $this->price = $product->price;
        }
    }

    public function addToCart()
    {
        $this->validate([
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $product = Product::find($this->productId);

        // Check inventory - using the correct relationship
        $inventory = Inventory::where('product_id', $this->productId)
            ->where('outlet_id', $this->outletId)
            ->first();

        if (!$inventory || $inventory->quantity < $this->quantity) {
            $this->addError('quantity', 'Not enough stock available');
            return;
        }

        $item = [
            'product_id' => $this->productId,
            'name' => $product->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount' => $this->discount ?? 0,
            'subtotal' => ($this->price * $this->quantity) - ($this->discount ?? 0),
            'inventory_id' => $inventory->id // Store inventory ID for later update
        ];

        $this->cart[] = $item;
        $this->calculateTotals();
        $this->reset(['productId', 'quantity', 'price', 'discount']);
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotals();
    }

    protected function calculateTotals()
    {
        $this->subtotal = collect($this->cart)->sum('subtotal');
        $this->tax = $this->subtotal * 0.1; // Assuming 10% tax
        $this->grandTotal = $this->subtotal + $this->tax;
        $this->change = max(0, round((float)$this->cashReceived - (float)$this->grandTotal, 2));
    }

    public function updatedCashReceived()
    {
        $this->calculateTotals();
    }

    public function save()
    {
        $this->validate([
            'outletId' => 'required|exists:outlets,id',
            'paymentMethod' => 'required',
            'cart' => 'required|array|min:1',
            'cashReceived' => 'required|numeric|min:' . $this->grandTotal,
        ]);

        // Create transaction
        $transaction = Transaction::create([
            'outlet_id' => $this->outletId,
            'user_id' => Auth::id(),
            'invoice_number' => app(InvoiceService::class)->generate($this->outletId),
            'total_amount' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => collect($this->cart)->sum('discount'),
            'grand_total' => $this->grandTotal,
            'cash_received' => $this->cashReceived,
            'change' => $this->change,
            'payment_method' => $this->paymentMethod,
            'status' => 'completed',
            'notes' => $this->notes,
        ]);

        // Add items to transaction and update inventory
        foreach ($this->cart as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'],
                'subtotal' => $item['subtotal'],
            ]);

            // Update inventory using the stored inventory_id
            $inventory = Inventory::find($item['inventory_id']);
            if ($inventory) {
                $inventory->decrement('quantity', $item['quantity']);

                // Log inventory change
                $inventory->logs()->create([
                    'user_id' => Auth::id(),
                    'quantity' => -$item['quantity'],
                    'reason' => 'Sold in transaction #' . $transaction->invoice_number,
                    'remaining_stock' => $inventory->fresh()->quantity
                ]);
            }
        }

        session()->flash('message', 'Transaction completed successfully!');
        $this->redirect(route('transactions.receipt', $transaction->id));
    }

    public function render()
    {
        return view('livewire.transaction.create-transaction');
    }
}
