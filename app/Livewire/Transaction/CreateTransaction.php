<?php

namespace App\Livewire\Transaction;

use App\Models\Product;
use App\Models\Outlet;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\InvoiceService; // Assuming you have this service
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate; // For newer Livewire 3 syntax
// If using older Livewire, use protected $rules instead of attributes

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

    #[Validate('required')] // Assuming you fetch payment methods from a model or config
    public $paymentMethod = 'cash'; // Set a default or ensure it's selected

    public $transactionTypeId = 0; // Consider validation if this is always required

    #[Validate('required|numeric|min:0')]
    public $cashReceived = 0;

    #[Validate('nullable|string|max:255')]
    public $notes;

    public $cart = [];
    public $products = [];
    public $outlets = [];

    public $subtotal = 0;
    public $tax = 0; // This will store the calculated tax amount
    public $grandTotal = 0;
    public $change = 0;

    // Properties to store current outlet's tax configuration
    public $currentOutletIsTaxEnabled = false;
    public $currentOutletTaxRate = 0.00;

    public function mount()
    {
        $this->outlets = Outlet::all(); // Consider only active outlets if applicable
        // $this->loadProducts(); // products will be loaded once outletId is set or changed

        if (Auth::user()->outlet_id) { // Check if outlet_id is set for the user
            $this->outletId = Auth::user()->outlet_id;
            $this->fetchOutletTaxSettings(); // Fetch tax settings for the default outlet
            $this->loadProducts(); // Load products for the default outlet
        }
         $this->calculateTotals(); // Calculate totals on mount (cart will be empty initially)
    }

    protected function loadProducts()
    {
        if ($this->outletId) {
            // Assuming products are linked to outlets via a pivot table 'product_outlet'
            // Or through inventory existing for that product in that outlet.
            // Your current query seems fine if 'inventories' link product to outlet.
            $selectedOutlet = Outlet::find($this->outletId);
            if ($selectedOutlet) {
                 // Get products available in the selected outlet (adjust based on your product_outlet pivot or inventory logic)
                $this->products = $selectedOutlet->products()->whereHas('inventories', function($q) {
                    $q->where('outlet_id', $this->outletId)->where('quantity', '>', 0); // Only show products with stock
                })->get();
            } else {
                $this->products = collect(); // Use collect() for an empty collection
            }
        } else {
            $this->products = collect(); // No outlet selected, no products
        }
        $this->reset(['productId', 'price', 'quantity', 'discount']); // Reset product input fields
    }

    protected function fetchOutletTaxSettings()
    {
        if ($this->outletId) {
            $outlet = Outlet::find($this->outletId);
            if ($outlet) {
                $this->currentOutletIsTaxEnabled = $outlet->is_tax_enabled;
                $this->currentOutletTaxRate = $outlet->is_tax_enabled ? $outlet->tax_rate : 0.00;
            } else {
                $this->currentOutletIsTaxEnabled = false;
                $this->currentOutletTaxRate = 0.00;
            }
        } else {
            $this->currentOutletIsTaxEnabled = false;
            $this->currentOutletTaxRate = 0.00;
        }
    }

    public function updatedOutletId($value) // $value is the new outletId
    {
        $this->fetchOutletTaxSettings();
        $this->loadProducts();
        // $this->cart = []; // Optional: Decide if cart should be cleared when outlet changes
        $this->calculateTotals(); // Recalculate totals if cart is not cleared
    }

    public function updatedProductId()
    {
        if ($this->productId && $this->outletId) {
            $product = Product::find($this->productId);
            if ($product) {
                $this->price = $product->price;
            } else {
                $this->price = 0;
            }
        } else {
             $this->price = 0;
        }
        $this->quantity = 1; // Reset quantity
        $this->discount = 0; // Reset discount
    }

    public function addToCart()
    {
        $this->validate([
            'outletId' => 'required|exists:outlets,id', // Ensure outlet is still selected
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $product = Product::find($this->productId);

        $inventory = Inventory::where('product_id', $this->productId)
            ->where('outlet_id', $this->outletId)
            ->first();

        if (!$inventory || $inventory->quantity < $this->quantity) {
            $this->addError('quantity', 'Not enough stock available. Available: ' . ($inventory->quantity ?? 0));
            return;
        }

        // Check if product already in cart to update quantity, or add as new
        $existingCartItemIndex = null;
        foreach ($this->cart as $index => $cartItem) {
            if ($cartItem['product_id'] == $this->productId) {
                $existingCartItemIndex = $index;
                break;
            }
        }

        if ($existingCartItemIndex !== null) {
            // Update existing item's quantity
            $newQuantity = $this->cart[$existingCartItemIndex]['quantity'] + $this->quantity;
            if ($inventory->quantity < $newQuantity) {
                 $this->addError('quantity', 'Not enough stock available to add more. Available: ' . ($inventory->quantity ?? 0) . ', In Cart: ' . $this->cart[$existingCartItemIndex]['quantity']);
                return;
            }
            $this->cart[$existingCartItemIndex]['quantity'] = $newQuantity;
            $this->cart[$existingCartItemIndex]['subtotal'] = ($this->cart[$existingCartItemIndex]['price'] * $newQuantity) - $this->cart[$existingCartItemIndex]['discount'];

        } else {
            // Add new item
            $item = [
                'product_id' => $this->productId,
                'name' => $product->name,
                'price' => $this->price, // Price at the time of adding to cart
                'quantity' => $this->quantity,
                'discount' => $this->discount ?? 0,
                'subtotal' => ($this->price * $this->quantity) - ($this->discount ?? 0),
                'inventory_id' => $inventory->id
            ];
            $this->cart[] = $item;
        }

        $this->calculateTotals();
        $this->reset(['productId', 'quantity', 'price', 'discount']);
        $this->dispatch('product-added-to-cart'); // For potential JS interactions e.g. focus
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart); // Re-index array
        $this->calculateTotals();
    }

    // Allow updating quantity directly in cart
    public function updateCartQuantity($index, $quantity)
    {
        if (!isset($this->cart[$index])) return;

        $quantity = (int)$quantity;
        if ($quantity < 1) {
            $this->removeFromCart($index);
            return;
        }

        $item = $this->cart[$index];
        $inventory = Inventory::find($item['inventory_id']);

        if (!$inventory || $inventory->quantity < $quantity) {
             $this->dispatch('show-error', message: 'Not enough stock for ' . $item['name'] . '. Available: ' . ($inventory->quantity ?? 0));
            // Optionally revert quantity in view or set to max available
            // For now, we'll just prevent update and show error. The view should ideally reflect original if update fails.
            // Or, livewire can automatically revert if the property isn't actually updated.
            return;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['subtotal'] = ($item['price'] * $quantity) - $item['discount'];
        $this->calculateTotals();
    }


    protected function calculateTotals()
    {
        $this->subtotal = collect($this->cart)->sum('subtotal');
        $this->tax = 0; // Reset tax first

        if ($this->currentOutletIsTaxEnabled && $this->currentOutletTaxRate > 0) {
            $this->tax = round($this->subtotal * ($this->currentOutletTaxRate / 100), 2);
        }

        $this->grandTotal = round($this->subtotal + $this->tax, 2);

        // Calculate change based on cashReceived and grandTotal
        // Ensure cashReceived is treated as a number
        $cashReceivedNum = is_numeric($this->cashReceived) ? (float)$this->cashReceived : 0;
        $this->change = max(0, round($cashReceivedNum - $this->grandTotal, 2));
    }

    public function updatedCashReceived()
    {
        // This will automatically trigger calculateTotals if using wire:model.live
        // If not, you might need to call it explicitly or ensure validation handles numeric.
        $this->calculateTotals();
    }

    // Trigger calculation when cart items change (e.g. discount per item)
    public function updatedCart($value, $key)
    {
        // key will be like '0.quantity' or '1.discount'
        // Re-calculate subtotal for the specific item if price, quantity or discount changes
        $parts = explode('.', $key);
        if (count($parts) === 2 && isset($this->cart[$parts[0]])) {
            $index = (int)$parts[0];
            $item = $this->cart[$index];
            $this->cart[$index]['subtotal'] = ($item['price'] * $item['quantity']) - $item['discount'];
        }
        $this->calculateTotals();
    }


    public function save()
    {
        // Ensure outlet tax settings are current before validation that depends on grandTotal
        $this->fetchOutletTaxSettings();
        $this->calculateTotals(); // Recalculate with potentially fresh tax settings

        $this->validate([
            'outletId' => 'required|exists:outlets,id',
            'paymentMethod' => 'required', // Add more specific validation if needed
            'transactionTypeId' => 'required|exists:transaction_types,id', // Assuming you have a transaction_types table
            'cart' => 'required|array|min:1',
            // Validate cashReceived against the final grandTotal
            'cashReceived' => 'required|numeric|min:' . $this->grandTotal,
            'notes' => 'nullable|string|max:255',
        ], [
            'cashReceived.min' => 'Cash received must be at least the grand total amount.',
            'cart.min' => 'Please add at least one product to the cart.'
        ]);

        // Ensure Auth::user() and other dependencies are available
        if (!Auth::check()) {
            session()->flash('error', 'User not authenticated.');
            return; // Or redirect to login
        }

        $invoiceNumber = app(InvoiceService::class)->generate($this->outletId);

        $transaction = Transaction::create([
            'outlet_id' => $this->outletId,
            'user_id' => Auth::id(),
            'invoice_number' => $invoiceNumber, // Your invoice generation logic
            'total_amount' => $this->subtotal, // This is Subtotal (Before Tax)
            'tax' => $this->tax,               // This is the Calculated Tax Amount
            'discount' => collect($this->cart)->sum('discount'), // Sum of item-level discounts
            'grand_total' => $this->grandTotal, // Subtotal + Tax
            'cash_received' => $this->cashReceived,
            'change' => $this->change,
            'payment_method' => $this->paymentMethod,
            'transaction_type_id' => $this->transactionTypeId, // Make sure this model/table exists
            'status' => 'completed', // Or your default status
            'notes' => $this->notes,
            'is_tax_applied' => $this->currentOutletIsTaxEnabled,       // Store tax state
            'tax_rate_snapshot' => $this->currentOutletTaxRate,       // Store tax rate used
        ]);

        foreach ($this->cart as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'],
                'subtotal' => $item['subtotal'],
            ]);

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

        session()->flash('message', "Transaction #{$invoiceNumber} completed successfully!");
        // Consider redirecting to a receipt page
        return redirect()->route('transactions.receipt', $transaction->id); // Ensure this route exists
    }

    public function render()
    {
        return view('livewire.transaction.create-transaction');
    }
}
