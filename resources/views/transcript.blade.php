@extends('layouts.master')

@section('title', 'Student Transcript')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h3>Student Transcript</h3>
            </div>
            <div class="card-body">
                
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($courses as $course)
                            <tr>
                                <td>{{ $course['course'] }}</td>
                                <td>{{ $course['grade'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection