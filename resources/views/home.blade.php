@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 py-2">
            <h2>Welcome, <b>{{ Auth::user()->name }}</b></h2>
            <h4>Let's start your business journey with <b>{{ config('app.name', 'GOOD CREDIT') }}</b></h4>
            <hr>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row mb-4">
        <!-- Today's Sales -->
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('transactions.list') }}" class="card-link">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2">Today's Sales</h6>
                        <h3 class="card-title text-success">{{ 'Rp. ' . $todaySales }}</h3>
                        <p class="small text-muted mb-0">{{ $todayTransactions }} transactions</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Monthly Sales -->
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('reports.view') }}?dateRange=this_month&groupBy=day" class="card-link">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2">Monthly Sales</h6>
                        <h3 class="card-title text-primary">{{ 'Rp. ' . $monthlySales }}</h3>
                        <p class="small text-muted mb-0">{{ $monthlyTransactions }} transactions</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Low Stock Items -->
        <div class="col-md-4 col-lg-2 mb-3">
            <a href="{{ route('inventory.list') }}?lowStockFilter=1" class="card-link">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2">Low Stock</h6>
                        <h3 class="card-title text-warning">{{ $lowStockCount }}</h3>
                        <p class="small text-muted mb-0">items need restock</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Product -->
        <div class="{{ Auth::user()->role === 'Owner' ? 'col-md-6 col-lg-2' : 'col-md-6 col-lg-6' }} mb-3">
            <a href="{{ route('reports.view') }}?groupBy=product" class="card-link">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2">Top Product</h6>
                        {{-- Change $topProduct to $topProductModel --}}
                        <h5 class="card-title">{{ $topProductModel->name ?? 'N/A' }}</h5>
                        <p class="small text-muted mb-0">Rp. {{ $topProductSales ?? 0 }} sales</p>
                    </div>
                </div>
            </a>
        </div>

        @if (Auth::user()->role === 'Owner')
            <!-- Best Cashier -->
            <div class="col-md-6 col-lg-2 mb-3">
                <a href="{{ route('reports.view') }}#cashier" class="card-link">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2">Top Cashier</h6>
                            {{-- Change $topCashier to $topCashierModel --}}
                            <h5 class="card-title">{{ $topCashierModel->name ?? 'N/A' }}</h5>
                            <p class="small text-muted mb-0">Rp. {{ $topCashierSales ?? 0 }} sales</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Outlet Performance -->
            <div class="col-md-6 col-lg-2 mb-3">
                <a href="{{ route('reports.view') }}?groupBy=outlet" class="card-link">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2">Top Outlet</h6>
                            {{-- Change $topOutlet to $topOutletModel --}}
                            <h5 class="card-title">{{ $topOutletModel->name ?? 'N/A' }}</h5>
                            <p class="small text-muted mb-0">Rp. {{ $topOutletSales ?? 0 }} sales</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sales Trend (Last 7 Days)</h5>
                    <a href="{{ route('reports.view') }}" class="btn btn-sm btn-outline-primary">View Report</a>
                </div>
                <div class="card-body">
                    <canvas id="salesTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment Methods</h5>
                    <a href="{{ route('transactions.list') }}" class="btn btn-sm btn-outline-primary">View Transactions</a>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodsChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Transactions</h5>
                    <a href="{{ route('transactions.list') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Cashier</th>
                                    <th>Outlet</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transactions.receipt', $transaction->id) }}">
                                                {{ $transaction->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $transaction->created_at->format('d M H:i') }}</td>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td>{{ $transaction->outlet->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">Rp. {{ $transaction->grand_total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No recent transactions</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesTrendLabels),
                datasets: [{
                    label: 'Sales Amount',
                    data: @json($salesTrendData),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Payment Methods Chart
        const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: @json($paymentMethodsLabels),
                datasets: [{
                    data: @json($paymentMethodsData),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
