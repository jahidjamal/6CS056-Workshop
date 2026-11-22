<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View { return view('courses.index', ['courses' => Course::latest()->paginate(10)]); }
    public function create(): View { return view('courses.create', ['course' => new Course]); }
    public function store(Request $request): RedirectResponse { Course::create($this->validated($request)); return to_route('courses.index')->with('success', 'Course created successfully.'); }
    public function show(Course $course): View { return view('courses.show', compact('course')); }
    public function edit(Course $course): View { return view('courses.edit', compact('course')); }
    public function update(Request $request, Course $course): RedirectResponse { $course->update($this->validated($request)); return to_route('courses.show', $course)->with('success', 'Course updated successfully.'); }
    public function destroy(Course $course): RedirectResponse { $course->delete(); return to_route('courses.index')->with('success', 'Course deleted successfully.'); }
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:2000'],
            'duration' => ['required', 'integer', 'min:1', 'max:520'], 'fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'difficulty' => ['required', 'in:Easy,Medium,Hard'], 'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
