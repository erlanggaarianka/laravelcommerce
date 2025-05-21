<div>
    <!-- Filters -->
    <div class="row mb-4 g-2">
        <div class="col-md-3">
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
            <div class="col-md-3">
                <input type="date" class="form-control" wire:model.live="customStart">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" wire:model.live="customEnd">
            </div>
        @endif

        <div class="col-md-3">
            <select class="form-select" wire:model.live="outletFilter">
                <option value="">All Outlets</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <h3 class="card-text text-success">{{ $totalSales }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Transactions</h5>
                    <h3 class="card-text text-primary">{{ $totalTransactions }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Avg. Transaction</h5>
                    <h3 class="card-text text-info">{{ $totalTransactions ? $totalSales / $totalTransactions : 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Cashier Performance</h5>
            {{-- <button class="btn btn-sm btn-success" wire:click="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export
            </button> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Cashier
                                @if($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-end" wire:click="sortBy('transaction_count')" style="cursor: pointer;">
                                Transactions
                                @if($sortField === 'transaction_count')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-end" wire:click="sortBy('total_sales')" style="cursor: pointer;">
                                Total Sales
                                @if($sortField === 'total_sales')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-end" wire:click="sortBy('avg_sale')" style="cursor: pointer;">
                                Avg. Sale
                                @if($sortField === 'avg_sale')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cashiers as $cashier)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <span class="avatar-initial bg-primary rounded-circle">
                                                {{ substr($cashier->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $cashier->name }}</h6>
                                            <small class="text-muted">{{ $cashier->outlet->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ $cashier->transaction_count }}</td>
                                <td class="text-end">{{ $cashier->total_sales }}</td>
                                <td class="text-end">{{ $cashier->avg_sale }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $cashiers->links() }}
            </div>
        </div>
    </div>
</div>
