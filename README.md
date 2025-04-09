
# Laravel Custom View Generator Package

A powerful Laravel package to **generate**, **delete**, **move**, and **manage Blade view files** with advanced options for layout, section, component inclusion, and permission handling.

> 📦 Created by [Harshad Pindoriya](mailto:infinitietechnologies05@gmail.com) — Infinitie Technologies  
> 🛠 Compatible with Laravel 8, 9, and 10+

---

## 🚀 Features

- ✅ Generate views using dot-notation (e.g., `blog.index`)
- 🧩 Extend layout templates (`--extend=layouts.app`)
- 📦 Include multiple sections and components
- 🔁 Manage views: **delete** or **move**
- 🔐 Set file permissions, ownership, and group
- ⚡ Shorthand template options
- 🎛 Supports resource, blank, and simple views

---

## 📦 Installation

```bash
composer require vendor/custom-view
```

If you're using Laravel < 5.5, add the service provider manually in `config/app.php`:

```php
'providers' => [
    Vendor\CustomView\CustomViewServiceProvider::class,
];
```

---

## 🛠 Usage

### ✅ Generate a New View

```bash
php artisan make:custom-view blog.index
```

### 🌐 Extend a Layout

```bash
php artisan make:custom-view blog.index --extend=layouts.app
```

### 🧩 Include Sections & Components

```bash
php artisan make:custom-view blog.index --section=content --section=scripts --component=alert --component=footer
```

### 📁 View Types

- `--type=simple` (default)
- `--type=blank`
- `--type=resource` – adds CRUD title and structure

---

## 🔁 View Management

### ❌ Delete a View

```bash
php artisan make:custom-view blog.index --manage=delete
```

### 📂 Move a View

```bash
php artisan make:custom-view dummy.index --manage=move --from=dummy.index --to=blog.index
```

---

## 🔐 File Permissions

Apply permission settings automatically:

```bash
php artisan make:custom-view blog.index --permission --chmod=644 --owner=www-data --group=www-data
```

---

## ⚡ Shorthand Template Option

Include a quick template indicator:

```bash
php artisan make:custom-view blog.index --template=basic
```

> Adds a comment: `<!-- Using shorthand template: 'basic' -->`

---

## 📄 Generated File Path

All views are created inside the standard Laravel path:

```
resources/views/{your-view-name}.blade.php
```

Dot notation like `blog.index` becomes `resources/views/blog/index.blade.php`.

---

## 🧠 Example

```bash
php artisan make:custom-view admin.dashboard \
    --extend=layouts.admin \
    --section=content \
    --component=breadcrumb \
    --type=simple \
    --permission --chmod=644 --owner=www-data --group=www-data
```

---

## 📚 Composer Metadata

```json
{
  "name": "vendor/custom-view",
  "description": "A package to generate, delete, move, and manage custom view files in Laravel.",
  "type": "library",
  "require": {
    "php": ">=7.3",
    "illuminate/support": "^8.0|^9.0|^10.0"
  },
  "autoload": {
    "psr-4": {
      "Vendor\\CustomView\\": "src/"
    }
  }
}
```

---

## 🤝 Contributing

Feel free to fork this repo and submit PRs. For major changes, open an issue first to discuss what you'd like to change.

---

## 📬 Author

**Harshad Pindoriya**  
📧 [infinitietechnologies05@gmail.com](mailto:infinitietechnologies05@gmail.com)  
🔗 [GitHub Repository](https://github.com/infinitietechnologies/custom-view-laravel)
🔗 [About us](https://infinitietech.com/)

---

## 📄 License

This package is open-source and available under the [MIT license](LICENSE).
