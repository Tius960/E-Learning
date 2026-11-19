@extends('layouts.app')

@section('title', 'Kelas tersedia')

@section('content')
<div class="section-heading student-section-heading">
    <div><h1>Kelas yang tersedia</h1><p class="muted">{{ $courses->total() }} kelas tersedia</p></div>
    <form method="GET" action="{{ route('student.courses.index') }}" class="search-form">
        <label class="sr-only" for="student-search">Cari kelas</label>
        <input id="student-search" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="⌕  Cari kelas...">
    </form>
</div>
@if ($courses->isEmpty())
    <div class="empty-state"><span class="empty-icon" aria-hidden="true">▤</span><h2>Belum ada kelas tersedia</h2><p>Course baru akan muncul di sini setelah diterbitkan oleh admin.</p></div>
@else
    <div class="course-grid student-grid">
        @foreach ($courses as $course)
            <a class="panel student-course-card" href="{{ route('student.courses.show', $course) }}">
                <div class="student-card-copy">
                    <h3>{{ $course->title }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit($course->description, 110) }}</p>
                    <div class="student-card-footer"><span>{{ $course->lessons_count }} lesson</span><strong>Mulai belajar <span aria-hidden="true">→</span></strong></div>
                </div>
            </a>
        @endforeach
    </div>
    <div class="pagination">{{ $courses->links() }}</div>
@endif
@endsection
