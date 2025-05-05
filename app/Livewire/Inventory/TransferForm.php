<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class TransferForm extends Component
{
    public $inventoryId;
    public $product;
    public $sourceOutlet;
    public $currentStock;

    #[Validate('required|exists:outlets,id')]
    public $destinationOutletId;

    #[Validate('required|integer|min:1')]
    public $quantity;

    #[Validate('required|string|max:255')]
    public $reason;

    public $outlets = [];

    public function mount($inventoryId = null)
    {
        $this->outlets = Outlet::where('id', '!=', $this->sourceOutlet?->id)->get();

        if ($inventoryId) {
            $inventory = Inventory::with(['product', 'outlet'])->findOrFail($inventoryId);
            $this->inventoryId = $inventory->id;
            $this->product = $inventory->product;
            $this->sourceOutlet = $inventory->outlet;
            $this->currentStock = $inventory->quantity;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->quantity > $this->currentStock) {
            $this->addError('quantity', 'Cannot transfer more than current stock');
            return;
        }

        // Get source and destination inventories
        $sourceInventory = Inventory::findOrFail($this->inventoryId);
        $destinationInventory = Inventory::firstOrCreate([
            'product_id' => $sourceInventory->product_id,
            'outlet_id' => $this->destinationOutletId
        ], [
            'quantity' => 0,
            'reorder_level' => $sourceInventory->reorder_level
        ]);

        // Perform transfer
        $sourceInventory->decrement('quantity', $this->quantity);
        $destinationInventory->increment('quantity', $this->quantity);

        // Record the transfer in your inventory history/log
        $sourceInventory->logs()->create([
            'user_id' => Auth::id(),
            'quantity' => -$this->quantity,
            'reason' => 'Transfer to ' . Outlet::find($this->destinationOutletId)->name . ': ' . $this->reason,
            'remaining_stock' => $sourceInventory->fresh()->quantity
        ]);

        $destinationInventory->logs()->create([
            'user_id' => Auth::id(),
            'quantity' => $this->quantity,
            'reason' => 'Transfer from ' . $this->sourceOutlet->name . ': ' . $this->reason,
            'remaining_stock' => $destinationInventory->fresh()->quantity
        ]);

        session()->flash('message', 'Inventory transferred successfully!');
        $this->redirect(route('inventory.list'));
    }

    public function render()
    {
        return view('livewire.inventory.transfer-form');
    }
}
