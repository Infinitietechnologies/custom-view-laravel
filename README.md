# Laravel Custom View Generator

[![PHP Version](https://img.shields.io/badge/PHP->=7.4-777BB4.svg?style=flat-square)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel->=8.0-FF2D20.svg?style=flat-square)](https://laravel.com)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/infinitietechnologies/custom-view-laravel.svg?style=flat-square)](https://packagist.org/packages/infinitietechnologies/custom-view-laravel)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A powerful and flexible Laravel package that extends the default view generation capabilities with customizable templates, layouts, and file management features.

## 🚀 Features

- Create views with predefined templates (simple, blank, resource)
- Extend layout templates
- Add multiple sections to views
- Include Blade components
- File management operations (move, delete)
- File permission management
- Template shortcuts for common configurations

## 📋 Requirements

- PHP >= 7.4
- Laravel >= 8.0

## ⚙️ Installation

You can install the package via composer:

```bash
composer require infinitietechnologies/custom-view-laravel
```

## 🔧 Usage

### Basic View Creation

```bash
# Create a simple view
php artisan make:custom-view blog.index

# Create a view with specific type
php artisan make:custom-view blog.show --type=resource
```

### Layout and Sections

```bash
# Create a view extending a layout
php artisan make:custom-view blog.create --extend=layouts.app

# Create a view with sections
php artisan make:custom-view blog.edit --extend=layouts.app --section=content --section=scripts
```

### Components

```bash
# Create a view with components
php artisan make:custom-view blog.index --component=navbar --component=footer
```

### File Management

```bash
# Move a view
php artisan make:custom-view old-view --manage=move --from=old-location --to=new-location

# Delete a view
php artisan make:custom-view unused-view --manage=delete
```

### File Permissions

```bash
# Set file permissions
php artisan make:custom-view blog.index --permission --chmod=644 --owner=www-data --group=www-data
```

## 📖 Command Reference

```bash
make:custom-view {name} [options]
```

### Arguments

- `name`: The view name (e.g., "blog.index")

### Options

| Option | Description | Example |
|--------|-------------|---------|
| `--type` | View type (simple, blank, resource) | `--type=resource` |
| `--extend` or `-E` | Layout template to extend | `--extend=layouts.app` |
| `--section` or `-S` | Sections to include (multiple allowed) | `--section=content` |
| `--component` or `-C` | Components to include (multiple allowed) | `--component=navbar` |
| `--manage` or `-M` | Manage operation (delete or move) | `--manage=move` |
| `--from` | Source view name for moving | `--from=old-view` |
| `--to` | Destination view name for moving | `--to=new-view` |
| `--permission` or `-P` | Apply file permission settings | `--permission` |
| `--chmod` | The chmod value | `--chmod=644` |
| `--owner` | File owner | `--owner=www-data` |
| `--group` | File group | `--group=www-data` |
| `--template` or `-T` | Shorthand template option | `--template` |

## 🔍 Examples

### Creating a Blog Post View

```bash
php artisan make:custom-view blog.post --type=resource --extend=layouts.app --section=content --section=meta --component=share-buttons
```

### Setting Up an Admin Dashboard

```bash
php artisan make:custom-view admin.dashboard --type=blank --extend=layouts.admin --section=main --component=stats --component=charts --permission --chmod=644
```

## 🤝 Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 🔒 Security

If you discover any security-related issues, please email security@infinitietechnologies.com instead of using the issue tracker.

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 👥 Credits

- [Infinitie Technologies](https://github.com/Infinitietechnologies)
- [All Contributors](../../contributors)