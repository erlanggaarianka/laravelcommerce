@extends('layouts.app')

@section('title', 'Transaction List')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Transaction</h2>
                <h4>Track your business transactions</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <livewire:transaction.transaction-list />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
