@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 py-2">
            <h2>Welcome, <b>{{ Auth::user()->name }}</b></h2>
            <h4>Let's start your business journey with <b>{{ config('app.name', 'GOOD CREDIT') }}</b></h4>
        </div>
    </div>
</div>
@endsection
