<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pepe:price')->everyFifteenMinutes();
