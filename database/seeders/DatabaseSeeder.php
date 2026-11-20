<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@elearning.test'], [
            'name' => 'Admin Demo',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        User::updateOrCreate(['email' => 'student@elearning.test'], [
            'name' => 'Student Demo',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $course = Course::firstOrCreate(
            ['title' => 'Dasar-Dasar Web Development'],
            ['description' => 'Kenali HTML, CSS, dan JavaScript untuk mulai membangun website.', 'is_published' => true, 'created_by' => $admin->id],
        );

        if ($course->lessons()->doesntExist()) {
            foreach (['Pengenalan Web', 'HTML dan Struktur Halaman', 'Dasar CSS'] as $index => $title) {
                Lesson::create(['course_id' => $course->id, 'title' => $title, 'description' => 'Materi '.$title, 'position' => $index + 1]);
            }
        }
    }
}
