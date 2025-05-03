@extends('layouts.app')

@section('title', 'Account List')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 py-2">
            <h2>Account List</h2>
            <h4>Managed connected account to your business</h4>
            <hr>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <livewire:account.account-list />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
