# 📜 Logs and Debugging
- The package automatically logs all code generation activities in the code_generator_file_logs table. 

You can view these logs directly from the built-in UI log viewer by navigating on Logs link
given in sidabar or visit route : /code-generator/logs.

# 🧹 Clearing Logs
- You can clear old logs using the Artisan command:
```
php artisan code-generator:clear-logs --days=4
```

This will remove logs based on the number of retention days defined in config/code-generator.php or via .env.

- Log Retention Period
 Control how many days logs should be retained in the code_generator_file_logs table. Older logs will be cleared based on this setting to keep the log storage optimized.


🔧 Example:
 Change route prefix, set a custom namespace for services, or adjust the log retention period like this:
 ```
'route_path => 'code-gen',
'paths' => [
    'services' => 'App\\Services\\Generated',
],
  'log_retention_days' => 5,
```