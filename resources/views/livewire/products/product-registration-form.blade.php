<div>
    <form wire:submit.prevent="confirmSave">
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <!-- Product Image -->
                <div class="mb-3">
                    <label for="productImage" class="form-label">Product Image</label>
                    <div class="d-flex align-items-center gap-3">
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: contain;">
                        @elseif($tempImage)
                            <img src="{{ asset('storage/'.$tempImage) }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: contain;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-camera text-muted fa-2x"></i>
                            </div>
                        @endif
                        <input type="file" wire:model="image" class="form-control" id="productImage">
                    </div>
                    @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                    <div class="form-text">Max 2MB. JPG, PNG, or GIF.</div>
                </div>

                <!-- Basic Info -->
                <div class="mb-3">
                    <label for="productName" class="form-label">Product Name *</label>
                    <input type="text" wire:model.defer="name" class="form-control" id="productName">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="productBarcode" class="form-label">Barcode</label>
                    <input type="text" wire:model.defer="barcode" class="form-control" id="productBarcode">
                    @error('barcode') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="productDescription" class="form-label">Description</label>
                    <textarea wire:model.defer="description" class="form-control" id="productDescription" rows="3"></textarea>
                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <!-- Pricing -->
                <div class="mb-3">
                    <label for="productPrice" class="form-label">Selling Price *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" wire:model.defer="price" class="form-control" id="productPrice">
                    </div>
                    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="productCost" class="form-label">Cost Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" wire:model.defer="cost_price" class="form-control" id="productCost">
                    </div>
                    @error('cost_price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Inventory -->
                <div class="mb-3">
                    <label for="productMinStock" class="form-label">Minimum Stock Level *</label>
                    <input type="number" wire:model.defer="minimum_stock" class="form-control" id="productMinStock">
                    @error('minimum_stock') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" wire:model.defer="is_active" id="productStatus">
                        <label class="form-check-label" for="productStatus">Active Product</label>
                    </div>
                    @error('is_active') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Outlet Assignment -->
                <div class="mb-3">
                    <label class="form-label">Assign to Outlets *</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @foreach($outlets as $outlet)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       wire:model="selectedOutlets"
                                       value="{{ $outlet->id }}"
                                       id="outlet{{ $outlet->id }}">
                                <label class="form-check-label" for="outlet{{ $outlet->id }}">
                                    {{ $outlet->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedOutlets') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('products.list') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <span wire:loading.remove>{{ $id ? 'Update' : 'Create' }}</span>
                <span wire:loading>Processing...</span>
            </button>
        </div>
    </form>

    <!-- Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true" wire:model="showConfirmationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('showConfirmationModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>{{ $confirmationMessage }}</p>
                    <div class="alert alert-info mt-3">
                        <div class="text-center mb-3">
                            @if($image)
                                <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 150px;">
                            @elseif($tempImage)
                                <img src="{{ Storage::url($tempImage) }}" class="img-thumbnail" style="max-height: 150px;">
                            @endif
                        </div>
                        <strong>Name:</strong> {{ $name }}<br>
                        <strong>Price:</strong> ${{ number_format($price, 2) }}<br>
                        <strong>Outlets:</strong> {{ count($selectedOutlets) }} selected<br>
                        <strong>Status:</strong> {{ $is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="$set('showConfirmationModal', false)">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('showConfirmation', () => {
                    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    modal.show();
                });

                Livewire.on('hideConfirmation', () => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                    modal?.hide();
                });
            });
        </script>
    @endpush
</div>
