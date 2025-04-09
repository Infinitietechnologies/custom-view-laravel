<?php

namespace Acme\CustomView;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * View Generator for Laravel Custom Views
 *
 * @author infinitietech05 <your.email@infinitietechnologies.com>
 * @package infinitietechnologies/custom-view-laravel
 * @created 2025-04-09 08:13:57
 */
class ViewGenerator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Create a new view generator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->config = app('config');
    }

    /**
     * Generate a view file.
     *
     * @param  string  $name
     * @param  array  $options
     * @return bool
     */
    public function generate(string $name, array $options = []): bool
    {
        $this->validateName($name);

        $path = $this->getViewPath($name);
        $content = $this->generateContent($name, $options['type'] ?? 'simple', $options);

        if ($this->files->exists($path)) {
            throw new InvalidArgumentException("View [{$name}] already exists!");
        }

        $this->ensureDirectoryExists($path);

        $this->files->put($path, $content);

        if (isset($options['permission']) && $options['permission']) {
            $this->setFilePermissions($path, $options);
        }

        return true;
    }

    /**
     * Validate the view name.
     *
     * @param  string  $name
     * @return void
     * @throws \InvalidArgumentException
     */
    public function validateName(string $name): void
    {
        if (preg_match('([^A-Za-z0-9_./\-])', $name)) {
            throw new InvalidArgumentException('View name contains invalid characters.');
        }
    }

    /**
     * Get the view path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getViewPath(string $name): string
    {
        $path = $this->formatViewPath($name);
        return $this->config->get('custom-view.view_path') . DIRECTORY_SEPARATOR . $path . '.blade.php';
    }

    /**
     * Format view path.
     *
     * @param  string  $name
     * @return string
     */
    public function formatViewPath(string $name): string
    {
        return str_replace('.', '/', $name);
    }

    /**
     * Generate view content.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  array  $options
     * @return string
     */
    public function generateContent(string $name, string $type = 'simple', array $options = []): string
    {
        if (isset($options['template'])) {
            return $this->processPreset($name, $options['template']);
        }

        $content = '';

        if (isset($options['extend'])) {
            $content .= "@extends('{$options['extend']}')\n\n";
        }

        if (isset($options['sections'])) {
            foreach ($options['sections'] as $section) {
                $this->validateSectionName($section);
                $content .= "@section('$section')\n\n@endsection\n\n";
            }
        }

        if (isset($options['components'])) {
            foreach ($options['components'] as $component) {
                $this->validateComponentName($component);
                $content .= "<x-{$component} />\n";
            }
        }

        switch ($type) {
            case 'simple':
                $content .= '<div>
    <!-- ' . Str::title(str_replace(['.', '/', '_', '-'], ' ', $name)) . ' -->
</div>';
                break;
            case 'blank':
                break;
            case 'resource':
                if (!str_contains($content, '@section')) {
                    $content .= "@section('content')\n\n@endsection\n";
                }
                break;
            default:
                throw new InvalidArgumentException("Invalid view type [{$type}]");
        }

        return $content;
    }

    /**
     * Process a preset template.
     *
     * @param  string  $name
     * @param  string  $preset
     * @return string
     */
    public function processPreset(string $name, string $preset): string
    {
        $presets = $this->config->get('custom-view.presets');

        if (!isset($presets[$preset])) {
            throw new InvalidArgumentException("Preset [{$preset}] not found!");
        }

        return $this->generateContent($name, $presets[$preset]['type'] ?? 'simple', $presets[$preset]);
    }

    /**
     * Ensure the view directory exists.
     *
     * @param  string  $path
     * @return void
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    /**
     * Set file permissions.
     *
     * @param  string  $path
     * @param  array  $options
     * @return void
     */
    protected function setFilePermissions(string $path, array $options): void
    {
        $permissions = $this->resolvePermissions($options);

        chmod($path, octdec($permissions['mode']));

        if (function_exists('chown') && isset($permissions['owner'])) {
            chown($path, $permissions['owner']);
        }

        if (function_exists('chgrp') && isset($permissions['group'])) {
            chgrp($path, $permissions['group']);
        }
    }

    /**
     * Resolve file permissions from options.
     *
     * @param  array  $options
     * @return array
     */
    public function resolvePermissions(array $options): array
    {
        $defaults = $this->config->get('custom-view.default_permissions');

        return [
            'mode' => $options['chmod'] ?? $defaults['chmod'] ?? '644',
            'owner' => $options['owner'] ?? $defaults['owner'] ?? null,
            'group' => $options['group'] ?? $defaults['group'] ?? null,
        ];
    }

    /**
     * Validate section name.
     *
     * @param  string  $name
     * @return void
     */
    public function validateSectionName(string $name): void
    {
        if (preg_match('([^A-Za-z0-9_-])', $name)) {
            throw new InvalidArgumentException('Section name contains invalid characters.');
        }
    }

    /**
     * Validate component name.
     *
     * @param  string  $name
     * @return void
     */
    public function validateComponentName(string $name): void
    {
        if (preg_match('([^A-Za-z0-9_-])', $name)) {
            throw new InvalidArgumentException('Component name contains invalid characters.');
        }
    }

    /**
     * Get default values from config.
     *
     * @return array
     */
    public function getDefaultValues(): array
    {
        return [
            'type' => $this->config->get('custom-view.default_type', 'simple'),
            'permissions' => $this->config->get('custom-view.default_permissions', []),
            'sections' => $this->config->get('custom-view.default_sections', []),
            'components' => $this->config->get('custom-view.default_components', []),
        ];
    }

    /**
     * Create nested directories if they don't exist.
     *
     * @param  string  $path
     * @return void
     */
    public function createNestedDirectories(string $path): void
    {
        $directory = dirname($this->getViewPath($path));

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}