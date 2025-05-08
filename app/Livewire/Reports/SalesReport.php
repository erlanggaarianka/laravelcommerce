<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use App\Models\Outlet;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class SalesReport extends Component
{
    use WithPagination;

    public $dateRange = 'today';
    public $customStart;
    public $customEnd;
    public $outletFilter = '';
    public $productFilter = '';
    public $groupBy = 'day'; // day, week, month, product, outlet
    public $chartType = 'bar';

    public $outlets = [];
    public $products = [];

    protected $queryString = [
        'dateRange' => ['except' => 'today'],
        'customStart' => ['except' => ''],
        'customEnd' => ['except' => ''],
        'outletFilter' => ['except' => ''],
        'productFilter' => ['except' => ''],
        'groupBy' => ['except' => 'day'],
    ];

    public function mount()
    {
        $this->outlets = Outlet::all();
        $this->products = Product::all();
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

    public function getSalesData()
    {
        [$startDate, $endDate] = $this->getDates();

        $query = Transaction::with(['items.product', 'outlet'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');

        if ($this->outletFilter) {
            $query->where('outlet_id', $this->outletFilter);
        }

        if ($this->productFilter) {
            $query->whereHas('items', function ($q) {
                $q->where('product_id', $this->productFilter);
            });
        }

        return $query->get();
    }

    public function getGroupedSales()
    {
        $sales = $this->getSalesData();

        return match ($this->groupBy) {
            'day' => $sales->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            }),
            'week' => $sales->groupBy(function ($item) {
                return $item->created_at->format('Y-W');
            }),
            'month' => $sales->groupBy(function ($item) {
                return $item->created_at->format('Y-m');
            }),
            'product' => $sales->flatMap(function ($transaction) {
                return $transaction->items->map(function ($item) use ($transaction) {
                    return (object) [
                        'key' => $item->product_id,
                        'name' => $item->product->name,
                        'date' => $transaction->created_at,
                        'amount' => $item->subtotal
                    ];
                });
            })->groupBy('key'),
            'outlet' => $sales->groupBy('outlet_id'),
            default => $sales->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            }),
        };
    }

    public function render()
    {
        $groupedSales = $this->getGroupedSales();
        $totalSales = $this->getSalesData()->sum('grand_total');
        $totalTransactions = $this->getSalesData()->count();

        return view('livewire.reports.sales-report', [
            'groupedSales' => $groupedSales,
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
