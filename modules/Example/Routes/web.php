<?php

use Illuminate\Support\Facades\Route;
use Modules\Example\Http\Controllers\ExampleController;

Route::resource('example', ExampleController::class);