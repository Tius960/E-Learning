<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/courses');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('courses', AdminCourseController::class)->except('show');
    Route::get('courses/{course}/lessons', [AdminLessonController::class, 'index'])->name('courses.lessons.index');
    Route::get('courses/{course}/lessons/create', [AdminLessonController::class, 'create'])->name('courses.lessons.create');
    Route::post('courses/{course}/lessons', [AdminLessonController::class, 'store'])->name('courses.lessons.store');
    Route::get('lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');
    Route::patch('lessons/{lesson}/order', [AdminLessonController::class, 'move'])->name('lessons.move');
    Route::post('lessons/{lesson}/video', [AdminLessonController::class, 'uploadVideo'])->name('lessons.video');
});

Route::get('/courses', [StudentCourseController::class, 'index'])
    ->middleware('auth')
    ->name('student.courses.index');

Route::middleware(['auth', 'role:student'])->name('student.')->group(function (): void {
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
});
Route::get('/lessons/{lesson}/video', [VideoController::class, 'show'])->middleware('auth')->name('lessons.video.show');
