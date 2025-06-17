#  Configuration
  Publishing Assets
- After installation, publish the configuration and migration files:
```
php artisan vendor:publish --tag=code-generator-config
php artisan vendor:publish --tag=code-generator-migrations
```
This will create:
-  Configuration File

**Path:** `config/code-generator.php`

> Allows customization of paths, namespaces, route prefixes, and stub templates to fit your application structure.

---

-   Migration File

**Path:**  `database/migrations/xxxx_xx_xx_xxxxxx_create_code_generator_file_logs_table.php`

- Migrate 'code_generator_file_logs' table to the database using:

```
php artisan migrate
```
> Creates the `code_generator_file_logs` table to store generation activity logs for the built-in log viewer UI.

---

 Configuration Options

After publishing, you can edit the main configuration file: config/code-generator.php

- You can customize the following to suit your project: Route Path & Prefix
- Define the Route path where UI wil be accessed.
- Set Folder Paths for generated file: where files will be generated. and  based on that namesacce will also changed. 
- Set Log Retention Days as the number of days you want to retain logs of the generated files in your .env file.

 Example:
 ```
'route_path => 'code-gen',
'paths' => [
    'services' => 'App\\Services\\Generated',
],
  'log_retention_days' => env('CODE_GENERATOR_LOG_RETENTION_DAYS', 2),
```
