<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Outlets</h5>
            <a href="{{ route('outlet.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Outlet
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="outlets-table" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Assigned Cashiers</th>
                            <th>Created At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($outlets as $outlet)
                            <tr>
                                <td>{{ $outlet->name }}</td>
                                <td>{{ $outlet->address }}</td>
                                <td>{{ $outlet->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $outlet->cashiers()->count() }}
                                    </span>
                                </td>
                                <td>{{ $outlet->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('outlet.edit', $outlet->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $outlet->id }})" class="btn btn-sm btn-danger">
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
                    <p>Are you sure you want to delete this outlet?</p>
                    <div class="alert alert-warning">
                        <strong>Name:</strong> {{ $deleteName ?? '' }}<br>
                        <strong>Address:</strong> {{ $deleteAddress ?? '' }}
                    </div>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteOutlet">Delete Outlet</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                let dataTable = null;

                function initializeDataTable() {
                    if ($.fn.DataTable.isDataTable('#outlets-table')) {
                        $('#outlets-table').DataTable().destroy();
                    }

                    dataTable = $('#outlets-table').DataTable({
                        responsive: true,
                        columnDefs: [
                            { orderable: false, targets: [5] } // Disable sorting for actions column
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

                Livewire.on('showDeleteModal', () => {
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    modal.show();

                    setTimeout(initializeDataTable, 100);
                });

                Livewire.on('hideDeleteModal', () => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();

                    setTimeout(initializeDataTable, 100);
                });
            });
        </script>
    @endpush
</div>
