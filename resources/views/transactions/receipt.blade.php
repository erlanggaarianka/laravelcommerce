@extends('layouts.app')

@section('title', 'Transaction Receipt')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Transaction Receipt</h5>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Receipt Header -->
                        <div class="text-center mb-4">
                            <h4>{{ config('app.name') }}</h4>
                            <p class="mb-1">{{ $outlet->address }}</p>
                            <p class="mb-1">Phone: {{ $outlet->phone }}</p>
                            <p class="mb-1">Invoice: {{ $transaction->invoice_number }}</p>
                            <p>Date: {{ $transaction->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <hr>

                        <!-- Transaction Details -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="mb-1"><strong>Cashier:</strong> {{ $transaction->user->name }}</p>
                                <p><strong>Outlet:</strong> {{ $outlet->name }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1"><strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-end">{{ number_format($transaction->total_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tax (10%):</th>
                                        <td class="text-end">{{ number_format($transaction->tax, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Discount:</th>
                                        <td class="text-end">{{ number_format($transaction->discount, 2) }}</td>
                                    </tr>
                                    <tr class="table-active">
                                        <th>Grand Total:</th>
                                        <td class="text-end"><strong>{{ number_format($transaction->grand_total, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Cash Received:</th>
                                        <td class="text-end">{{ number_format($transaction->cash_received, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Change:</th>
                                        <td class="text-end">{{ number_format($transaction->change, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Footer -->
                        <hr>
                        <div class="text-center mt-4">
                            <p class="mb-1">Thank you for your purchase!</p>
                            <p class="mb-1">For returns, please present this receipt</p>
                            <p>{{ config('app.name') }}</p>
                        </div>

                        @if($transaction->notes)
                            <div class="mt-3 p-2 bg-light rounded">
                                <p class="mb-0"><strong>Notes:</strong> {{ $transaction->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('transactions.list') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Transactions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .card, .card * {
                visibility: visible;
            }
            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
@endsection
