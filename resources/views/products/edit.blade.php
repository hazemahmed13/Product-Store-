@extends('layouts.master')
@section('title', isset($product->id) ? 'Edit Product' : 'Add Product')
@section('content')
<div class="row mt-2">
    <div class="col-12">
        <h1>{{ isset($product->id) ? 'Edit Product' : 'Add Product' }}</h1>
    </div>
</div>

<form action="{{ isset($product->id) ? route('products_save', $product->id) : route('products_save') }}" method="POST">
    @csrf
    <div class="row mb-2">
        <div class="col">
            <label for="name" class="form-label">Product Name:</label>
            <input type="text" class="form-control" name="name" required value="{{ old('name', $product->name ?? '') }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="model" class="form-label">Model:</label>
            <input type="text" class="form-control" name="model" required value="{{ old('model', $product->model ?? '') }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="code" class="form-label">Code:</label>
            <input type="text" class="form-control" name="code" required value="{{ old('code', $product->code ?? '') }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="price" class="form-label">Price:</label>
            <input type="number" class="form-control" name="price" required value="{{ old('price', $product->price ?? '') }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" class="form-control" name="stock" required value="{{ old('stock', $product->stock ?? '') }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" name="description" required>{{ old('description', $product->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <button type="submit" class="btn btn-primary">Save Product</button>
        </div>
    </div>
</form>
@endsection
