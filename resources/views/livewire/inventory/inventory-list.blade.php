<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventory Management</h5>
            <div>
                <a href="{{ route('inventory.adjust') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Adjust Inventory
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search products..."
                           wire:model.live.debounce.300ms="searchTerm">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="outletFilter">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               id="lowStockFilter" wire:model.live="lowStockFilter">
                        <label class="form-check-label" for="lowStockFilter">
                            Show Low Stock Only
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                        <i class="fas fa-filter-circle-xmark me-1"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Barcode</th>
                            <th>Outlet</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Reorder Level</th>
                            <th class="text-end">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($inventory->product->image)
                                            <img src="{{ asset('storage/'.$inventory->product->image) }}"
                                                 class="img-thumbnail me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <span>{{ $inventory->product->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $inventory->product->barcode ?? 'N/A' }}</td>
                                <td>{{ $inventory->outlet->name }}</td>
                                <td class="text-end">{{ $inventory->quantity }}</td>
                                <td class="text-end">{{ $inventory->reorder_level }}</td>
                                <td class="text-end">
                                    @if($inventory->quantity <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($inventory->quantity <= $inventory->reorder_level)
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary"
                                                wire:click="openAdjustModal({{ $inventory->id }})">
                                            <i class="fas fa-edit"></i> Adjust
                                        </button>
                                        <button class="btn btn-sm btn-info"
                                                wire:click="openTransferModal({{ $inventory->id }})">
                                            <i class="fas fa-truck"></i> Transfer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No inventory records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>

    <!-- Adjustment Modal -->
    @if($showAdjustModal && $selectedInventory)
        <div wire:ignore.self class="modal fade show" tabindex="-1" style="display: block; padding-right: 16px;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Adjust Inventory</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <livewire:inventory.adjustment-form :inventoryId="$selectedInventory->id" />
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Transfer Modal -->
    @if($showTransferModal && $selectedInventory)
        <div wire:ignore.self class="modal fade show" tabindex="-1" style="display: block; padding-right: 16px;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer Inventory</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <livewire:inventory.transfer-form :inventoryId="$selectedInventory->id" />
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
