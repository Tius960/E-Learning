<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use League\Flysystem\FilesystemException;

class LessonController extends Controller
{
    public function index(Course $course): View
    {
        return view('admin.lessons.index', ['course' => $course, 'lessons' => $course->lessons()->get()]);
    }

    public function create(Course $course): View
    {
        $lesson = new Lesson(['course_id' => $course->id, 'position' => $course->lessons()->count() + 1]);

        return view('admin.lessons.form', compact('course', 'lesson'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $this->validated($request);
        $data['position'] = $course->lessons()->max('position') + 1;
        $lesson = $course->lessons()->create($data);

        return redirect()->route('admin.lessons.edit', $lesson)->with('success', 'Lesson berhasil dibuat. Video dapat diunggah di halaman ini.');
    }

    public function edit(Lesson $lesson): View
    {
        return view('admin.lessons.form', ['course' => $lesson->course, 'lesson' => $lesson]);
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $lesson->update($this->validated($request));

        return redirect()->route('admin.lessons.edit', $lesson)->with('success', 'Lesson berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $courseId = $lesson->course_id;
        if ($lesson->video_path) {
            Storage::disk('private')->delete($lesson->video_path);
        }
        $lesson->delete();
        $this->normalizePositions($courseId);

        return redirect()->route('admin.courses.lessons.index', $courseId)->with('success', 'Lesson berhasil dihapus.');
    }

    public function move(Request $request, Lesson $lesson): RedirectResponse
    {
        $direction = $request->validate(['direction' => ['required', 'in:up,down']])['direction'];
        $siblings = $lesson->course->lessons()->get();
        $index = $siblings->search(fn (Lesson $item): bool => $item->is($lesson));
        $targetIndex = $index + ($direction === 'up' ? -1 : 1);

        if ($index !== false && isset($siblings[$targetIndex])) {
            $target = $siblings[$targetIndex];
            $currentPosition = $lesson->position;
            $lesson->update(['position' => $target->position]);
            $target->update(['position' => $currentPosition]);
        }

        return back()->with('success', 'Urutan lesson diperbarui.');
    }

    public function uploadVideo(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime,video/x-matroska', 'max:512000'],
        ]);

        $lesson->update(['video_status' => 'uploading']);

        try {
            $directory = "assets/videos/lessons/{$lesson->id}";
            Storage::disk('private')->makeDirectory($directory);

            $path = $validated['video']->store($directory, 'private');
            if (! $path || ! Storage::disk('private')->exists($path) || Storage::disk('private')->size($path) === 0) {
                throw new \RuntimeException('File video tidak berhasil disimpan.');
            }

            $oldPath = $lesson->video_path;
            $lesson->update(['video_path' => $path, 'video_status' => 'ready']);

            if ($oldPath && $oldPath !== $path) {
                try {
                    Storage::disk('private')->delete($oldPath);
                } catch (FilesystemException $exception) {
                    report($exception);
                }
            }

            return response()->json([
                'message' => 'Video berhasil disimpan dan siap diputar.',
                'status' => $lesson->video_status,
                'url' => route('lessons.video.show', $lesson),
            ]);
        } catch (FilesystemException|\RuntimeException $exception) {
            $lesson->update(['video_status' => 'failed']);
            report($exception);

            return response()->json(['message' => 'Video gagal disimpan. Periksa ruang penyimpanan dan coba lagi.', 'status' => 'failed'], 500);
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);
    }

    private function normalizePositions(int $courseId): void
    {
        $this->getCourseLessons($courseId)->each(fn (Lesson $lesson, int $index) => $lesson->update(['position' => $index + 1]));
    }

    private function getCourseLessons(int $courseId)
    {
        return Lesson::where('course_id', $courseId)->orderBy('position')->get();
    }
}
