<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Adjust Inventory</h5>
        </div>
        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Outlet *</label>
                        <select class="form-select" wire:model.live="outletId">
                            <option value="">Select Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outletId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product *</label>
                        <select class="form-select" wire:model.live="productId">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('productId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" value="{{ $currentStock }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Adjustment Type *</label>
                        <select class="form-select" wire:model="adjustmentType">
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                        </select>
                        @error('adjustmentType') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" class="form-control" wire:model="quantity">
                        @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reason *</label>
                        <input type="text" class="form-control" wire:model="reason" placeholder="e.g. New shipment, damaged goods">
                        @error('reason') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('inventory.list') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>Save Adjustment</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
