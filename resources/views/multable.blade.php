@extends('layouts.master')
@section('title', 'Prime Numbers')
@section('content')                                       

@php($j = 5)
 <div class="card m-4 col-sm-6">
  <div class="card-header text-center">{{$j}} Multiplication Table</div>
  <div class="card-body">
    <table class="table table-bordered table-striped text-center">
      <thead>
        <tr>
          <th>Multiplication</th>
          <th>Result</th>
        </tr>
      </thead>
      <tbody>
        @foreach (range(1, 10) as $i)
        <tr>
          <td>{{$i}} * {{$j}}</td>
          <td>{{ $i * $j }}</td>
        </tr>    
        @endforeach
      </tbody>
    </table>
  </div>
 </div> 

@endsection