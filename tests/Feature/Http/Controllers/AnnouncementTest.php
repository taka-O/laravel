<?php

namespace Tests\Feature\Http\Controllers;

use Tests\Feature\FeatureBaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\Models\Announcement;
use App\Models\AnnouncementCourse;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use App\Enums\Role;

class AnnouncementTest extends FeatureBaseTestCase
{
    public $announcements;
    public $courses;
    public $instructors;
    public $students;

    public function setUp(): void
    {
        parent::setUp();

        $this->announcements = [
            Announcement::factory()->create(['category' => 0]),
            Announcement::factory()->create(['category' => 1]),
            Announcement::factory()->create(['category' => 1]),
            Announcement::factory()->create(['category' => 0, 'start_at' => now()->addMonth()]), // 対象外
            Announcement::factory()->create(['category' => 1, 'end_at' => now()->subMonth()]), // 対象外
        ];

        $this->courses = [
            Course::factory()->create(),
            Course::factory()->create(),
        ];

        $this->instructors = [
            User::factory()->create(['name' => '講師太郎', 'role_type' => Role::instructor->value]),
            User::factory()->create(['name' => '講師二郎', 'role_type' => Role::instructor->value]),
        ];

        $this->students = [
            User::factory()->create(['name' => '生徒太郎', 'role_type' => Role::student->value]),
            User::factory()->create(['name' => '生徒二郎', 'role_type' => Role::student->value]),
            User::factory()->create(['name' => '生徒三郎', 'role_type' => Role::student->value]),
        ];

        AnnouncementCourse::factory()->create(['announcement_id' => $this->announcements[1]->id, 'course_id' => $this->courses[0]->id]);
        AnnouncementCourse::factory()->create(['announcement_id' => $this->announcements[2]->id, 'course_id' => $this->courses[1]->id]);
        AnnouncementCourse::factory()->create(['announcement_id' => $this->announcements[4]->id, 'course_id' => $this->courses[1]->id]);

        CourseUser::factory()->create(['course_id' => $this->courses[0]->id, 'user_id' => $this->instructors[0]->id, 'user_type' => 'instructor']);
        CourseUser::factory()->create(['course_id' => $this->courses[1]->id, 'user_id' => $this->instructors[1]->id, 'user_type' => 'instructor']);
        CourseUser::factory()->create(['course_id' => $this->courses[0]->id, 'user_id' => $this->students[0]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[0]->id, 'user_id' => $this->students[1]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[1]->id, 'user_id' => $this->students[2]->id, 'user_type' => 'student']);
    }

    public function test_index(): void
    {
        $response = $this->withHeaders($this->getHeaders())->get('/api/announcements');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->json());
    }

    public function test_index_with_instructor_user(): void
    {
        $response = $this->withHeaders($this->getHeadersByUser($this->instructors[1]))->get('/api/announcements');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json());
        $results = $response->json();
        $this->assertEquals(array_column($results, 'id'), [$this->announcements[2]->id, $this->announcements[0]->id]);
    }

    public function test_index_with_student_user(): void
    {
        $response = $this->withHeaders($this->getHeadersByUser($this->students[0]))->get('/api/announcements');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json());
        $results = $response->json();
        $this->assertEquals(array_column($results, 'id'), [$this->announcements[1]->id, $this->announcements[0]->id]);
    }
}
