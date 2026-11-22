<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View { return view('students.index', ['students' => Student::latest()->paginate(10)]); }
    public function create(): View { return view('students.create', ['student' => new Student]); }
    public function store(Request $request): RedirectResponse { Student::create($this->validated($request)); return to_route('students.index')->with('success', 'Student created successfully.'); }
    public function show(Student $student): View { return view('students.show', compact('student')); }
    public function edit(Student $student): View { return view('students.edit', compact('student')); }
    public function update(Request $request, Student $student): RedirectResponse { $student->update($this->validated($request, $student)); return to_route('students.show', $student)->with('success', 'Student updated successfully.'); }
    public function destroy(Student $student): RedirectResponse { $student->delete(); return to_route('students.index')->with('success', 'Student deleted successfully.'); }
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', Rule::unique('students')->ignore($student)],
            'phone' => ['required', 'string', 'max:20'], 'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
        ]);
    }
}
