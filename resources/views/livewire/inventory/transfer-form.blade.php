<div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Product</label>
            <input type="text" class="form-control" value="{{ $product->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">From Outlet</label>
            <input type="text" class="form-control" value="{{ $sourceOutlet->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Stock</label>
            <input type="text" class="form-control" value="{{ $currentStock }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">To Outlet *</label>
            <select class="form-select" wire:model="destinationOutletId">
                <option value="">Select Destination Outlet</option>
                @foreach($outlets as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
            @error('destinationOutletId') <span class="text-danger">{{ $message }}</span> @enderror
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
        <button type="button" class="btn btn-primary" wire:loading.attr='disabled' wire:click="save">Complete Transfer</button>
    </div>
</div>
