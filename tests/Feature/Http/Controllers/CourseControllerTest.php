<?php

namespace Tests\Feature\Http\Controllers;

use Tests\Feature\FeatureBaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use App\Enums\Role;

class CourseControllerTest extends FeatureBaseTestCase
{
    public $courses;
    public $instructors;
    public $students;

    public function setUp(): void
    {
        parent::setUp();

        $this->courses = [
            Course::factory()->create(),
            Course::factory()->create(),
            Course::factory()->create(),
            Course::factory()->create(['start_at' => now()->addDay(), 'end_at' => now()->addMonth()]), // 対象外
            Course::factory()->create(['start_at' => now()->subMonth(), 'end_at' => now()->subDay()]), // 対象外
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

        CourseUser::factory()->create(['course_id' => $this->courses[0]->id, 'user_id' => $this->instructors[0]->id, 'user_type' => 'instructor']);
        CourseUser::factory()->create(['course_id' => $this->courses[1]->id, 'user_id' => $this->instructors[1]->id, 'user_type' => 'instructor']);
        CourseUser::factory()->create(['course_id' => $this->courses[2]->id, 'user_id' => $this->instructors[1]->id, 'user_type' => 'instructor']);
        CourseUser::factory()->create(['course_id' => $this->courses[3]->id, 'user_id' => $this->instructors[1]->id, 'user_type' => 'instructor']);
        CourseUser::factory()->create(['course_id' => $this->courses[0]->id, 'user_id' => $this->students[0]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[0]->id, 'user_id' => $this->students[1]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[1]->id, 'user_id' => $this->students[2]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[2]->id, 'user_id' => $this->students[0]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[3]->id, 'user_id' => $this->students[0]->id, 'user_type' => 'student']);
        CourseUser::factory()->create(['course_id' => $this->courses[4]->id, 'user_id' => $this->students[1]->id, 'user_type' => 'student']);
    }

    public function test_index(): void
    {
        $response = $this->withHeaders($this->getHeaders())->get('/api/courses');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->json());
        $results = $response->json();
        $this->assertEquals(array_column($results, 'id'), [$this->courses[0]->id, $this->courses[1]->id, $this->courses[2]->id]);
    }

    public function test_index_with_instructor_user(): void
    {
        $response = $this->withHeaders($this->getHeadersByUser($this->instructors[1]))->get('/api/courses');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json());
        $results = $response->json();
        $this->assertEquals(array_column($results, 'id'), [$this->courses[1]->id, $this->courses[2]->id]);
        $instructors = array_column($results, 'instructors');
        $this->assertEquals(array_column($instructors[0], 'name'), ['講師二郎']);
        $this->assertEquals(array_column($instructors[1], 'name'), ['講師二郎']);
        $students = array_column($results, 'students');
        $this->assertEquals(array_column($students[0], 'name'), ['生徒三郎']);
        $this->assertEquals(array_column($students[1], 'name'), ['生徒太郎']);
    }

    public function test_index_with_student_user(): void
    {
        $response = $this->withHeaders($this->getHeadersByUser($this->students[0]))->get('/api/courses');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json());
        $results = $response->json();
        $this->assertEquals(array_column($results, 'id'), [$this->courses[0]->id, $this->courses[2]->id]);
        $instructors = array_column($results, 'instructors');
        $this->assertEquals(array_column($instructors[0], 'name'), ['講師太郎']);
        $this->assertEquals(array_column($instructors[1], 'name'), ['講師二郎']);
        $this->assertEquals(array_column($results, 'students'), []);
    }
}
