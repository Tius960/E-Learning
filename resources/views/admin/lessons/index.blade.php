@extends('layouts.app')

@section('title', 'Lesson · '.$course->title)

@section('content')
<div class="page-heading">
    <div><a class="back-link" href="{{ route('admin.courses.index') }}">← Semua course</a><h1>{{ $course->title }}</h1><p class="muted">Atur lesson dan urutannya. Siswa hanya dapat melihat course yang sudah terbit.</p></div>
    <a class="button button-primary" href="{{ route('admin.courses.lessons.create', $course) }}">＋ Tambah lesson</a>
</div>
<div class="lesson-list">
    @forelse ($lessons as $lesson)
        <article class="panel lesson-row">
            <span class="lesson-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <div class="lesson-info"><h2>{{ $lesson->title }}</h2><p>{{ \Illuminate\Support\Str::limit($lesson->description ?: 'Belum ada deskripsi.', 100) }}</p></div>
            <span class="status {{ $lesson->video_status === 'ready' ? 'status-ready' : ($lesson->video_status === 'failed' ? 'status-failed' : 'status-draft') }}">{{ $lesson->video_status === 'none' ? 'Tanpa video' : ucfirst($lesson->video_status) }}</span>
            <div class="lesson-controls">
                <form method="POST" action="{{ route('admin.lessons.move', $lesson) }}">@csrf @method('PATCH')<input type="hidden" name="direction" value="up"><button class="icon-button" aria-label="Pindah ke atas" title="Pindah ke atas" @disabled($loop->first)>↑</button></form>
                <form method="POST" action="{{ route('admin.lessons.move', $lesson) }}">@csrf @method('PATCH')<input type="hidden" name="direction" value="down"><button class="icon-button" aria-label="Pindah ke bawah" title="Pindah ke bawah" @disabled($loop->last)>↓</button></form>
                <a class="button button-secondary button-small" href="{{ route('admin.lessons.edit', $lesson) }}">Ubah</a>
                <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" data-confirm="Hapus lesson “{{ $lesson->title }}” dan video yang tersimpan? Tindakan ini tidak dapat dibatalkan.">@csrf @method('DELETE')<button class="button button-danger-quiet button-small" type="submit">Hapus</button></form>
            </div>
        </article>
    @empty
        <div class="empty-state"><span class="empty-icon">▤</span><h2>Belum ada lesson</h2><p>Tambahkan lesson pertama untuk course ini.</p><a class="button button-primary" href="{{ route('admin.courses.lessons.create', $course) }}">Tambah lesson</a></div>
    @endforelse
</div>
@endsection
