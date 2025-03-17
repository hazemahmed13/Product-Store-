@extends('layouts.master')
@section('title', 'Even Numbers')
@section('content')
              
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                        @foreach ($bill as $item)
                            <tr>
                                <td>{{ $item['item'] }}</td>
                                <td>{{ $item['price'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ $item['price'] * $item['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

               
                <div class="text-end">
                    <h4>Total: 
                        @php
                            $total = 0;
                            foreach ($bill as $item) {
                                $total += $item['price'] * $item['quantity'];
                            }
                            echo number_format($total, 2);
                        @endphp
                    </h4>
                </div>
            </div>
        </div>
    </div>



@endsection