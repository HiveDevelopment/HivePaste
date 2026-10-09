<?php
use App\Models\Paste;
use Illuminate\Support\Facades\Schedule;
Schedule::call(fn () => Paste::where('expires_at', '<=', now())->delete())->hourly()->name('expire-pastes')->withoutOverlapping();
