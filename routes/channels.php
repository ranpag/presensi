<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('siswa', function () {
    return true;
});