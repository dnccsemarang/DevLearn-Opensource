<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CourseResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Admin\CourseService;
use App\Services\Admin\CourseServices;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CourseController extends Controller
{
    private $coursesService;

    public function __construct(CourseService $coursesService)
    {
        $this->coursesService = $coursesService;
    }

    public function index()
    {
        $coursesData = $this->coursesService->course();
        $courses = new CourseResource($coursesData['courses']);

        return view('admin.courses.index', [
            'courses' => $courses,
        ]);
    }
    public function deleteCourse(Course $course)
    {
        $course->delete();
        return Redirect::back()->with('message', 'Course Deleted');
    }

    public function detailCourse(Course $course)
    {
        return view('admin.courses.detail', compact('course'));
    }

    public function lessonCourseDetail($id, $chapter)
    {
        $courseData = $this->coursesService->getLesson($id, $chapter);

        // Membuat resource dari data yang diambil
        $courseResource = new CourseResource($courseData);

        // Mengirim data ke tampilan
        return view('admin.courses.lesson_detail', [
            'lesson' => $courseResource['lesson'],
            'lesson_detail' => $courseResource['lesson_detail'],
            'course' => $courseResource['course'],
        ]);
    }
}
