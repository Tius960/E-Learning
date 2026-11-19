<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VideoController extends Controller
{
    public function show(Lesson $lesson): BinaryFileResponse
    {
        abort_unless($lesson->video_status === 'ready' && $lesson->video_path, 404);
        abort_unless(
            auth()->user()->role === 'admin' || $lesson->course->is_published,
            403
        );
        abort_unless(Storage::disk('private')->exists($lesson->video_path), 404);

        return response()->file(Storage::disk('private')->path($lesson->video_path), [
            'Content-Type' => Storage::disk('private')->mimeType($lesson->video_path) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline',
        ]);
    }
}
