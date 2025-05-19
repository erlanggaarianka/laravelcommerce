@extends('layouts.app')

@section('title', 'Transaction Type List')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Transaction Type List</h2>
                <h4>Manage available transaction type in your business</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:transaction-type.transaction-type-list />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
