<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'instructor_id',
        'price',
        'description',
        'photo'
    ];

    // علاقة بين الدورة والمدرس
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // علاقة Many-to-Many بين الدورة والطلاب
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
    }
}