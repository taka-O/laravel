<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse {
        $now = now();
        $user = auth()->user();
        $announcementQuery = Announcement::query()
                                ->where('start_at', '<=', $now)
                                ->where('end_at', '>=', $now);
         if (!$user->isAdmin()) {
            $announcementQuery = $announcementQuery
                                    ->where(function ($query) use ($user) {
                                        $query->whereIn('id',
                                            Announcement::select('announcements.id')
                                                ->join('announcement_courses', 'announcements.id', '=', 'announcement_courses.announcement_id')
                                                ->join('courses', 'announcement_courses.course_id', '=', 'courses.id')
                                                ->join('course_users', 'courses.id', '=', 'course_users.course_id')
                                                ->where('announcements.category', "!=", '0')
                                                ->where('course_users.user_id', "=", $user->id)
                                        )
                                        ->orWhere('category', "=", '0');
                                    });
        }

        $announcements = $announcementQuery->orderBy('start_at', 'desc')->get();
        return response()->json($announcements);
    }
}
