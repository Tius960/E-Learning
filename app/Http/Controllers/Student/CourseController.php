<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.courses.index');
        }

        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $courses = Course::where('is_published', true)
            ->withCount('lessons')
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where('title', 'like', '%'.$term.'%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('student.courses.index', compact('courses', 'filters'));
    }

    public function show(Request $request, Course $course): View
    {
        abort_unless($course->is_published, 404);
        $course->load('lessons');
        $activeLesson = $course->lessons->firstWhere('id', (int) $request->query('lesson'));

        if (! $activeLesson || $activeLesson->video_status !== 'ready') {
            $activeLesson = $course->lessons->first(fn ($lesson) => $lesson->video_status === 'ready');
        }

        return view('student.courses.show', compact('course', 'activeLesson'));
    }
}
