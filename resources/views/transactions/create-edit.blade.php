@extends('layouts.app')

@section('title', 'Adjust Inventory')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Create Transaction</h2>
                <h4>Input outlet transaction here</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:transaction.create-transaction />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
