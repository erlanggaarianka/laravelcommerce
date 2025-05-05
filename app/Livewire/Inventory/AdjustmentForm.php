<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class AdjustmentForm extends Component
{
    public $inventoryId;
    public $product;
    public $outlet;
    public $currentStock;

    #[Validate('required|in:add,remove')]
    public $adjustmentType = 'add';

    #[Validate('required|integer|min:1')]
    public $quantity;

    #[Validate('required|string|max:255')]
    public $reason;

    public function mount($inventoryId = null)
    {
        if ($inventoryId) {
            $inventory = Inventory::with(['product', 'outlet'])->findOrFail($inventoryId);
            $this->inventoryId = $inventory->id;
            $this->product = $inventory->product;
            $this->outlet = $inventory->outlet;
            $this->currentStock = $inventory->quantity;
        }
    }

    public function save()
    {
        $this->validate();

        $inventory = Inventory::findOrFail($this->inventoryId);

        if ($this->adjustmentType === 'add') {
            $inventory->increment('quantity', $this->quantity);
        } else {
            if ($this->quantity > $inventory->quantity) {
                $this->addError('quantity', 'Cannot remove more than current stock');
                return;
            }
            $inventory->decrement('quantity', $this->quantity);
        }

        // Record the adjustment in your inventory history/log
        $inventory->logs()->create([
            'user_id' => Auth::id(),
            'quantity' => $this->adjustmentType === 'add' ? $this->quantity : -$this->quantity,
            'reason' => $this->reason,
            'remaining_stock' => $inventory->fresh()->quantity
        ]);

        session()->flash('message', 'Inventory adjusted successfully!');
        $this->redirect(route('inventory.list'));
    }

    public function render()
    {
        return view('livewire.inventory.adjustment-form');
    }
}
