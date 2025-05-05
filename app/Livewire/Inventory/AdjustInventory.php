<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class AdjustInventory extends Component
{
    use WithFileUploads;

    #[Validate('required|exists:outlets,id')]
    public $outletId;

    #[Validate('required|exists:products,id')]
    public $productId;

    #[Validate('required|in:add,remove')]
    public $adjustmentType = 'add';

    #[Validate('required|integer|min:1')]
    public $quantity;

    #[Validate('required|string|max:255')]
    public $reason;

    public $currentStock = 0;
    public $products = [];
    public $outlets = [];

    public function mount()
    {
        $this->outlets = Outlet::all();
        $this->products = Product::all();
    }

    public function updatedOutletId()
    {
        $this->updateCurrentStock();
    }

    public function updatedProductId()
    {
        $this->updateCurrentStock();
    }

    protected function updateCurrentStock()
    {
        if ($this->outletId && $this->productId) {
            $inventory = Inventory::where('outlet_id', $this->outletId)
                ->where('product_id', $this->productId)
                ->first();

            $this->currentStock = $inventory ? $inventory->quantity : 0;
        } else {
            $this->currentStock = 0;
        }
    }

    public function save()
    {
        $this->validate();

        // Find or create inventory record
        $inventory = Inventory::firstOrCreate(
            [
                'outlet_id' => $this->outletId,
                'product_id' => $this->productId
            ],
            ['quantity' => 0, 'reorder_level' => 10]
        );

        // Perform adjustment
        if ($this->adjustmentType === 'add') {
            $inventory->increment('quantity', $this->quantity);
        } else {
            if ($this->quantity > $inventory->quantity) {
                $this->addError('quantity', 'Cannot remove more than current stock');
                return;
            }
            $inventory->decrement('quantity', $this->quantity);
        }

        // Record the adjustment
        $inventory->logs()->create([
            'user_id' => Auth::id(),
            'quantity' => $this->adjustmentType === 'add' ? $this->quantity : -$this->quantity,
            'reason' => $this->reason,
            'remaining_stock' => $inventory->fresh()->quantity
        ]);

        // Reset form
        $this->reset(['quantity', 'reason']);
        $this->updateCurrentStock();

        session()->flash('message', 'Inventory adjusted successfully!');

        $this->redirect(route('inventory.list'));
    }

    public function render()
    {
        return view('livewire.inventory.adjust-inventory');
    }
}
