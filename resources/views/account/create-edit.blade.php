@extends('layouts.app')

@section('title', $id === null ? 'Register Account' : 'Edit Account')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>{{ $id === null ? 'Register Account' : 'Edit Account' }}</h2>
                <h4>{{ $id === null ? 'Register' : 'Edit' }} an account into your business</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:account.account-registration-form :id="$id ?? null" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
