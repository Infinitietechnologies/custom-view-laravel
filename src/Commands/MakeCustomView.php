<?php

namespace Infinitietech\CustomView\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeCustomView extends Command
{
    protected $signature = 'make:custom-view 
                            {name : The base view name, e.g., "blog"} 
                            {--type= : The type of view. Options: simple, blank, resource} 
                            {--E|extend= : Layout template to extend (e.g., layouts.custom)} 
                            {--S|section=* : One or more sections to include in the view} 
                            {--C|component=* : One or more components to include in the view}
                            {--M|manage= : Manage operation: delete or move}
                            {--from= : The source view name for moving a file}
                            {--to= : The destination view name for moving a file}
                            {--P|permission : Apply file permission settings}
                            {--chmod= : The chmod value (e.g., 644)}
                            {--owner= : File owner (e.g., www-data)}
                            {--group= : File group (e.g., www-data)}
                            {--T|template : Shorthand template option for common options}
                            {--force : Skip confirmation prompts}';

    protected $description = 'Generates, deletes, or moves a custom view file with a wide range of options.';

    protected $help = <<<EOT
This command generates, deletes, or moves a custom view file with numerous options.

Usage Examples:

  Generate a simple view:
    php artisan make:custom-view blog.index --type=simple --extend=layouts.app --section=content

  Generate resource views (index, show, create, edit):
    php artisan make:custom-view admin.tax --type=resource

  Delete a view:
    php artisan make:custom-view blog.index --manage=delete

  Move a view:
    php artisan make:custom-view dummy --manage=move --from=old-view --to=new-view

  Apply file permissions:
    php artisan make:custom-view blog.index --permission --chmod=644 --owner=www-data --group=www-data

For additional details, consult the documentation at: [Your Docs URL]

EOT;

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->option('type') ?? 'simple';
        $layout = $this->option('extend');
        $sections = $this->option('section') ?: [];
        $components = $this->option('component') ?: [];
        $manage = $this->option('manage');
        $from = $this->option('from');
        $to = $this->option('to');
        $applyPermission = $this->option('permission');
        $chmod = $this->option('chmod');
        $owner = $this->option('owner');
        $group = $this->option('group');
        $shorthand = $this->option('template');
        $force = $this->option('force');

        if ($manage) {
            return $this->handleManagement($manage, $name, $from, $to, $force);
        }

        if ($type === 'resource') {
            return $this->generateResourceFiles($name, $layout, $sections, $components, $shorthand, $applyPermission, $chmod, $owner, $group, $force);
        }

        return $this->createViewFile($name, $type, $layout, $sections, $components, $shorthand, $applyPermission, $chmod, $owner, $group, $force);
    }

    /**
     * Generate resource-specific view files (index, show, create, edit).
     *
     * @param string      $baseName
     * @param string|null $layout
     * @param array       $sections
     * @param array       $components
     * @param string|null $shorthand
     * @param bool        $applyPermission
     * @param string|null $chmod
     * @param string|null $owner
     * @param string|null $group
     * @param bool        $force
     * @return void
     */
    private function generateResourceFiles($baseName, $layout, $sections, $components, $shorthand, $applyPermission, $chmod, $owner, $group, $force)
    {
        $actions = ['index', 'show', 'create', 'edit'];

        foreach ($actions as $action) {
            $viewName = "{$baseName}.{$action}";
            $this->createViewFile($viewName, 'resource', $layout, $sections, $components, $shorthand, $applyPermission, $chmod, $owner, $group, $force);
        }
    }

    /**
     * Create a single view file.
     *
     * @param string      $name
     * @param string      $type
     * @param string|null $layout
     * @param array       $sections
     * @param array       $components
     * @param string|null $shorthand
     * @param bool        $applyPermission
     * @param string|null $chmod
     * @param string|null $owner
     * @param string|null $group
     * @param bool        $force
     * @return void
     */
    private function createViewFile($name, $type, $layout, $sections, $components, $shorthand, $applyPermission, $chmod, $owner, $group, $force)
    {
        $viewPath = $this->getViewPath($name);
        $content = $this->generateContent($name, $type, $layout, $sections, $components, $shorthand);

        if (File::exists($viewPath) && !$force && !$this->confirm("The view '{$name}' already exists. Overwrite?")) {
            $this->info("Skipping '{$name}'.");
            return;
        }

        try {
            File::ensureDirectoryExists(dirname($viewPath));
            File::put($viewPath, $content);
            $this->info("View '{$name}' created successfully at {$viewPath}.");
        } catch (\Exception $e) {
            $this->error("Error creating view: " . $e->getMessage());
            return;
        }

        if ($applyPermission) {
            $this->applyPermissions($viewPath, $chmod, $owner, $group);
        }
    }

    /**
     * Handles management operations: delete or move.
     *
     * @param string $manage
     * @param string $name
     * @param string|null $from
     * @param string|null $to
     * @param bool $force
     * @return void
     */
    private function handleManagement($manage, $name, $from, $to, $force)
    {
        if ($manage === 'delete') {
            return $this->deleteView($name, $force);
        } elseif ($manage === 'move') {
            return $this->moveView($from, $to, $force);
        }

        $this->error("Unsupported manage operation '{$manage}'. Use delete or move.");
    }

    /**
     * Delete the specified view file.
     *
     * @param string $name
     * @param bool $force
     * @return void
     */
    private function deleteView($name, $force)
    {
        $viewPath = $this->getViewPath($name);

        if (!File::exists($viewPath)) {
            $this->error("View '{$name}' does not exist.");
            return;
        }

        if (!$force && !$this->confirm("Delete view '{$name}' permanently?")) {
            $this->info("Deletion canceled.");
            return;
        }

        try {
            File::delete($viewPath);
            $this->info("View '{$name}' deleted.");
        } catch (\Exception $e) {
            $this->error("Error deleting view: " . $e->getMessage());
        }
    }

    /**
     * Move (rename) a view file from one name to another.
     *
     * @param string $from
     * @param string $to
     * @param bool $force
     * @return void
     */
    private function moveView($from, $to, $force)
    {
        if (!$from || !$to) {
            $this->error("For 'move', both --from and --to are required.");
            return;
        }

        $fromPath = $this->getViewPath($from);
        $toPath = $this->getViewPath($to);

        if (!File::exists($fromPath)) {
            $this->error("Source view '{$from}' does not exist.");
            return;
        }

        try {
            File::ensureDirectoryExists(dirname($toPath));
            File::move($fromPath, $toPath);
            $this->info("Moved view from '{$from}' to '{$to}'.");
        } catch (\Exception $e) {
            $this->error("Error moving view: " . $e->getMessage());
        }
    }

    /**
     * Get the full file path for a given view name.
     *
     * @param string $name
     * @return string
     */
    private function getViewPath($name)
    {
        return resource_path('views/' . str_replace('.', '/', $name) . '.blade.php');
    }

    /**
     * Generate the content for the view file.
     *
     * @param string      $name
     * @param string      $type
     * @param string|null $layout
     * @param array       $sections
     * @param array       $components
     * @param string|null $shorthand
     * @return string
     */
    private function generateContent($name, $type, $layout, $sections, $components, $shorthand)
    {
        $content = '';

        if ($shorthand) {
            $content .= "<!-- Using shorthand template: '{$shorthand}' -->\n";
        }

        if ($layout) {
            $content .= "@extends('{$layout}')\n";
        }

        // Customize content based on view type.
        if ($type === 'resource') {
            // Extract the final segment to determine the action.
            $segments = explode('.', $name);
            $action = strtolower(end($segments));
            $content .= "<!-- Resource View ({$action}) for: {$name} -->\n";

            // Provide basic boilerplate per resource action.
            switch ($action) {
                case 'index':
                    $content .= "<h1>" . ucwords(str_replace('.', ' ', $name)) . " List</h1>\n";
                    break;
                case 'create':
                    $content .= "<h1>Create " . ucwords(str_replace('.', ' ', $name)) . "</h1>\n<form><!-- form fields here --></form>\n";
                    break;
                case 'edit':
                    $content .= "<h1>Edit " . ucwords(str_replace('.', ' ', $name)) . "</h1>\n<form><!-- form fields here --></form>\n";
                    break;
                case 'show':
                    $content .= "<h1>Show Details</h1>\n<div><!-- details here --></div>\n";
                    break;
            }
        } else {
            $content .= "<!-- View: {$name} -->\n";
        }

        foreach ($sections as $section) {
            $content .= "\n@section('{$section}')\n    {{-- Content for {$section} --}}\n@endsection\n";
        }

        foreach ($components as $component) {
            $content .= "\n@include('components.{$component}')\n";
        }

        return $content;
    }

    /**
     * Apply file permissions.
     *
     * @param string      $viewPath
     * @param string|null $chmod
     * @param string|null $owner
     * @param string|null $group
     * @return void
     */
    private function applyPermissions($viewPath, $chmod, $owner, $group)
    {
        try {
            if ($chmod) {
                chmod($viewPath, octdec($chmod));
            }
            if ($owner && function_exists('chown')) {
                chown($viewPath, $owner);
            }
            if ($group && function_exists('chgrp')) {
                chgrp($viewPath, $group);
            }
            $this->info("Permissions updated for: {$viewPath}");
        } catch (\Exception $e) {
            $this->error("Error applying permissions: " . $e->getMessage());
        }
    }
}
