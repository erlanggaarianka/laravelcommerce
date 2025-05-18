@extends('layouts.app')

@section('title', $id === null ? 'Register Payment Method' : 'Edit Payment Method')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>{{ $id === null ? 'Register Payment Method' : 'Edit Payment Method' }}</h2>
                <h4>{{ $id === null ? 'Register' : 'Edit' }} an payment method into your business</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:payment-method.payment-method-registration-form :id="$id ?? null" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
