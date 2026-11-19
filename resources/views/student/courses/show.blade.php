@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="course-breadcrumb"><a href="{{ route('student.courses.index') }}">← Katalog belajar</a><span>/</span><span>{{ $course->title }}</span></div>
<header class="lesson-page-heading">
    <h1>{{ $activeLesson?->title ?? $course->title }}</h1>
    <p>{{ $course->title }} @if ($activeLesson)<span>·</span> Lesson {{ $course->lessons->search(fn ($lesson) => $lesson->is($activeLesson)) + 1 }} dari {{ $course->lessons->count() }}@endif</p>
</header>
<div class="lesson-view-grid">
    <div class="lesson-main">
        <section class="video-player-shell">
            @if ($activeLesson)
                <video controls preload="none" src="{{ route('lessons.video.show', $activeLesson) }}">Browser Anda tidak mendukung video.</video>
            @else
                <div class="video-empty"><span>Video kelas</span><strong>{{ $course->lessons->isEmpty() ? 'Materi sedang disiapkan.' : 'Video lesson belum tersedia.' }}</strong></div>
            @endif
        </section>
        <section class="lesson-about">
            <h2>Deskripsi</h2>
            <p>{{ $activeLesson?->description ?: $course->description }}</p>
        </section>
    </div>
    <aside class="panel lesson-playlist">
        <div class="playlist-heading"><div><h2>Daftar lesson</h2><p>{{ $course->lessons->count() }} lesson</p></div></div>
        <div class="playlist-items">
            @forelse ($course->lessons as $lesson)
                @if ($lesson->video_status === 'ready')
                    <a class="playlist-item {{ $activeLesson?->is($lesson) ? 'is-playing' : '' }}" href="{{ route('student.courses.show', ['course' => $course, 'lesson' => $lesson->id]) }}">
                        <span class="playlist-play" aria-hidden="true">{{ $activeLesson?->is($lesson) ? 'Ⅱ' : '▷' }}</span>
                        <span><strong>{{ $lesson->title }}</strong><small>{{ $activeLesson?->is($lesson) ? 'Sedang diputar' : 'Video lesson' }}</small></span>
                    </a>
                @else
                    <div class="playlist-item playlist-unavailable"><span class="playlist-play" aria-hidden="true">○</span><span><strong>{{ $lesson->title }}</strong><small>Video belum tersedia</small></span></div>
                @endif
            @empty
                <p class="empty-inline">Materi kelas belum ditambahkan.</p>
            @endforelse
        </div>
    </aside>
</div>
@endsection
