@extends('layouts.app')

@section('title', $id === null ? 'Register Transaction Type' : 'Edit Transaction Type')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>{{ $id === null ? 'Register Transaction Type' : 'Edit Transaction Type' }}</h2>
                <h4>{{ $id === null ? 'Register' : 'Edit' }} an transaction type into your business</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:transaction-type.transaction-type-registration-form :id="$id ?? null" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
