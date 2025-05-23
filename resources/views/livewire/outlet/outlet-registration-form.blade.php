<div>
    <form wire:submit.prevent="confirmSave">
        <div class="row g-3"> {{-- Changed gap-2 to g-3 for consistent Bootstrap spacing --}}
            <div class="col-12">
                <label for="validationCustomName" class="form-label">Outlet Name</label>
                <input wire:model.defer='name' type="text" class="form-control @error('name') is-invalid @enderror" id="validationCustomName">
                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="validationCustomAddress" class="form-label">Address</label>
                <textarea wire:model.defer='address' class="form-control @error('address') is-invalid @enderror" id="validationCustomAddress" rows="2"></textarea>
                @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="validationCustomPhone" class="form-label">Phone Number</label>
                <input wire:model.defer='phone' type="text" class="form-control @error('phone') is-invalid @enderror" id="validationCustomPhone">
                @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input wire:model.live='is_tax_enabled' class="form-check-input @error('is_tax_enabled') is-invalid @enderror" type="checkbox" role="switch" id="isTaxEnabledSwitch">
                    <label class="form-check-label" for="isTaxEnabledSwitch">Enable Tax</label>
                </div>
                @error('is_tax_enabled') <div class="text-danger d-block mt-1">{{ $message }}</div> @enderror
            </div>
            @if($is_tax_enabled)
                <div class="col-12">
                    <label for="validationCustomTaxRate" class="form-label">Tax Rate (%)</label>
                    <div class="input-group">
                        <input wire:model.defer='tax_rate' type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror" id="validationCustomTaxRate" placeholder="e.g., 10.00">
                        <span class="input-group-text">%</span>
                    </div>
                    @error('tax_rate') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            @endif
            </div>

        <div class="d-flex justify-content-end align-items-center mt-3">
            <button wire:loading.attr='disabled' class="btn btn-primary" type="submit">
                <span wire:loading.remove>{{ $id ? 'Update' : 'Register' }}</span>
                <span wire:loading wire:target="confirmSave">Processing...</span>
            </button>
            <a href="{{ route('outlet.list') }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>

    <div wire:ignore.self class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ $confirmationMessage }}</p>
                    <div class="alert alert-info mt-3">
                        <strong>Outlet Name:</strong> {{ $name }}<br>
                        <strong>Address:</strong> {{ $address }}<br>
                        @if($phone)
                            <strong>Phone:</strong> {{ $phone }}<br>
                        @endif
                        <strong>Tax Enabled:</strong> {{ $is_tax_enabled ? 'Yes' : 'No' }}<br> @if($is_tax_enabled)
                            <strong>Tax Rate:</strong> {{ number_format($tax_rate ?? 0, 2) }}% <br> @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading wire:target="save">Saving...</span>
                        <span wire:loading.remove wire:target="save">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            const confirmationModalElement = document.getElementById('confirmationModal');
            const confirmationModal = new bootstrap.Modal(confirmationModalElement);

            Livewire.on('showConfirmation', () => {
                confirmationModal.show();
            });

            Livewire.on('hideConfirmation', () => {
                if (confirmationModalElement.classList.contains('show')) {
                     confirmationModal.hide();
                }
            });
        });
    </script>
    @endpush
</div>
