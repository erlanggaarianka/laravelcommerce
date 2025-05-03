<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Accounts</h5>
            <a href="{{ route('account.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Register Account
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="accounts-table" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $account->role === 'Owner' ? 'primary' : 'success' }}">
                                        {{ $account->role }}
                                    </span>
                                </td>
                                <td>{{ $account->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('account.edit', $account->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $account->id }})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user account?</p>
                    <div class="alert alert-warning">
                        <strong>Name:</strong> {{ $deleteName ?? '' }}<br>
                        <strong>Email:</strong> {{ $deleteEmail ?? '' }}<br>
                        <strong>Role:</strong> {{ $deleteRole ?? '' }}
                    </div>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser">Delete Account</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                let dataTable = null;

                function initializeDataTable() {
                    if ($.fn.DataTable.isDataTable('#accounts-table')) {
                        $('#accounts-table').DataTable().destroy();
                    }

                    dataTable = $('#accounts-table').DataTable({
                        responsive: true,
                        columnDefs: [
                            { orderable: false, targets: [4] } // Disable sorting for actions column
                        ],
                        initComplete: function() {
                            $('.dataTables_filter input').addClass('form-control');
                            $('.dataTables_length select').addClass('form-select');
                        }
                    });
                }

                // Initial initialization
                initializeDataTable();

                // Listen for Livewire events
                Livewire.on('initializeDataTable', () => {
                    initializeDataTable();
                });

                // Modal controls
                Livewire.on('showDeleteModal', () => {
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    modal.show();

                    // Reinitialize DataTable after modal shows
                    setTimeout(initializeDataTable, 100);
                });

                Livewire.on('hideDeleteModal', () => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();

                    // Reinitialize DataTable after modal hides
                    setTimeout(initializeDataTable, 100);
                });
            });
        </script>
    @endpush
</div>
