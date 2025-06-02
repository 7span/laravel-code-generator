 -- Clearing Logs
You can clear generated logs either manually or automatically.

🔹 Manually (Artisan)
php artisan code-generator:clear-logs

Add env variable in .env file like this:
 CODE_GENERATOR_LOG_RETENTION_DAYS = 7,

🔹 Automatically (Laravel 12+)

Add this in bootstrap/app.php:
scheduler()
    ->command('codegenerator:clear')
    ->daily(); // or weekly/monthly

🔹 Automatically (Laravel 10+)
For Laravel 10 and later, you can schedule the log clearing command in the schedule method of your app/Console/Kernel.php file:

protected function schedule(Schedule $schedule): void
{
    $schedule->command('codegenerator:clearlogs')->daily(); // Runs daily
    // Or, weekly:
    // $schedule->command('codegenerator:clearlogs')->weekly();
    // Or, for monthly:
    // $schedule->command('codegenerator:clearlogs')->monthly();
}