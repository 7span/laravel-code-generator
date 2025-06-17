#  Logs
- The package automatically logs all code generation activities in the code_generator_file_logs table. 

You can view these logs directly from the built-in UI log viewer by navigating on Logs link
given in sidabar or visit route : /code-generator/logs.

---

##  Clear Logs
- You can clear old logs using the Artisan command:
```
php artisan code-generator:clear-logs --days=4
```
 This will remove logs based on the number of retention days defined in config/code-generator.php or via .env.

-  To automatically clear generated logs add the following to your bootstrap/app.php file:

```
scheduler()
    ->command('code-generator:clear-logs')
    ->daily(); // or weekly/monthly
```

This ensures that the code-generator:clear-logs command is executed automatically based on your preferred schedule.

- Log Retention Days
 Control how many days logs should be retained in the code_generator_file_logs table. Older logs will be cleared based on this setting to keep the log storage optimized.