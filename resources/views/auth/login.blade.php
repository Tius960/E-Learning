@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="login-wrap">
    <div class="login-copy" aria-hidden="true"></div>
    <div class="panel login-panel">
        <h1>Masuk</h1>
        <p class="muted">Masuk dengan akun admin atau siswa.</p>
        <form method="POST" action="{{ route('login.store') }}" class="stack-form">
            @csrf
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="username" required autofocus>
            </label>
            <label>Password
                <input type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
            </label>
            <label class="check-row"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button type="submit" class="button button-primary button-wide">Masuk</button>
        </form>
        <p class="login-contact">Belum punya akun? Hubungi admin kelas.</p>
    </div>
</section>
@endsection
