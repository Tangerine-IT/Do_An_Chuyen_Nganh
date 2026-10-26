<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('pets:send-health-reminders')->dailyAt('08:00');
        $schedule->command('pets:send-birthday-coupons')->dailyAt('08:05');
        $schedule->command('orders:expire-unpaid')->everyTenMinutes();
        $schedule->command('pets:send-feeding-reminders')->dailyAt('08:10');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => RoleMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
