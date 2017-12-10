<?php

Route::post('message', \App\Http\Controllers\BotmanContoller::class.'@handle');
