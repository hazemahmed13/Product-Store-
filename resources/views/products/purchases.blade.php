@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <h2>My Purchases</h2>
    
    @if($purchases->isEmpty())
        <div class="alert alert-info">
            You haven't made any purchases yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->product->name }}</td>
                            <td>${{ number_format($purchase->product->price, 2) }}</td>
                            <td>{{ $purchase->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection 