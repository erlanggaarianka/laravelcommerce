@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Inventory</h2>
                <h4>Manage your outlets inventory</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:inventory.inventory-list />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
