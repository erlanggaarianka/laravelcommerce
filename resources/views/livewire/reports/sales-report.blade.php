<div>
    <!-- Filters -->
    <div class="row mb-4 g-2">
        <div class="col-md-2">
            <select class="form-select" wire:model.live="dateRange">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>

        @if($dateRange === 'custom')
            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.live="customStart">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.live="customEnd">
            </div>
        @endif

        <div class="col-md-2">
            <select class="form-select" wire:model.live="outletFilter">
                <option value="">All Outlets</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select" wire:model.live="productFilter">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select" wire:model.live="groupBy">
                <option value="day">Group by Day</option>
                <option value="week">Group by Week</option>
                <option value="month">Group by Month</option>
                <option value="product">Group by Product</option>
                <option value="outlet">Group by Outlet</option>
            </select>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <h3 class="card-text text-success">{{ $totalSales }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Transactions</h5>
                    <h3 class="card-text text-primary">{{ $totalTransactions }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Sales Trend</h5>
        </div>
        <div class="card-body">
            <div style="height: 300px;">
                <canvas id="salesChart" wire:ignore></canvas>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detailed Sales</h5>
            {{-- <button class="btn btn-sm btn-success" wire:click="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export
            </button> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ $groupBy === 'day' ? 'Date' : ($groupBy === 'product' ? 'Product' : ($groupBy === 'outlet' ? 'Outlet' : ucfirst($groupBy))) }}</th>
                            <th class="text-end">Transactions</th>
                            <th class="text-end">Total Sales</th>
                            <th class="text-end">Avg. Sale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedSales as $key => $group)
                            <tr>
                                <td>
                                    @if($groupBy === 'day')
                                        {{ \Carbon\Carbon::parse($key)->format('d M Y') }}
                                    @elseif($groupBy === 'product')
                                        {{ $group->first()['name'] }}
                                    @elseif($groupBy === 'outlet')
                                        {{ $group->first()->outlet->name }}
                                    @else
                                        {{ $key }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{ $groupBy === 'product' ? $group->count() : $group->count() }}
                                </td>
                                <td class="text-end">
                                    {{ $groupBy === 'product' ? $group->sum('amount') : $group->sum('grand_total') }}
                                </td>
                                <td class="text-end">
                                    {{ $groupBy === 'product' ? $group->avg('amount') : $group->avg('grand_total') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:init', function() {
                // Initialize chart with empty data
                const ctx = document.getElementById('salesChart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Sales Amount',
                            data: [],
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Function to update chart
                function updateChart() {
                    // Get data from Livewire component
                    const groupedSales = @json($groupedSales);
                    const groupBy = @json($groupBy);

                    // Prepare labels and data
                    const labels = [];
                    const data = [];

                    Object.entries(groupedSales).forEach(([key, group]) => {
                        if (groupBy === 'day') {
                            labels.push(new Date(key).toLocaleDateString());
                        } else if (groupBy === 'product') {
                            labels.push(group[0].name);
                        } else if (groupBy === 'outlet') {
                            labels.push(group[0].outlet.name);
                        } else {
                            labels.push(key);
                        }

                        if (groupBy === 'product') {
                            data.push(group.reduce((sum, item) => sum + item.amount, 0));
                        } else {
                            data.push(group.reduce((sum, transaction) => sum + transaction.grand_total, 0));
                        }
                    });

                    // Update chart
                    chart.data.labels = labels;
                    chart.data.datasets[0].data = data;
                    chart.update();
                }

                // Initial chart update
                updateChart();

                // Update chart when Livewire updates
                Livewire.on('updated', () => {
                    updateChart();
                });
            });
        </script>
    @endpush
</div>
