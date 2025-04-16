@extends('layouts.master')

@section('title', 'My Purchases')

@section('content')
<h1>My Purchased Products</h1>

@if($purchases->isEmpty())
    <div class="alert alert-warning">You haven't purchased any products yet.</div>
@else
    @foreach($purchases as $purchase)
        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col col-sm-12 col-lg-4">
                        <img src="{{ asset("images/{$purchase->product->photo}") }}" class="img-thumbnail" alt="{{ $purchase->product->name }}" width="100%">
                    </div>
                    <div class="col col-sm-12 col-lg-8">
                        <h3>{{ $purchase->product->name }}</h3>
                        <table class="table table-striped">
                            <tr><th>Model</th><td>{{ $purchase->product->model }}</td></tr>
                            <tr><th>Code</th><td>{{ $purchase->product->code }}</td></tr>
                            <tr><th>Price</th><td>${{ $purchase->product->price }}</td></tr>
                            <tr><th>Description</th><td>{{ $purchase->product->description }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection