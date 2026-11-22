<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_crud_flow(): void
    {
        $response = $this->post(route('students.store'), [
            'name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'phone' => '123456789',
            'address' => 'London', 'date_of_birth' => '1815-12-10',
        ]);
        $student = Student::firstOrFail();
        $response->assertRedirect(route('students.index'));
        $this->get(route('students.show', $student))->assertOk()->assertSee('Ada Lovelace');
        $this->put(route('students.update', $student), ['name' => 'Ada Byron', 'email' => 'ada@example.com', 'phone' => '987654321'])->assertRedirect(route('students.show', $student));
        $this->delete(route('students.destroy', $student))->assertRedirect(route('students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_course_crud_flow_and_validation(): void
    {
        $this->from(route('courses.create'))->post(route('courses.store'), ['name' => '', 'duration' => 0, 'fee' => -1, 'difficulty' => 'Unknown'])->assertRedirect(route('courses.create'))->assertSessionHasErrors(['name', 'duration', 'fee', 'difficulty']);
        $this->post(route('courses.store'), ['name' => 'Laravel Basics', 'description' => 'Foundations', 'duration' => 6, 'fee' => 250, 'difficulty' => 'Easy', 'is_active' => 1])->assertRedirect(route('courses.index'));
        $course = Course::firstOrFail();
        $this->get(route('courses.show', $course))->assertOk()->assertSee('Laravel Basics');
        $this->put(route('courses.update', $course), ['name' => 'Advanced Laravel', 'duration' => 8, 'fee' => 350, 'difficulty' => 'Hard', 'is_active' => 1])->assertRedirect(route('courses.show', $course));
        $this->delete(route('courses.destroy', $course))->assertRedirect(route('courses.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
