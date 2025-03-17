@extends('layouts.master')
@section('title', 'Even Numbers')
@section('content')
@php ($j=5)

                                               


    <?php
  function isPrime($number) {
    if($number<=1) return false;
    $i = $number - 1;
    while($i>1) {
     if($number%$i==0) return false;
      $i--;
    }
    return true;
  }
 ?>
 <body>
 <div class="card m-4">
  <div class="card-header">Prime Numbers</div>
  <div class="card-body">
    @foreach (range(1, 100) as $i)
      @if(isPrime($i))
        <span class="badge bg-primary">{{$i}}</span>  
      @else
        <span class="badge bg-secondary">{{$i}}</span>  
      @endif
    @endforeach
  </div>
 </div>
 @endsection