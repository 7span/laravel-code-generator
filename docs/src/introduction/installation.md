# Prerequisites

Ensure your environment meets the following requirements:
- Laravel 12.x or higher – Latest Laravel version for compatibility.

- PHP 8.2+ – Required for modern syntax and type support.

- Livewire 3 – The UI is built entirely using Livewire components.

- spatie/laravel-query-builder – Used internally within trait files for structured query handling.

---

# Installation
Install the package via Composer:
```
composer require sevenspan/code-generator --dev
```

ℹ️ Note: This package is intended for development use only, as it helps generate code during the development phase and is not required in production.

---

## Additional Notes

Upon generating files, the following traits will also be included:

-   **ApiResponser Trait:** A trait to standardize API responses.
-   **BaseModel Trait:** A trait providing common model functionalities.