<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    public function thirdaction () 
    {
   
        $courses = [
            ['course' => 'Math 101', 'grade' => 'A'],
            ['course' => 'History 201', 'grade' => 'B+'],
            ['course' => 'Biology 150', 'grade' => 'A-'],
            ['course' => 'Chemistry 130', 'grade' => 'B'],
            ['course' => 'Physics 120', 'grade' => 'A'],
        ];
     
       
        return view('transcript', compact('courses'));
     }
}
