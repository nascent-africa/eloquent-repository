<?php

namespace NascentAfrica\EloquentRepository;


use Illuminate\Support\ServiceProvider;

/**
 * Class LumenRepositoryServiceProvider
 *
 * @package NascentAfrica\EloquentRepository
 * @author Anitche Chisom
 */
class LumenRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(\Console\RepositoryCommand::class);
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
}
