@extends('layouts.master')
@section('title', 'Edit Course')
@section('content')

<form action="{{ route('courses_save', $course->id ?? null) }}" method="post">
    @csrf
    @foreach($errors->all() as $error)
    <div class="alert alert-danger">
        <strong>Error!</strong> {{ $error }}
    </div>
    @endforeach
    <div class="row mb-2">
        <div class="col-6">
            <label for="title" class="form-label">Title:</label>
            <input type="text" class="form-control" placeholder="Title" name="title" required value="{{ $course->title ?? '' }}">
        </div>
        <div class="col-6">
            <label for="instructor_id" class="form-label">Instructor:</label>
            <select name="instructor_id" class="form-control" required>
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" {{ isset($course) && $course->instructor_id == $instructor->id ? 'selected' : '' }}>
                        {{ $instructor->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-6">
            <label for="price" class="form-label">Price:</label>
            <input type="number" class="form-control" placeholder="Price" name="price" required value="{{ $course->price ?? '' }}">
        </div>
        <div class="col-6">
            <label for="photo" class="form-label">Photo:</label>
            <input type="text" class="form-control" placeholder="Photo URL" name="photo" required value="{{ $course->photo ?? '' }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" placeholder="Description" name="description" required>{{ $course->description ?? '' }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<!-- عرض الطلاب المسجلين -->
<div class="mt-4">
    <h5>Registered Students:</h5>
    <ul>
        @forelse ($course->students as $student)
            <li>{{ $student->name }}</li>
        @empty
            <li>No students registered.</li>
        @endforelse
    </ul>
</div>

<!-- نموذج تسجيل طالب جديد -->
@can('enroll_students')
    <form action="{{ route('courses_enroll_student', $course->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="student_id">Select Student:</label>
            <select name="student_id" id="student_id" class="form-control" required>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Enroll Student</button>
    </form>
@endcan

@endsection