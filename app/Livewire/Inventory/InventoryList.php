<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Outlet;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $outletFilter = '';
    public $lowStockFilter = false;
    public $outlets = [];

    // For modals
    public $showAdjustModal = false;
    public $showTransferModal = false;
    public $selectedInventory = null;

    public function mount()
    {
        $this->outlets = Outlet::all();
    }

    public function render()
    {
        $inventories = Inventory::with(['product', 'outlet'])
            ->when($this->searchTerm, function ($query) {
                return $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('barcode', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->when($this->outletFilter, function ($query) {
                return $query->where('outlet_id', $this->outletFilter);
            })
            ->when($this->lowStockFilter, function ($query) {
                return $query->whereColumn('quantity', '<=', 'reorder_level');
            })
            ->orderBy('outlet_id')
            ->orderBy('product_id')
            ->paginate(25);

        return view('livewire.inventory.inventory-list', compact('inventories'));
    }

    public function openAdjustModal($inventoryId)
    {
        $this->selectedInventory = Inventory::with(['product', 'outlet'])->find($inventoryId);
        $this->showAdjustModal = true;
    }

    public function openTransferModal($inventoryId)
    {
        $this->selectedInventory = Inventory::with(['product', 'outlet'])->find($inventoryId);
        $this->showTransferModal = true;
    }

    public function closeModal()
    {
        $this->reset(['showAdjustModal', 'showTransferModal', 'selectedInventory']);
    }

    public function resetFilters()
    {
        $this->reset(['searchTerm', 'outletFilter', 'lowStockFilter']);
        $this->resetPage();
    }
}
