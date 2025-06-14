<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">New Transaction</h5>
        </div>
        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Outlet *</label>
                        <select class="form-select" wire:model.live="outletId" {{ Auth::user()->outlet_id ? 'disabled' : '' }}>
                            @if(Auth::user()->outlet_id)
                                <option value="{{ Auth::user()->outlet_id }}">{{ Auth::user()->outlet->name }}</option>
                            @else
                                <option value="">Select Outlet</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('outletId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transaction Type *</label>
                        <select class="form-select" wire:model="transactionTypeId" required>
                            <option value="">Select Type</option>
                            @foreach(\App\Models\TransactionType::active()->get() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('transactionTypeId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-select" wire:model="paymentMethod">
                            <option value="">Select Payment Method</option>
                            @foreach(\App\Models\PaymentMethod::active()->get() as $method)
                                <option value="{{ $method->code }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                        @error('paymentMethod') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Add Product</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label">Product *</label>
                                <select class="form-select" wire:model.live="productId">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                @error('productId') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Price *</label>
                                <input type="number" class="form-control" wire:model.live="price" step="0.01">
                                @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty *</label>
                                <input type="number" class="form-control" wire:model.live="quantity" min="1">
                                @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Discount</label>
                                <input type="number" class="form-control" wire:model.live="discount" min="0" step="0.01">
                                @error('discount') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" wire:click="addToCart">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if(count($cart) > 0)
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $index => $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td class="text-end">{{ number_format($item['price'], 2) }}</td>
                                        <td class="text-end">{{ $item['quantity'] }}</td>
                                        <td class="text-end">{{ number_format($item['discount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($item['subtotal'], 2) }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="removeFromCart({{ $index }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mb-4">
                            <div class="col-md-5 col-lg-4"> {{-- Adjusted width --}}
                                <table class="table table-sm"> {{-- table-sm for denser table --}}
                                    <tbody>
                                        <tr>
                                            <th>Subtotal</th>
                                            <td class="text-end">{{ number_format($subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                Tax
                                                @if($currentOutletIsTaxEnabled && $currentOutletTaxRate > 0)
                                                    ({{ number_format($currentOutletTaxRate, 2) }}%)
                                                @else
                                                    (0%)
                                                @endif
                                            </th>
                                            <td class="text-end">{{ number_format($tax, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="fw-bold">Grand Total</th>
                                            <td class="text-end fw-bold">{{ number_format($grandTotal, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Cash Received *</label>
                                <input type="number" class="form-control @error('cashReceived') is-invalid @enderror" wire:model.live="cashReceived" min="0" step="0.01" placeholder="0.00">
                                @error('cashReceived') <span class="text-danger invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Change</label>
                                <input type="text" class="form-control" value="{{ number_format($change, 2) }}" readonly>
                            </div>
                             <div class="col-md-4">
                                <label class="form-label">Notes</label>
                                <input type="text" class="form-control @error('notes') is-invalid @enderror" wire:model.defer="notes" placeholder="Optional transaction notes">
                                @error('notes') <span class="text-danger invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            @if(count($cart) > 0)
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save">Complete Transaction</span>
                                    <span wire:loading wire:target="save">Processing...</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-primary" disabled>Complete Transaction</button>
                            @endif
                             <a href="{{ route('transactions.list') }}" class="btn btn-secondary ms-2">Cancel</a> {{-- Assuming route('transactions.list') exists --}}
                        </div>
                    @else {{-- if cart is empty --}}
                        <div class="alert alert-info">
                            No products added to cart yet. Please add products to continue.
                        </div>
                    @endif
            </form>
        </div>
    </div>
</div>
