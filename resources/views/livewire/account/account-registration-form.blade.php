<div>
    <form wire:submit.prevent="confirmSave">
        <div class="row gap-2">
            <div class="col-md-4 col-lg-12">
                <label for="validationCustomName" class="form-label">Name</label>
                <div class="input-group has-validation">
                    <input wire:loading.attr='disabled' wire:model.defer='name' type="text" class="form-control" id="validationCustomName">
                </div>
                @error('name')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-4 col-lg-12">
                <label for="validationCustomEmail" class="form-label">Email</label>
                <div class="input-group has-validation">
                    <input wire:loading.attr='disabled' wire:model.defer='email' type="email" class="form-control" id="validationCustomEmail"
                           {{ $id ? 'readonly' : '' }}>
                </div>
                @error('email')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-4 col-lg-12">
                <label for="validationCustomRole" class="form-label">Role</label>
                <div class="input-group has-validation">
                    <select wire:model.defer='role' wire:loading.attr='disabled' class="form-select" id="validationCustomRole">
                        <option value="">Select a role</option>
                        <option value="Cashier">Cashier</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                @error('role')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-4 col-lg-12" x-data="{ showOutletField: @entangle('role') }"
                 x-show="showOutletField === 'Cashier'">
                <label for="validationCustomOutlet" class="form-label">Outlet</label>
                <div class="input-group has-validation">
                    <select wire:model.defer='outlet_id' wire:loading.attr='disabled'
                            class="form-select" id="validationCustomOutlet">
                        <option value="">Select an outlet</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('outlet_id')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-4 col-lg-12">
                <label for="validationCustomPassword" class="form-label">Password</label>
                <div class="input-group has-validation">
                    <input wire:loading.attr='disabled' wire:model.defer='password' type="password"
                           class="form-control" id="validationCustomPassword"
                           placeholder="{{ $id ? 'Leave blank to keep current password' : '' }}">
                </div>
                @error('password')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            @unless($id)
            <div class="col-md-4 col-lg-12">
                <label for="validationCustomPasswordConfirmation" class="form-label">Confirm Password</label>
                <div class="input-group has-validation">
                    <input wire:loading.attr='disabled' wire:model.defer='password_confirmation' type="password"
                           class="form-control" id="validationCustomPasswordConfirmation">
                </div>
            </div>
            @endunless
        </div>

        <div class="d-flex justify-content-end align-items-center mt-2">
            <button wire:loading.attr='disabled' class="btn btn-primary" type="submit">
                <span wire:loading.remove>{{ $id ? 'Update' : 'Register' }}</span>
                <span wire:loading>Processing...</span>
            </button>
            <a href="{{ route('account.list') }}" class="btn btn-secondary ms-2">Cancel</a>
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
                        <strong>Name:</strong> {{ $name }}<br>
                        <strong>Email:</strong> {{ $email }}<br>
                        <strong>Role:</strong> {{ $role }}<br>
                        @if($role === 'Cashier' && $outlet_id)
                            <strong>Outlet:</strong> {{ $outlets->firstWhere('id', $outlet_id)->name ?? 'N/A' }}
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
