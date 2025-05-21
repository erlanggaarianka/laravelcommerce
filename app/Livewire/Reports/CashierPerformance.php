<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Outlet;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class CashierPerformance extends Component
{
    use WithPagination;

    public $dateRange = 'this_month';
    public $customStart;
    public $customEnd;
    public $outletFilter = '';
    public $sortField = 'total_sales';
    public $sortDirection = 'desc';

    public $outlets = [];

    protected $queryString = [
        'dateRange' => ['except' => 'today'],
        'customStart' => ['except' => ''],
        'customEnd' => ['except' => ''],
        'outletFilter' => ['except' => ''],
        'sortField' => ['except' => 'total_sales'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->outlets = Outlet::all();
    }

    public function getDates()
    {
        return match ($this->dateRange) {
            'today' => [Carbon::today(), Carbon::today()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
            'this_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'custom' => [Carbon::parse($this->customStart), Carbon::parse($this->customEnd)],
            default => [Carbon::today(), Carbon::today()],
        };
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function getCashiersPerformance()
    {
        [$startDate, $endDate] = $this->getDates();

        return User::query()
            ->whereHas('transactions', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed');
            })
            ->when($this->outletFilter, function ($query) {
                $query->where('outlet_id', $this->outletFilter);
            })
            ->withCount(['transactions as transaction_count' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed');
            }])
            ->withSum(['transactions as total_sales' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed');
            }], 'grand_total')
            ->withAvg(['transactions as avg_sale' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed');
            }], 'grand_total')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.reports.cashier-performance', [
            'cashiers' => $this->getCashiersPerformance(),
            'totalSales' => $this->getCashiersPerformance()->sum('total_sales'),
            'totalTransactions' => $this->getCashiersPerformance()->sum('transaction_count'),
        ]);
    }
}
