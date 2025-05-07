<?php

namespace App\Livewire\Reports;

use App\Models\InventoryLog;
use App\Models\Outlet;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryLogs extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $outletFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $actionType = '';

    public $outlets = [];
    public $actionTypes = ['add', 'remove', 'adjust', 'transfer'];

    public function mount()
    {
        $this->outlets = Outlet::all();
    }

    public function render()
    {
        $logs = InventoryLog::with(['inventory.product', 'inventory.outlet', 'user'])
            ->when($this->searchTerm, function ($query) {
                return $query->whereHas('inventory.product', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('barcode', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->when($this->outletFilter, function ($query) {
                return $query->whereHas('inventory.outlet', function ($q) {
                    $q->where('id', $this->outletFilter);
                });
            })
            ->when($this->actionType, function ($query) {
                return $query->where('action', $this->actionType);
            })
            ->when($this->dateFrom && $this->dateTo, function ($query) {
                return $query->whereBetween('created_at', [
                    $this->dateFrom . ' 00:00:00',
                    $this->dateTo . ' 23:59:59'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('livewire.reports.inventory-logs', compact('logs'));
    }

    public function resetFilters()
    {
        $this->reset([
            'searchTerm',
            'outletFilter',
            'dateFrom',
            'dateTo',
            'actionType'
        ]);
        $this->resetPage();
    }
}