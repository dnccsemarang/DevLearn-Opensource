<?php

namespace App\Services\Author;

use App\Http\Resources\DashboardResource;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserCourse;
use Illuminate\Support\Facades\DB;

class AuthorService
{
    function dashboard()
    {
        // course
        $course = Course::where('author_id', 10)->get();

        // jumlah lesson
        $lesson = [];
        $lesson_count = 0;
        foreach ($course as $findId) {
            array_push($lesson, Lesson::where('course_id', $findId->id)->get());
        }
        foreach ($lesson as $count) {
            $lesson_count += count($count);
        }

        // member
        $member = [];
        $member_count = 0;
        foreach ($course as $findId) {
            array_push($member, UserCourse::where('course_id', $findId->id)->get());
        }
        foreach ($member as $count) {
            $member_count += count($count);
        }

        //penghasilan bulan ini
        $buyerThisMonth = [];
        $courseBought = [];
        $income = 0;
        foreach ($course as $findId) {
            array_push(
                $buyerThisMonth,
                UserCourse::whereMonth('created_at', date('n'))
                    ->where('course_id', $findId->id)
                    ->get()
            );
        }
        $buyerThisMonth = collect($buyerThisMonth)->flatten()->toArray();
        foreach ($buyerThisMonth as $findCourseId) {
            $id = $findCourseId['course_id'];
            array_push($courseBought, Course::where('id', $id)
                ->get());
        }

        foreach ($courseBought as $findPrice) {
            $income += $findPrice[0]['price'];
        }

        $topBought = UserCourse::select('course_id', DB::raw('COUNT(*) as total'))
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $courseIds = $topBought->pluck('course_id');

        $topBought = Course::whereIn('id', $courseIds)->where('author_id', 10)->get();

        $topPass = Certificate::select('course_id', DB::raw('COUNT(*) as total'))
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $courseIds = $topPass->pluck('course_id');

        $topPass = Course::whereIn('id', $courseIds)->where('author_id', 10)->get();

        $data = [
            "course" => $course,
            "topBought" => collect($topBought),
            "topPass" => $topPass,
            "lesson_count" => $lesson_count,
            "member_count" => $member_count,
            "income" => $income
        ];

        return $data;
    }
}
