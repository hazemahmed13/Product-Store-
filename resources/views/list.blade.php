@extends('layouts.master')
@section('title', 'Prime Numbers')
@section('content')
 <h3>{{$product->name}}</h3>
 <div class="row mt-2">
    <div class="col col-10">
    <h1>Products</h1>
    </div>
    <div class="col col-2">
    @auth
    <a href="{{route('products_edit')}}" class="btn btn-success form-control">Add Product</a>
    @endauth
    </div>
   </div>
@endsection
