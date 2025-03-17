@extends('layouts.master')

@section('title', 'Even Numbers')

@section('content')
<h2>Add New Student</h2>
<form method="POST" action="{{ route('students.store') }}">
    @csrf
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="major">Major</label>
        <input type="text" class="form-control" id="major" name="major" required>
    </div>
    <div class="form-group">
        <label for="age">Age</label>
        <input type="number" class="form-control" id="age" name="age" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Student</button>
</form>

<div class="container mt-4">
    <h2>Student Table</h2>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Name</th>
                <th>Major</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
    <tr>
        <td>
            @if($student->user)
                {{ $student->user->name }}
            @else
                No User
            @endif
        </td>
        <td>{{ $student->major }}</td>
        <td>{{ $student->age }}</td>
    </tr>
@endforeach
        </tbody>
    </table>
</div>

@endsection

