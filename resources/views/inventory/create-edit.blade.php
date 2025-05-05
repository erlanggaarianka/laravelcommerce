@extends('layouts.app')

@section('title', 'Adjust Inventory')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Adjust Inventory</h2>
                <h4>Manage outlets inventory</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:inventory.adjust-inventory />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
