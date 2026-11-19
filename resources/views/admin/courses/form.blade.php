@extends('layouts.app')

@section('title', $course->exists ? 'Ubah course' : 'Course baru')

@section('content')
<div class="page-heading admin-heading">
    <div><a class="back-link" href="{{ route('admin.courses.index') }}">← Kembali ke course</a><h1>{{ $course->exists ? 'Ubah course' : 'Course baru' }}</h1><p class="muted">Atur informasi kelas dan materi videonya.</p></div>
    @if ($course->exists)<a class="button button-secondary" href="{{ route('admin.courses.lessons.index', $course) }}">Kelola lesson</a>@endif
</div>
<div class="course-editor-grid">
    <section class="panel form-panel">
        <h2>Informasi course</h2>
        <form method="POST" action="{{ $course->exists ? route('admin.courses.update', $course) : route('admin.courses.store') }}" class="stack-form">
            @csrf @if ($course->exists) @method('PUT') @endif
            <label>Judul course<input type="text" name="title" value="{{ old('title', $course->title) }}" maxlength="180" required placeholder="Contoh: Dasar Web Development"></label>
            <label>Deskripsi<textarea name="description" rows="5" required placeholder="Apa yang akan dipelajari siswa di course ini?">{{ old('description', $course->description) }}</textarea></label>
            <label>Status course
                <select name="is_published" required>
                    <option value="0" @selected((string) old('is_published', $course->exists ? (int) $course->is_published : 0) === '0')>Draft</option>
                    <option value="1" @selected((string) old('is_published', $course->exists ? (int) $course->is_published : 0) === '1')>Terbit</option>
                </select>
            </label>
            <p class="form-help">Course terbit dapat dilihat oleh semua siswa.</p>
            <div class="form-actions"><a class="button button-secondary" href="{{ route('admin.courses.index') }}">Batal</a><button type="submit" class="button button-primary">{{ $course->exists ? 'Simpan course' : 'Buat course' }}</button></div>
        </form>
        @if ($course->exists)
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" data-confirm="Hapus course “{{ $course->title }}” beserta semua lesson dan videonya? Tindakan ini tidak dapat dibatalkan." class="delete-course-form">@csrf @method('DELETE')<button class="text-danger" type="submit">Hapus course</button></form>
        @endif
    </section>
    @if ($course->exists)
    <section class="panel editor-lessons">
        <div class="section-heading"><div><h2>Lesson dalam course</h2><p class="muted">{{ $course->lessons->count() }} lesson</p></div><a class="button button-secondary button-small" href="{{ route('admin.courses.lessons.create', $course) }}">＋ Lesson baru</a></div>
        @forelse ($course->lessons as $lesson)
            <div class="editor-lesson-row">
                <span class="lesson-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="lesson-info"><strong>{{ $lesson->title }}</strong><small>{{ $lesson->video_status === 'ready' ? 'Video tersedia' : 'Video belum diunggah' }}</small></div>
                <span class="status {{ $lesson->video_status === 'ready' ? 'status-ready' : 'status-draft' }}">{{ $lesson->video_status === 'ready' ? 'Video' : 'Draft' }}</span>
                <a class="button button-quiet button-small" href="{{ route('admin.lessons.edit', $lesson) }}">Ubah</a>
            </div>
        @empty
            <p class="empty-inline">Belum ada lesson. Tambahkan materi pertama untuk kelas ini.</p>
        @endforelse
        <a class="button button-secondary add-lesson-button" href="{{ route('admin.courses.lessons.index', $course) }}">Lihat dan urutkan lesson</a>
    </section>
    @endif
</div>
@if (! $course->exists)
    <aside class="draft-note"><strong>Course baru disimpan sebagai draft.</strong><span>Tambahkan lesson dan video sebelum menerbitkannya.</span></aside>
@endif
@endsection
