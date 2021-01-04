<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('moveDxeverDb', function() {
    echo (new \App\Http\Controllers\Fanli\DbMoveController())->index();
});
