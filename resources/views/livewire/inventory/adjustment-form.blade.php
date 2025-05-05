<div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Product</label>
            <input type="text" class="form-control" value="{{ $product->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Outlet</label>
            <input type="text" class="form-control" value="{{ $outlet->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Stock</label>
            <input type="text" class="form-control" value="{{ $currentStock }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Adjustment Type *</label>
            <select class="form-select" wire:model="adjustmentType">
                <option value="add">Add Stock</option>
                <option value="remove">Remove Stock</option>
            </select>
            @error('adjustmentType') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity *</label>
            <input type="number" class="form-control" wire:model="quantity">
            @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Reason *</label>
            <textarea class="form-control" rows="3" wire:model="reason"></textarea>
            @error('reason') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" wire:loading.attr='disabled' wire:click="save">Save Adjustment</button>
    </div>
</div>
