<?php

namespace Vendor\CustomView\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\InteractsWithTime;
use Orchestra\Testbench\Concerns\Testing;
use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\CustomView\CustomViewServiceProvider;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;

/**
 * Base TestCase for Laravel Custom View Generator
 *
 * @author infinitietech05 <your.email@infinitietechnologies.com>
 * @package infinitietechnologies/custom-view-laravel
 * @created 2025-04-09 06:20:34
 */
class TestCase extends Orchestra
{
    use Testing,
        InteractsWithAuthentication,
        InteractsWithConsole,
        InteractsWithContainer,
        InteractsWithDatabase,
        InteractsWithDeprecationHandling,
        InteractsWithExceptionHandling,
        InteractsWithSession,
        InteractsWithTime,
        InteractsWithViews,
        MakesHttpRequests;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create views directory if it doesn't exist
        if (!File::exists($this->getResourcePath('views'))) {
            File::makeDirectory($this->getResourcePath('views'), 0755, true);
        }
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            CustomViewServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Configure the environment
        $app['config']->set('custom-view.default_type', 'simple');
        $app['config']->set('custom-view.extensions', ['.blade.php']);
    }

    /**
     * Get the path to a resource file.
     *
     * @param string $path
     * @return string
     */
    protected function getResourcePath(string $path = ''): string
    {
        return __DIR__ . '/tmp/resources/' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the view path for a given view.
     *
     * @param string $view
     * @return string
     */
    protected function getViewPath(string $view): string
    {
        return $this->getResourcePath('views/' . str_replace('.', '/', $view) . '.blade.php');
    }

    /**
     * Assert that a view file exists.
     *
     * @param string $path
     * @return void
     */
    protected function assertViewExists(string $path): void
    {
        $this->assertFileExists($this->getViewPath($path));
    }

    /**
     * Assert that a view file contains specific content.
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    protected function assertViewContains(string $path, string $content): void
    {
        $viewPath = $this->getViewPath($path);
        $this->assertFileExists($viewPath);
        $this->assertStringContainsString($content, file_get_contents($viewPath));
    }

    /**
     * Assert that a view extends a specific layout.
     *
     * @param string $path
     * @param string $layout
     * @return void
     */
    protected function assertViewExtends(string $path, string $layout): void
    {
        $this->assertViewContains($path, "@extends('" . $layout . "')");
    }

    /**
     * Assert that a view has a specific section.
     *
     * @param string $path
     * @param string $section
     * @return void
     */
    protected function assertViewHasSection(string $path, string $section): void
    {
        $this->assertViewContains($path, "@section('" . $section . "')");
    }

    /**
     * Assert that a view includes a specific component.
     *
     * @param string $path
     * @param string $component
     * @return void
     */
    protected function assertViewHasComponent(string $path, string $component): void
    {
        $this->assertViewContains($path, "<x-" . $component);
    }

    /**
     * Clean up after tests.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Clean up temporary files
        if (File::exists(__DIR__ . '/tmp')) {
            File::deleteDirectory(__DIR__ . '/tmp');
        }

        parent::tearDown();
    }
}