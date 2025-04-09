<?php

namespace Vendor\CustomView\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeCustomView extends Command
{

    protected $signature = 'make:custom-view 
                            {name : The view name, e.g., "blog.index"} 
                            {--type= : The type of view. Options: simple, blank, resource} 
                            {--E|extend= : Layout template to extend (e.g. layouts.custom)} 
                            {--S|section=* : One or more sections to include in the view} 
                            {--C|component=* : One or more components to include in the view}
                            {--M|manage= : Manage operation: delete or move}
                            {--from= : The source view name for moving a file}
                            {--to= : The destination view name for moving a file}
                            {--P|permission : Apply file permission settings}
                            {--chmod= : The chmod value (e.g., 644)}
                            {--owner= : File owner (e.g., www-data)}
                            {--group= : File group (e.g., www-data)}
                            {--T|template : Shorthand template option for common options}';

    protected $description = 'Generates, deletes, or moves a custom view file with a wide range of options.';

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

        if ($manage) {
            return $this->handleManagement($manage, $name, $from, $to);
        }

        $content = $this->generateContent($name, $type, $layout, $sections, $components, $shorthand);
        $viewPath = resource_path('views/' . str_replace('.', '/', $name) . '.blade.php');

        if (File::exists($viewPath) && !$this->confirm("The view '{$name}' already exists. Overwrite?")) {
            $this->info("Operation aborted.");
            return 0;
        }

        try {
            File::ensureDirectoryExists(dirname($viewPath));
            File::put($viewPath, $content);
            $this->info("View '{$name}' created successfully at {$viewPath}.");
        } catch (\Exception $e) {
            $this->error("Error creating view: " . $e->getMessage());
            return 1;
        }

        if ($applyPermission) {
            $this->applyPermissions($viewPath, $chmod, $owner, $group);
        }

        return 0;
    }

    private function handleManagement(string $manage, string $name, ?string $from, ?string $to)
    {
        if (!function_exists('resource_path')) {
            /**
             * Get the path to the resources folder.
             *
             * @param  string  $path
             * @return string
             */
            function resource_path($path = '')
            {
                // Assume the current working directory is the base path.
                return getcwd() . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
            }
        }


        $viewPath = resource_path('views/' . str_replace('.', '/', $name) . '.blade.php');

        if ($manage === 'delete') {
            if (!File::exists($viewPath)) {
                $this->error("View '{$name}' does not exist.");
                return 1;
            }
            if (!$this->confirm("Delete view '{$name}' permanently?")) {
                $this->info("Deletion canceled.");
                return 0;
            }
            try {
                File::delete($viewPath);
                $this->info("View '{$name}' deleted.");
            } catch (\Exception $e) {
                $this->error("Error deleting view: " . $e->getMessage());
                return 1;
            }
        } elseif ($manage === 'move') {
            if (!$from || !$to) {
                $this->error("For 'move', both --from and --to are required.");
                return 1;
            }
            $fromPath = resource_path('views/' . str_replace('.', '/', $from) . '.blade.php');
            $toPath = resource_path('views/' . str_replace('.', '/', $to) . '.blade.php');
            if (!File::exists($fromPath)) {
                $this->error("Source view '{$from}' does not exist.");
                return 1;
            }
            try {
                File::ensureDirectoryExists(dirname($toPath));
                File::move($fromPath, $toPath);
                $this->info("Moved view from '{$from}' to '{$to}'.");
            } catch (\Exception $e) {
                $this->error("Error moving view: " . $e->getMessage());
                return 1;
            }
        } else {
            $this->error("Unsupported manage operation '{$manage}'. Use delete or move.");
            return 1;
        }
        return 0;
    }

    private function generateContent(string $name, string $type, ?string $layout, array $sections, array $components, ?string $shorthand)
    {
        $content = '';

        if ($shorthand) {
            $content .= "<!-- Using shorthand template: '{$shorthand}' -->\n";
        }

        if ($type === 'resource') {
            $content .= "<!-- Resource View for: {$name} -->\n";
            $content .= "<h1>" . ucwords(str_replace('.', ' ', $name)) . " CRUD</h1>\n";
        } elseif ($type === 'blank') {
            $content .= "<!-- Blank View -->\n";
        } else {
            $content .= "<!-- View: {$name} -->\n";
            if ($layout) {
                $content .= "@extends('{$layout}')\n";
            }
            foreach ($sections as $section) {
                $content .= "\n@section('{$section}')\n";
                $content .= "    {{-- Content for {$section} --}}\n";
                $content .= "@endsection\n";
            }
            foreach ($components as $component) {
                $content .= "\n@include('components.{$component}')\n";
            }
        }

        return $content;
    }

    private function applyPermissions(string $viewPath, ?string $chmod, ?string $owner, ?string $group)
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
