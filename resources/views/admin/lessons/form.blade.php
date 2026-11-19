@extends('layouts.app')

@section('title', $lesson->exists ? 'Ubah lesson' : 'Lesson baru')

@section('content')
<div class="page-heading"><div><a class="back-link" href="{{ route('admin.courses.lessons.index', $course) }}">← {{ $course->title }}</a><h1>{{ $lesson->exists ? 'Ubah lesson' : 'Tambah lesson' }}</h1><p class="muted">Tambahkan detail materi dan unggah video lesson secara langsung.</p></div></div>
@if (! $lesson->exists)
<section class="panel form-panel">
    <form method="POST" action="{{ $lesson->exists ? route('admin.lessons.update', $lesson) : route('admin.courses.lessons.store', $course) }}" class="stack-form">
        @csrf @if ($lesson->exists) @method('PUT') @endif
        <label>Judul lesson<input type="text" name="title" value="{{ old('title', $lesson->title) }}" maxlength="180" required placeholder="Contoh: Pengenalan Web"></label>
        <label>Deskripsi materi <span class="optional">(opsional)</span><textarea name="description" rows="5" placeholder="Ringkasan materi lesson...">{{ old('description', $lesson->description) }}</textarea></label>
        <div class="form-actions"><a class="button button-quiet" href="{{ route('admin.courses.lessons.index', $course) }}">Batal</a><button type="submit" class="button button-primary">{{ $lesson->exists ? 'Simpan perubahan' : 'Buat lesson' }}</button></div>
    </form>
</section>
@else
<div class="lesson-editor-grid">
<section class="panel form-panel">
    <h2>Informasi lesson</h2>
    <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" class="stack-form">
        @csrf @method('PUT')
        <label>Judul lesson<input type="text" name="title" value="{{ old('title', $lesson->title) }}" maxlength="180" required></label>
        <label>Deskripsi lesson <span class="optional">(opsional)</span><textarea name="description" rows="5" placeholder="Ringkasan materi lesson...">{{ old('description', $lesson->description) }}</textarea></label>
        <p class="form-help">Perubahan lesson disimpan terpisah dari course.</p>
        <div class="form-actions"><a class="button button-secondary" href="{{ route('admin.courses.edit', $course) }}">Batal</a><button type="submit" class="button button-primary">Simpan lesson</button></div>
    </form>
</section>

<section class="panel upload-panel">
    <div><h2>Video lesson</h2><p class="muted">MP4, MOV, WebM · Maks. 500 MB</p></div>
    <div class="video-state">
        <span id="video-status" data-status="{{ $lesson->video_status }}" class="status {{ $lesson->video_status === 'ready' ? 'status-ready' : ($lesson->video_status === 'failed' ? 'status-failed' : 'status-draft') }}">Status: {{ $lesson->video_status === 'none' ? 'Belum diunggah' : ucfirst((string) $lesson->video_status) }}</span>
        @if ($lesson->video_status === 'ready')<a id="video-link" class="text-link" href="{{ route('lessons.video.show', $lesson) }}" target="_blank" rel="noopener">Lihat video ↗</a>@else<a id="video-link" class="text-link" href="{{ route('lessons.video.show', $lesson) }}" target="_blank" rel="noopener" hidden>Lihat video ↗</a>@endif
    </div>
    <form id="video-upload-form" data-upload-url="{{ route('admin.lessons.video', $lesson) }}" class="stack-form">
        <label class="upload-dropzone">⇧<strong>Pilih video untuk diunggah</strong><small>MP4, MOV, WebM · Maks. 500 MB</small><input id="video-file" type="file" name="video" accept="video/mp4,video/webm,video/quicktime,video/x-matroska" required></label>
        <div id="upload-progress-wrap" class="progress-wrap" hidden><div class="progress-track"><div id="upload-progress" class="progress-bar"></div></div><div class="progress-label"><span id="upload-message">Menyiapkan upload...</span><span id="upload-percent">0%</span></div></div>
        <div id="upload-feedback" class="upload-feedback" role="status" aria-live="polite"></div>
        <button id="upload-button" class="button button-secondary" type="submit">Unggah video</button>
    </form>
</section>
</div>
<script>
(() => {
    const form = document.getElementById('video-upload-form');
    const file = document.getElementById('video-file');
    const button = document.getElementById('upload-button');
    const progressWrap = document.getElementById('upload-progress-wrap');
    const progressBar = document.getElementById('upload-progress');
    const percentLabel = document.getElementById('upload-percent');
    const message = document.getElementById('upload-message');
    const feedback = document.getElementById('upload-feedback');
    const status = document.getElementById('video-status');
    const videoLink = document.getElementById('video-link');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const statusClass = (value) => value === 'ready' ? 'status status-ready' : (value === 'failed' ? 'status status-failed' : 'status status-draft');

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!file.files.length) return;
        const request = new XMLHttpRequest();
        const data = new FormData();
        data.append('video', file.files[0]);
        request.open('POST', form.dataset.uploadUrl);
        request.setRequestHeader('X-CSRF-TOKEN', csrf);
        request.setRequestHeader('Accept', 'application/json');
        button.disabled = true;
        button.textContent = 'Mengunggah...';
        status.textContent = 'Status: uploading';
        status.className = 'status status-draft';
        feedback.textContent = '';
        progressWrap.hidden = false;
        progressBar.style.width = '0%';
        percentLabel.textContent = '0%';
        message.textContent = 'Mengunggah video...';
        request.upload.addEventListener('progress', (event) => {
            if (!event.lengthComputable) return;
            const percent = Math.round((event.loaded / event.total) * 100);
            progressBar.style.width = `${percent}%`;
            percentLabel.textContent = `${percent}%`;
        });
        request.addEventListener('load', () => {
            let result = {};
            try {
                result = JSON.parse(request.responseText);
            } catch {
                result = { message: 'Server mengirim respons yang tidak dapat dibaca.' };
            }
            if (request.status >= 200 && request.status < 300) {
                progressBar.style.width = '100%';
                percentLabel.textContent = '100%';
                message.textContent = 'Upload selesai';
                feedback.textContent = result.message;
                feedback.className = 'upload-feedback success-text';
                status.dataset.status = 'ready';
                status.textContent = 'Status: ready';
                status.className = statusClass('ready');
                videoLink.href = result.url;
                videoLink.hidden = false;
                file.value = '';
                button.textContent = 'Unggah video lain';
            } else {
                message.textContent = 'Upload gagal';
                const validationMessage = result.errors?.video?.[0];
                feedback.textContent = request.status === 413
                    ? 'Ukuran upload melebihi batas server. Naikkan upload_max_filesize dan post_max_size pada PHP Laragon, lalu restart server.'
                    : validationMessage || result.message || `Video gagal diunggah (HTTP ${request.status}). Periksa file dan konfigurasi upload server.`;
                feedback.className = 'upload-feedback error-text';
                if (result.status === 'failed') {
                    status.dataset.status = 'failed';
                    status.textContent = 'Status: failed';
                    status.className = statusClass('failed');
                    videoLink.hidden = true;
                } else {
                    status.textContent = `Status: ${status.dataset.status}`;
                    status.className = statusClass(status.dataset.status);
                }
                button.disabled = false;
                button.textContent = 'Coba unggah lagi';
            }
        });
        request.addEventListener('error', () => {
            message.textContent = 'Upload gagal';
            feedback.textContent = 'Koneksi terputus. Periksa jaringan, kemudian coba kembali.';
            feedback.className = 'upload-feedback error-text';
            status.textContent = `Status: ${status.dataset.status}`;
            status.className = statusClass(status.dataset.status);
            button.disabled = false;
            button.textContent = 'Coba unggah lagi';
        });
        request.send(data);
    });
})();
</script>
@endif
@endsection
