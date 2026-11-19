@extends('layouts.app')

@section('title', 'Kelola course')

@section('content')
<div class="page-heading admin-heading">
    <h1>Kelola course</h1>
    <a class="button button-primary" href="{{ route('admin.courses.create') }}">＋ Course baru</a>
</div>
<p class="admin-summary">{{ $totalCourses }} course <span>·</span> {{ $publishedCourses }} terbit <span>·</span> {{ $videoCount }} video</p>

<section class="panel course-table-panel">
    <div class="table-toolbar">
        <h2>Semua course</h2>
        <form method="GET" action="{{ route('admin.courses.index') }}" class="course-filters">
            <label class="sr-only" for="course-search">Cari judul course</label>
            <input id="course-search" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="⌕  Cari judul course...">
            <label class="sr-only" for="course-status">Filter status</label>
            <select id="course-status" name="status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="published" @selected(($filters['status'] ?? '') === 'published')>Terbit</option>
                <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
            </select>
            <button class="button button-secondary button-small" type="submit">Cari</button>
        </form>
    </div>
    @if ($courses->isEmpty())
        <div class="empty-state table-empty"><h2>Course tidak ditemukan</h2><p>Coba ubah kata pencarian atau filter status.</p></div>
    @else
        <div class="table-scroll">
            <table class="course-table">
                <thead><tr><th>Course</th><th>Lesson</th><th>Status</th><th>Diperbarui</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td data-label="Course"><div class="table-course"><div><strong>{{ $course->title }}</strong><small>{{ \Illuminate\Support\Str::limit($course->description, 76) }}</small></div></div></td>
                            <td data-label="Lesson">{{ $course->lessons_count }} lesson</td>
                            <td data-label="Status"><span class="status {{ $course->is_published ? 'status-ready' : 'status-draft' }}">{{ $course->is_published ? 'Terbit' : 'Draft' }}</span></td>
                            <td data-label="Diperbarui">{{ $course->updated_at->format('d M Y') }}</td>
                            <td data-label="Aksi"><div class="table-actions">
                                <a class="button button-secondary button-small" href="{{ route('admin.courses.lessons.index', $course) }}">Kelola</a>
                                <a class="icon-button" href="{{ route('admin.courses.edit', $course) }}" aria-label="Ubah {{ $course->title }}" title="Ubah">✎</a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" data-confirm="Hapus course “{{ $course->title }}” beserta semua lesson dan videonya? Tindakan ini tidak dapat dibatalkan.">@csrf @method('DELETE')<button class="icon-button delete-icon" type="submit" aria-label="Hapus {{ $course->title }}" title="Hapus">⌫</button></form>
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="table-footer"><span>Menampilkan {{ $courses->count() }} dari {{ $courses->total() }} course</span><div class="pagination">{{ $courses->links() }}</div></div>
    @endif
</section>
@endsection
