# Custom View Laravel Package

A Laravel package that provides an Artisan command to generate, delete, move, and manage custom view files. This package streamlines the process of scaffolding Blade view files with customizable layouts, sections, components, and file permissions.

## Features

- **Generate Views:** Create simple, blank, or resource (CRUD) views.
- **Custom Layouts:** Extend custom Blade layouts with ease.
- **Sections & Components:** Add multiple sections and include reusable components.
- **View Management:** Delete or move (rename) view files interactively.
- **File Permissions:** Set file permissions, owner, and group after generation.
- **Shorthand Mode:** Use quick-start options to rapidly build common view configurations.

## Installation

### Via Composer

Since this package is hosted on GitHub, add the repository to your Laravel project's `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Infinitietechnologies/custom-view-laravel.git"
    }
]
```

Then run the following command in your Laravel project root:

```bash
composer require infinitietechnologies/custom-view-laravel
```

Laravel's package auto-discovery will automatically register the service provider and register the Artisan command.

## Usage Examples

The package provides the `make:custom-view` command with various options. Here are some common usage examples:

### 1. Basic Usage

```bash
# Create a simple view
php artisan make:custom-view blog.index

# Create a blank view (no template)
php artisan make:custom-view blog.index --type=blank
```

### 2. Resource Views

```bash
# Create full CRUD views (index, create, edit, show)
php artisan make:custom-view blog --type=resource
```

### 3. Template Options

```bash
# Create view with custom layout
php artisan make:custom-view blog.index -E layouts.custom

# Create view with multiple sections
php artisan make:custom-view blog.index -S content -S sidebar -S scripts

# Create view with components
php artisan make:custom-view blog.index -C header -C footer -C sidebar

# Combine template options
php artisan make:custom-view blog.index -E layouts.custom -S content -S sidebar -C alert
```

### 4. Management Options

```bash
# Delete a view
php artisan make:custom-view blog.index -M delete

# Move/rename a view
php artisan make:custom-view blog --M move --from=blog.index --to=posts.index
```

### 5. Permission Options

```bash
# Set file permissions
php artisan make:custom-view blog.index -P --chmod=644

# Set owner and group
php artisan make:custom-view blog.index -P --chmod=644 --owner=www-data --group=www-data
```

### 6. Complex Examples

```bash
# Create resource views with custom layout and sections
php artisan make:custom-view admin.blog --type=resource -E layouts.admin -S content -S sidebar

# Create view with all options
php artisan make:custom-view blog.index \
    -E layouts.custom \
    -S content -S sidebar \
    -C header -C footer \
    -P --chmod=644 --owner=www-data
```

## Command Options Reference

| Option | Description |
|--------|-------------|
| `name` | The name of the view (e.g., blog.index) |
| `--type` | View type (default, blank, resource) |
| `-E, --extends` | Layout the view extends |
| `-S, --section` | Sections to include (multiple allowed) |
| `-C, --component` | Components to include (multiple allowed) |
| `-M, --management` | Management operations (delete, move) |
| `--from` | Source path for move operation |
| `--to` | Destination path for move operation |
| `-P, --permission` | Set file permissions |
| `--chmod` | File permissions in octal (default: 644) |
| `--owner` | File owner |
| `--group` | File group |

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email infinitietechnologies05@gmail.com instead of using the issue tracker.

## Credits

- [Infinitie Technologies](https://github.com/infinitietech05)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---
Created by [Infinitie Technologies](https://github.com/infinitietech05) | Last updated: 2025-04-09