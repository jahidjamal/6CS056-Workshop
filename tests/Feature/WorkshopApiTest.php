<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_api_supports_crud_and_returns_resources(): void
    {
        $this->getJson(route('api.students.index'))->assertOk()->assertJsonPath('data', []);

        $this->postJson('/api/students', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '123456789',
            'address' => 'Kathmandu',
            'date_of_birth' => '1990-12-10',
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Ada Lovelace')
            ->assertJsonPath('data.date_of_birth', '1990-12-10');

        $student = Student::query()->firstOrFail();
        $this->getJson("/api/students/{$student->id}")->assertOk()->assertJsonPath('data.email', 'ada@example.com');

        $this->putJson("/api/students/{$student->id}", [
            'name' => 'Ada Byron',
            'email' => 'ada@example.com',
            'phone' => '987654321',
            'address' => 'Lalitpur',
            'date_of_birth' => '1990-12-10',
        ])->assertOk()->assertJsonPath('data.name', 'Ada Byron');

        $this->deleteJson("/api/students/{$student->id}")->assertNoContent();
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_api_returns_json_validation_errors(): void
    {
        $this->postJson('/api/students', [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'phone']);

        $this->postJson('/api/students', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '123456789',
        ])->assertCreated();

        $this->postJson('/api/students', [
            'name' => 'Grace Hopper',
            'email' => 'ada@example.com',
            'phone' => '987654321',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function test_courses_api_supports_crud_and_returns_resources(): void
    {
        $this->getJson('/api/courses')->assertOk()->assertJsonPath('data', []);

        $this->postJson('/api/courses', [
            'name' => 'Laravel Basics',
            'description' => 'Foundations of Laravel',
            'duration' => 6,
            'fee' => 250,
            'difficulty' => 'Easy',
            'is_active' => true,
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Laravel Basics')
            ->assertJsonPath('data.is_active', true);

        $course = Course::query()->firstOrFail();
        $this->getJson("/api/courses/{$course->id}")->assertOk()->assertJsonPath('data.name', 'Laravel Basics');

        $this->putJson("/api/courses/{$course->id}", [
            'name' => 'Advanced Laravel',
            'description' => 'Advanced topics',
            'duration' => 8,
            'fee' => 350,
            'difficulty' => 'Hard',
            'is_active' => false,
        ])->assertOk()->assertJsonPath('data.name', 'Advanced Laravel')
            ->assertJsonPath('data.is_active', false);

        $this->deleteJson("/api/courses/{$course->id}")->assertNoContent();
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_api_returns_json_validation_errors(): void
    {
        $this->postJson('/api/courses', [
            'name' => '',
            'duration' => 0,
            'fee' => -1,
            'difficulty' => 'Unknown',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'duration', 'fee', 'difficulty']);

        $this->postJson('/api/courses', [
            'name' => 'Laravel Basics',
            'duration' => 6,
            'fee' => 250,
            'difficulty' => 'Easy',
        ])->assertCreated();
    }
}
