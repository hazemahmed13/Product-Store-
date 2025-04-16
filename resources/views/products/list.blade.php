@extends('layouts.master')

@section('title', 'Products List')

@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    <div class="col col-2">
        @can('add_products')
        <a href="{{ route('products_edit') }}" class="btn btn-success form-control">Add Product</a>
        @endcan
    </div>
</div>

@if(auth()->user())
    <div class="alert alert-info">
        Your Credit: ${{ auth()->user()->credit }}
    </div>
@endif

<!-- Search and Filter Form -->
<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="number" class="form-control" placeholder="Min Price" value="{{ request()->min_price }}" />
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="number" class="form-control" placeholder="Max Price" value="{{ request()->max_price }}" />
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" disabled>Order By</option>
                <option value="name">Name</option>
                <option value="price">Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" disabled>Order Direction</option>
                <option value="ASC">ASC</option>
                <option value="DESC">DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>

<!-- Products List -->
@foreach($products as $product)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col col-sm-12 col-lg-4">
                    <img src="{{ asset("images/$product->photo") }}" class="img-thumbnail" alt="{{ $product->name }}" width="100%">
                </div>
                <div class="col col-sm-12 col-lg-8 mt-3">
                    <h3>{{ $product->name }}</h3>
                    <p>Your Credit: ${{ auth()->user()->credit ?? 0 }}</p>

                    <table class="table table-striped">
                        <tr><th>Name</th><td>{{ $product->name }}</td></tr>
                        <tr><th>Model</th><td>{{ $product->model }}</td></tr>
                        <tr><th>Code</th><td>{{ $product->code }}</td></tr>
                        <tr><th>Price</th><td>${{ $product->price }}</td></tr>
                        <tr><th>Stock</th><td>{{ $product->stock }}</td></tr>
                        <tr><th>Description</th><td>{{ $product->description }}</td></tr>
                    </table>

                    <div class="row mt-2">
                        <div class="col col-4">
                            @if(auth()->user() && auth()->user()->credit >= $product->price && $product->stock > 0)
                                <form action="{{ route('products_purchase', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary form-control">Buy</button>
                                </form>
                            @else
                                <button class="btn btn-secondary form-control" disabled>
                                    @if(!auth()->user())
                                        Login to Purchase
                                    @elseif($product->stock <= 0)
                                        Out of Stock
                                    @else
                                        Insufficient Credit
                                    @endif
                                </button>
                            @endif
                        </div>

                        <!-- Edit Button -->
                        @can('edit_products')
                        <div class="col col-4">
                            <a href="{{ route('products_edit', $product->id) }}" class="btn btn-warning form-control">Edit</a>
                        </div>
                        @endcan

                        <!-- Delete Button -->
                        @can('delete_products')
                        <div class="col col-4">
                            <form action="{{ route('products_delete', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger form-control">Delete</button>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
