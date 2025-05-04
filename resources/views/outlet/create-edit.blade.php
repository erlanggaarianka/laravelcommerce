@extends('layouts.app')

@section('title', $id === null ? 'Register outlet' : 'Edit Outlet')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>{{ $id === null ? 'Register Outlet' : 'Edit Outlet' }}</h2>
                <h4>{{ $id === null ? 'Register' : 'Edit' }} an outlet into your business</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:outlet.outlet-registration-form :id="$id ?? null" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
