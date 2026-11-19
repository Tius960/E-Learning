<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:elearning', function (): void {
    $this->comment('Kelas Kita e-learning demo is ready.');
})->purpose('Display a short e-learning project message');
