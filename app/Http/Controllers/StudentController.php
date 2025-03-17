<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all(); 
        return view('students.index', compact('students'));
    }

    public function store(Request $request)
    {
         
        $request->validate([
            'name' => 'required|string|max:255', 
            'major' => 'required|string|max:255',
            'age' => 'required|integer|min:18',
        ]);

        
        $student = new Student();
        $student->user_id = Auth::id(); 
        $student->name = $request->name;  
        $student->major = $request->major;
        $student->age = $request->age;
        $student->save(); 

        return redirect()->route('students.index'); 
    }
}
