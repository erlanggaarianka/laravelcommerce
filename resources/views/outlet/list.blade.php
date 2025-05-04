@extends('layouts.app')

@section('title', 'Outlet List')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 py-2">
            <h2>Outlet List</h2>
            <h4>Manage available outlets in your business</h4>
            <hr>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <livewire:outlet.outlet-list />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
