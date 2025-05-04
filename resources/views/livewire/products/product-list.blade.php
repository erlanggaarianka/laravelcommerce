<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Product
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="products-table" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Barcode</th>
                            <th>Price</th>
                            <th>Outlets</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                                            class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-box-open text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->barcode ?? 'N/A' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if($product->outlets->count() > 0)
                                        <span class="badge bg-primary">
                                            {{ $product->outlets->count() }} Outlets
                                        </span>
                                    @else
                                        <span class="badge bg-warning">Not Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $product->id }})" class="btn btn-sm btn-danger">
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
                    <p>Are you sure you want to delete this product?</p>
                    <div class="alert alert-warning">
                        @if($deleteImage)
                        <div class="text-center mb-3">
                            <img src="{{ Storage::url($deleteImage) }}" alt="{{ $deleteName }}"
                                 class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                        <strong>Name:</strong> {{ $deleteName ?? '' }}<br>
                        <strong>Price:</strong> {{ number_format($deletePrice ?? 0, 2) }}<br>
                    </div>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteProduct">Delete Product</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                let dataTable = null;

                function initializeDataTable() {
                    if ($.fn.DataTable.isDataTable('#products-table')) {
                        $('#products-table').DataTable().destroy();
                    }

                    dataTable = $('#products-table').DataTable({
                        responsive: true,
                        columnDefs: [
                            { orderable: false, targets: [0, 6] }, // Disable sorting for image and actions columns
                            { searchable: false, targets: [0, 4, 5, 6] } // Disable search for these columns
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
