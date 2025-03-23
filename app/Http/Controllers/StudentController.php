<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;

class StudentController extends Controller
{
    // عرض المواد المسجل فيها الطالب
    public function showCourses()
    {
        // التحقق من أن المستخدم طالب
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized action.'); // 403 Forbidden
        }

        // الحصول على الطالب الحالي
        $student = auth()->user();

        // الحصول على المواد المسجل فيها الطالب
        $courses = $student->enrolledCourses;

        return view('student.courses', compact('courses'));
    }
}