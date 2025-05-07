<div>
    <!-- Filters -->
    <div class="row mb-4 g-2">
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Search product..."
                   wire:model.live.debounce.300ms="searchTerm">
        </div>
        <div class="col-md-2">
            <select class="form-select" wire:model.live="outletFilter">
                <option value="">All Outlets</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" wire:model.live="actionType">
                <option value="">All Actions</option>
                @foreach($actionTypes as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" wire:model="dateFrom" placeholder="From Date">
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" wire:model="dateTo" placeholder="To Date">
        </div>
        <div class="col-md-1">
            <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                <i class="fas fa-filter-circle-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Export Button -->
    {{-- <div class="mb-3">
        <button class="btn btn-success" wire:click="exportExcel">
            <i class="fas fa-file-excel me-1"></i> Export to Excel
        </button>
    </div> --}}

    <!-- Logs Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Outlet</th>
                    <th class="text-end">Qty Change</th>
                    <th class="text-end">Remaining</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $log->inventory->product->name }}</td>
                        <td>{{ $log->inventory->outlet->name }}</td>
                        <td class="text-end {{ $log->quantity > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                        </td>
                        <td class="text-end">{{ $log->remaining_stock }}</td>
                        <td>{{ ucfirst($log->action) }}</td>
                        <td>{{ $log->user->name }}</td>
                        <td>{{ $log->reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No inventory logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>