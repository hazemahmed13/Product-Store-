@extends('layouts.master')
@section('title', 'Courses')
@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Courses</h1>
    </div>
    @can('edit_courses')
    <div class="col col-2">
        <a href="{{ route('courses.create') }}" class="btn btn-primary">Add Course</a>
    </div>
    @endcan
</div>

<div class="card mt-2">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Description</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>{{ $course->id }}</td>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->description }}</td>
                    <td>
                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info">View</a>
                        @can('edit_courses')
                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-primary">Edit</a>
                        @endcan
                        @can('delete_courses')
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection