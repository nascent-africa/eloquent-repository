<?php

namespace NascentAfrica\EloquentRepository;


use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 *
 * @package NascentAfrica\EloquentRepository
 * @author Anitche Chisom
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->offerPublishing();

        $this->configure();

        $this->registerResources();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Setup the resource publishing groups for Eloquent Repository.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../config/repository.php' => config_path('nascent-africa/repository.php')
        ]);
    }

    /**
     * Register the Eloquent Repository Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\BindingCommand::class,
                Console\CriteriaMakeCommand::class,
                Console\InterfaceMakeCommand::class,
                Console\ModelMakeCommand::class,
                Console\ProviderMakeCommand::class,
                Console\RepositoryMakeCommand::class
            ]);
        }
    }

    /**
     * Setup the configuration for Eloquent Repository.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/repository.php', 'nascent-africa.repository');
    }

    /**
     * Register the Eloquent Repository resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'na_repository');
    }
}
