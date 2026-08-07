<?php

namespace Platform\Customer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Livewire;
use Platform\Core\PlatformCore;
use Platform\Core\Routing\ModuleRouter;
use Platform\Customer\Models\RiskAssessment;
use Platform\Customer\Models\Hazard;
use Platform\Customer\Models\Exposure;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/customer.php', 'customer');

        // Registry, in die Fachmodule ihre Betrieb→Patienten-Provider eintragen.
        $this->app->singleton(\Platform\Customer\Services\CompanyPatientRegistry::class);
    }

    public function boot(): void
    {
        Relation::morphMap([
            'customer_risk_assessment' => RiskAssessment::class,
            'customer_hazard'          => Hazard::class,
            'customer_exposure'        => Exposure::class,
        ]);

        if (
            config()->has('customer.routing') &&
            config()->has('customer.navigation') &&
            Schema::hasTable('modules')
        ) {
            PlatformCore::registerModule([
                'key'        => 'customer',
                'title'      => 'Kundenbetriebe',
                'group'      => 'praxis_admin',
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

        $this->registerOrganizationIntegration();

        $this->registerPatientNavigationLens();
    }

    /**
     * Bringt die „Betrieb"-Linse in die Patienten-Navigation ein (wenn patient da ist).
     */
    protected function registerPatientNavigationLens(): void
    {
        try {
            resolve(\Platform\Patient\Services\PatientNavigationRegistry::class)
                ->register(new \Platform\Customer\Navigation\BetriebNavigationLens());
        } catch (\Throwable $e) {
            // patient-Modul nicht verfügbar — ignorieren.
        }
    }

    /**
     * Registriert den EntityLinkProvider, damit Gefährdungsbeurteilungen am Betrieb-Org-Entity rendern.
     */
    protected function registerOrganizationIntegration(): void
    {
        try {
            resolve(\Platform\Organization\Services\EntityLinkRegistry::class)
                ->register(new \Platform\Customer\Organization\CustomerEntityLinkProvider());
        } catch (\Throwable $e) {
            // Organization-Modul nicht verfügbar — ignorieren.
        }
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
