<?php

namespace Platform\Customer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Platform\Core\PlatformCore;
use Platform\Core\Routing\ModuleRouter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/customer.php', 'customer');
    }

    public function boot(): void
    {
        if (
            config()->has('customer.routing') &&
            config()->has('customer.navigation') &&
            Schema::hasTable('modules')
        ) {
            PlatformCore::registerModule([
                'key'        => 'customer',
                'title'      => 'Betriebe',
                'routing'    => config('customer.routing'),
                'guard'      => config('customer.guard'),
                'navigation' => config('customer.navigation'),
                'sidebar'    => config('customer.sidebar'),
            ]);
        }

        if (PlatformCore::getModule('customer')) {
            ModuleRouter::group('customer', function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/customer.php' => config_path('customer.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'customer');

        $this->registerLivewireComponents();
    }

    /**
     * Datei src/Livewire/Company/Index.php → Alias customer.company.index
     */
    protected function registerLivewireComponents(): void
    {
        $basePath = __DIR__ . '/Livewire';
        $baseNamespace = 'Platform\\Customer\\Livewire';
        $prefix = 'customer';

        if (!is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $classPath = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $class = $baseNamespace . '\\' . $classPath;

            if (!class_exists($class)) {
                continue;
            }

            $aliasPath = str_replace(['\\', '/'], '.', Str::kebab(str_replace('.php', '', $relativePath)));
            $alias = $prefix . '.' . $aliasPath;

            Livewire::component($alias, $class);
        }
    }
}
