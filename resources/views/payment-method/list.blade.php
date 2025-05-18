@extends('layouts.app')

@section('title', 'Payment Method List')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Payment Method List</h2>
                <h4>Manage available payment method in your business</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:payment-method.payment-method-list />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
