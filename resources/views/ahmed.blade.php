@extends('layouts.master')
@section('title', 'Even Numbers')
@section('content')
hellow



@foreach ($names as $name ) 
{{$name}}
@endforeach


<button type="button" class='btn btn-primary'>play</button>
@endsection