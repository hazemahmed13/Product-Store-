<form action="{{ route('courses.save', $course->id) }}" method="POST">
    @csrf
    @foreach($errors->all() as $error)
    <div class="alert alert-danger">
        <strong>Error!</strong> {{$error}}
    </div>
    @endforeach

    <div class="row mb-2">
        <div class="col">
            <label for="name" class="form-label">Course Name:</label>
            <input type="text" class="form-control" name="name" required value="{{ old('name', $course->name) }}">
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" name="description" required>{{ old('description', $course->description) }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Course</button>
</form>