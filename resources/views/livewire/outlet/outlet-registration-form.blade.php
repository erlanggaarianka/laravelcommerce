<div>
    <form wire:submit.prevent="confirmSave">
        <div class="row gap-2">
            <div class="col-md-6 col-lg-12">
                <label for="validationCustomName" class="form-label">Outlet Name</label>
                <div class="input-group has-validation">
                    <input wire:loading.attr='disabled' wire:model.defer='name'
                           type="text" class="form-control" id="validationCustomName">
                </div>
                @error('name')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 col-lg-12">
                <label for="validationCustomAddress" class="form-label">Address</label>
                <div class="input-group has-validation">
                    <textarea wire:loading.attr='disabled' wire:model.defer='address'
                              class="form-control" id="validationCustomAddress" rows="2"></textarea>
                </div>
                @error('address')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6 col-lg-12">
                <label for="validationCustomPhone" class="form-label">Phone Number</label>
                <div class="input-group has-validation">
                    <input wire:loading.attr='disabled' wire:model.defer='phone'
                           type="text" class="form-control" id="validationCustomPhone">
                </div>
                @error('phone')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center mt-2">
            <button wire:loading.attr='disabled' class="btn btn-primary" type="submit">
                <span wire:loading.remove>{{ $id ? 'Update' : 'Register' }}</span>
                <span wire:loading>Processing...</span>
            </button>
            <a href="{{ route('outlet.list') }}" class="btn btn-secondary ms-2">Cancel</a>
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
                        <strong>Outlet Name:</strong> {{ $name }}<br>
                        <strong>Address:</strong> {{ $address }}<br>
                        @if($phone)
                        <strong>Phone:</strong> {{ $phone }}
                        @endif
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
