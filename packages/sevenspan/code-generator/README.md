# Laravel Code Generator

A developer-friendly Laravel package to **generate models, migrations, controllers, requests, resources, factories, policies, observers, services, notifications, and traits** using a modern Livewire-powered UI.

This package is designed to **accelerate API development** in Laravel by providing a visual interface for defining models, fields, relationships, and scaffolding complete REST endpoints.

---

## 🚀 Features

-   **REST API Generator**: Instantly scaffold Models, Controllers, Migrations, Services, Resources, Requests, and more.
-   **Livewire UI**: Interactive, dynamic interface for rapid development.
-   **Trait Support**: Easily add reusable traits to your models via the UI.
-   **Validation & Error Handling**: Smart file handling with overwrite protection.
-   **Highly Configurable**: Customize paths, namespaces, route prefixes, and stub templates.
-   **Smart File Placement**: Files are created in Laravel-standard folders.
-   **Log Viewer**: View package logs directly from the UI for troubleshooting and transparency.

---

## 🧩 Requirements

-   Laravel 12+
-   PHP 8.2+
-   [Livewire 3](https://livewire.laravel.com/)
-   [spatie/laravel-query-builder](https://github.com/spatie/laravel-query-builder)

---

## 📦 Installation

1. **Install via Composer:**

    ```bash
    composer require sevenspan/code-generator
    ```

2. **Publish the configuration and migrations:**

    ```bash
    php artisan vendor:publish --tag=code-generator-config
    php artisan vendor:publish --tag=code-generator-migrations
    ```

3. **(Optional) Customize configuration:**

    Edit `config/code-generator.php` to set route paths, folder locations, and stub templates as needed.

---

## 🖥️ Usage

1. **Access the UI**

    Visit:

    ```
    http://yourdomain.com/code-generator
    ```

2. **Define your model, fields, and relationships**

    - Use the UI to add fields (columns), set data types, validation, and foreign keys.
    - Add Eloquent relationships visually (hasOne, hasMany, belongsToMany, etc.).
    - Select which files to generate (model, migration, controller, etc.).
    - Optionally, select other features like traits, observers, and notifications.

3. **Generate Files**

    - Click "Generate" to scaffold all selected files in your Laravel app.

---

## 📜 Logs

-   The package provides a log viewer in the UI to help you review generation activity and errors.
-   **To clear the logs**, run the following Artisan command:

    ```bash
    php artisan code-generator:clear-logs
    ```

---

## ⚙️ Configuration

The main configuration file is published at `config/code-generator.php`.  
You can customize:

-   Route path and prefix
-   Folder paths for generated files
-   Stub templates for each file type

---

## 🧑‍💻 Contributing

Pull requests and issues are welcome!

-   Fork the repo
-   Create a new branch: `git checkout -b feature/my-feature`
-   Commit your changes and push
-   Open a Pull Request

---

## 📄 License

The MIT License (MIT).

---

**Happy coding! 🚀**
