# ⚙️ Configuration
 - 📤 Publishing Assets
After installation, publish the configuration and migration files:
```
php artisan vendor:publish --tag=code-generator-config
php artisan vendor:publish --tag=code-generator-migrations
```
This will create:
- 📁 Configuration File

**Path:** `config/code-generator.php`

> Allows customization of paths, namespaces, route prefixes, and stub templates to fit your application structure.

---

-  🗃️ Migration File

**Path:**  `database/migrations/xxxx_xx_xx_xxxxxx_create_code_generator_file_logs_table.php`

> Creates the `code_generator_file_logs` table to store generation activity logs for the built-in log viewer UI.

---

⚙️ Configuration Options

After publishing, you can edit the main configuration file: config/code-generator.php

- You can customize the following to suit your project:
 -Route Path & Prefix
 - Define the URL path where the code generator UI can be accessed.


- Folder Paths
 - Set custom paths and namespaces for generated files like Models, Controllers, Services, Requests, etc.
