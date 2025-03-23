<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;

class CoursesController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        // التحقق من تسجيل الدخول باستثناء صفحة عرض الدورات
        $this->middleware('auth:web')->except('list');
    }

    /**
     * عرض قائمة الدورات مع إمكانية البحث والترتيب.
     */
    public function list(Request $request)
    {
        $query = Course::select("courses.*")->with('instructor');
    
        $query->when($request->keywords, function ($q) use ($request) {
            return $q->where("title", "like", "%$request->keywords%");
        });
    
        $query->when($request->min_price, function ($q) use ($request) {
            return $q->where("price", ">=", $request->min_price);
        });
    
        $query->when($request->max_price, function ($q) use ($request) {
            return $q->where("price", "<=", $request->max_price);
        });
    
        $query->when($request->order_by, function ($q) use ($request) {
            return $q->orderBy($request->order_by, $request->order_direction ?? "ASC");
        });
    
        $courses = $query->get();
    
        return view('courses.list', compact('courses'));
    }
    /**
     * عرض نموذج إضافة/تعديل دورة.
     */
    public function edit(Request $request, Course $course = null)
{
    // التحقق من تسجيل الدخول
    if (!auth()->user()) {
        return redirect('/');
    }

    // إذا لم يتم تمرير دورة، قم بإنشاء دورة جديدة
    $course = $course ?? new Course();

    // جلب جميع المدرسين والطلاب
    $instructors = User::where('role', 'instructor')->get();
    $students = User::where('role', 'student')->get();

    // عرض الصفحة مع تمرير البيانات
    return view('courses.edit', compact('course', 'instructors', 'students'));
}

    /**
     * حفظ بيانات الدورة (إضافة/تعديل).
     */
    public function save(Request $request, Course $course = null)
    {
        // التحقق من صحة البيانات المدخلة
        $this->validate($request, [
            'title' => ['required', 'string', 'max:128'],
            'instructor_id' => ['required', 'exists:users,id'], // التأكد من وجود المدرس
            'price' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:1024'],
            'photo' => ['required', 'string', 'max:256'], // يمكن تغيير هذا لرفع ملفات
        ]);

        // إذا لم يتم تمرير دورة، قم بإنشاء دورة جديدة
        $course = $course ?? new Course();
        $course->fill($request->all());
        $course->save();

        // إعادة التوجيه إلى صفحة قائمة الدورات
        return redirect()->route('courses_list');
    }

    /**
     * تسجيل طالب في دورة.
     */
    public function enrollStudent(Request $request, Course $course)
    {
        // التحقق من صلاحية المستخدم لتسجيل الطلاب
        if (!auth()->user()->can('enroll_students')) {
            abort(403, 'Unauthorized action.');
        }

        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // تسجيل الطالب في الدورة
        $course->students()->attach($request->student_id);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->back()->with('success', 'Student enrolled successfully!');
    }

    /**
     * حذف دورة.
     */
    public function delete(Request $request, Course $course)
    {
        // التحقق من صلاحية المستخدم لحذف الدورة
        if (!auth()->user()->hasPermissionTo('delete_courses')) {
            abort(401); // غير مصرح
        }

        // حذف الدورة
        $course->delete();

        // إعادة التوجيه إلى صفحة قائمة الدورات
        return redirect()->route('courses_list');
    }
}