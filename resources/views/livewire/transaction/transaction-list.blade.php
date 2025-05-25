<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Transaction History</h5>
            <div>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> New Transaction
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search invoice number..."
                           wire:model.live.debounce.300ms="searchTerm">
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="outletFilter">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="userFilter">
                        <option value="">All Cashiers</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="paymentMethodFilter">
                        <option value="">All Payment Methods</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}">{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                        <i class="fas fa-filter-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" wire:model.live="dateFrom">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" wire:model.live="dateTo">
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Outlet</th>
                            <th>Cashier</th>
                            <th class="text-end">Items</th>
                            <th class="text-end">Total</th>
                            <th>Type</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->invoice_number }}</td>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $transaction->outlet->name }}</td>
                                <td>{{ $transaction->user->name }}</td>
                                <td class="text-end">{{ $transaction->items->sum('quantity') }}</td>
                                <td class="text-end">{{ number_format($transaction->grand_total, 2) }}</td>
                                <td>{{ $transaction->transactionType->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($transaction->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary"
                                                wire:click="openDetailModal({{ $transaction->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($transaction->status === 'completed')
                                            <button class="btn btn-sm btn-danger"
                                                    wire:click="confirmCancel({{ $transaction->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    @if($showDetailModal && $selectedTransaction)
        <div wire:ignore.self class="modal fade show" tabindex="-1" style="display: block; padding-right: 16px;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transaction Details - {{ $selectedTransaction->invoice_number }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Outlet:</strong> {{ $selectedTransaction->outlet->name }}</p>
                                <p><strong>Cashier:</strong> {{ $selectedTransaction->user->name }}</p>
                                <p><strong>Date:</strong> {{ $selectedTransaction->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Payment Method:</strong>
                                    {{ ucfirst(str_replace('_', ' ', $selectedTransaction->payment_method)) }}
                                </p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-{{ $selectedTransaction->status === 'completed' ? 'success' : ($selectedTransaction->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($selectedTransaction->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Discount</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedTransaction->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                                            <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Subtotal:</th>
                                        <th class="text-end">{{ number_format($selectedTransaction->total_amount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Tax:</th>
                                        <th class="text-end">{{ number_format($selectedTransaction->tax, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Discount:</th>
                                        <th class="text-end">{{ number_format($selectedTransaction->discount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Grand Total:</th>
                                        <th class="text-end">{{ number_format($selectedTransaction->grand_total, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Cash Received:</th>
                                        <th class="text-end">{{ number_format($selectedTransaction->cash_received, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Change:</th>
                                        <th class="text-end">{{ number_format($selectedTransaction->change, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($selectedTransaction->notes)
                            <div class="mt-3">
                                <h6>Notes:</h6>
                                <p>{{ $selectedTransaction->notes }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                        <a href="{{ route('transactions.receipt', $selectedTransaction->id) }}"
                           target="_blank" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Cancel Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="cancelModal" tabindex="-1"
         aria-labelledby="cancelModalLabel" aria-hidden="true" wire:model="showCancelModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel transaction #{{ $selectedTransaction->invoice_number ?? '' }}?</p>
                    <p>This will restore all items to inventory.</p>

                    <div class="form-group mt-3">
                        <label for="cancelReason">Cancellation Reason *</label>
                        <textarea class="form-control" wire:model="cancelReason" rows="3" required></textarea>
                        @error('cancelReason') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            wire:click="closeModal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger"
                            wire:click="cancelTransaction">
                        Confirm Cancellation
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('showCancelModal', () => {
                    const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
                    cancelModal.show();
                });

                Livewire.on('hideCancelModal', () => {
                    const cancelModal = bootstrap.Modal.getInstance(document.getElementById('cancelModal'));
                    cancelModal.hide();
                });
            });
        </script>
    @endpush
</div>
