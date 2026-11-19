<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:published,draft'],
        ]);
        $query = Course::query()->withCount('lessons')->latest();

        if (! empty($filters['q'])) {
            $query->where('title', 'like', '%'.$filters['q'].'%');
        }

        if (($filters['status'] ?? null) === 'published') {
            $query->where('is_published', true);
        } elseif (($filters['status'] ?? null) === 'draft') {
            $query->where('is_published', false);
        }

        return view('admin.courses.index', [
            'courses' => $query->paginate(12)->withQueryString(),
            'totalCourses' => Course::count(),
            'publishedCourses' => Course::where('is_published', true)->count(),
            'videoCount' => \App\Models\Lesson::where('video_status', 'ready')->count(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.form', ['course' => new Course()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $course = Course::create($data);

        return redirect()->route('admin.courses.lessons.index', $course)->with('success', 'Course berhasil dibuat.');
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.form', ['course' => $course->load('lessons')]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $course->update($this->validated($request));

        return redirect()->route('admin.courses.index')->with('success', 'Course berhasil diperbarui.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        foreach ($course->lessons()->whereNotNull('video_path')->get(['video_path']) as $lesson) {
            Storage::disk('private')->delete($lesson->video_path);
        }
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course dan lesson terkait berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'is_published' => ['required', 'boolean'],
        ]);
    }
}
