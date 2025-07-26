<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Course;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse {
        $now = now();
        $user = auth()->user();
        $courseQuery = Course::query()
                            ->selectRaw('courses.*')
                            ->where('start_at', '<=', $now)
                            ->where('end_at', '>=', $now);
        if (!$user->isAdmin()) {
            $courseQuery = $courseQuery
                                ->join('course_users', 'courses.id', '=', 'course_users.course_id')
                                ->where('course_users.user_id', "=", $user->id);
        }

        if ($user->isStudent()) {
            $courseQuery = $courseQuery->with('instructors');
        } else {
            $courseQuery = $courseQuery->with('instructors', 'students');
        }
        $courses = $courseQuery->orderBy('start_at', 'asc')->get();
        return response()->json($courses);
    }
}
