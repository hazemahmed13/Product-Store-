@extends('layouts.master')
@section('title', 'Courses List')
@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Courses</h1>
    </div>
    <div class="col col-2">
        @can('add_courses')
        <a href="{{ route('courses_edit') }}" class="btn btn-success form-control">Add Course</a>
        @endcan
    </div>
</div>
<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="numeric" class="form-control" placeholder="Min Price" value="{{ request()->min_price }}" />
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="numeric" class="form-control" placeholder="Max Price" value="{{ request()->max_price }}" />
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" {{ request()->order_by == "" ? "selected" : "" }} disabled>Order By</option>
                <option value="title" {{ request()->order_by == "title" ? "selected" : "" }}>Title</option>
                <option value="price" {{ request()->order_by == "price" ? "selected" : "" }}>Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" {{ request()->order_direction == "" ? "selected" : "" }} disabled>Order Direction</option>
                <option value="ASC" {{ request()->order_direction == "ASC" ? "selected" : "" }}>ASC</option>
                <option value="DESC" {{ request()->order_direction == "DESC" ? "selected" : "" }}>DESC</option>
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

@foreach($courses as $course)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col col-sm-12 col-lg-4">
                    <img src="{{ asset('images/' . $course->photo) }}" class="img-thumbnail" alt="{{ $course->title }}" width="100%">
                </div>
                <div class="col col-sm-12 col-lg-8 mt-3">
                    <div class="row mb-2">
                        <div class="col-8">
                            <h3>{{ $course->title }}</h3>
                        </div>
                        <div class="col col-2">
                            @can('edit_courses')
                            <a href="{{ route('courses_edit', $course->id) }}" class="btn btn-success form-control">Edit</a>
                            @endcan
                        </div>
                        <div class="col col-2">
                            @can('delete_courses')
                            <a href="{{ route('courses_delete', $course->id) }}" class="btn btn-danger form-control">Delete</a>
                            @endcan
                        </div>
                    </div>
                    <table class="table table-striped">
                        <tr><th width="20%">Title</th><td>{{ $course->title }}</td></tr>
                        <tr><th>Instructor</th><td>{{ $course->instructor ? $course->instructor->name : 'No Instructor' }}</td></tr>
                        <tr><th>Price</th><td>{{ $course->price }}</td></tr>
                        <tr><th>Description</th><td>{{ $course->description }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection