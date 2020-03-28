<?php

namespace NascentAfrica\EloquentRepository;


use Illuminate\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * @package NascentAfrica\EloquentRepository
 * @author Anitche Chisom
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Events\RepositoryEntityCreated::class => [
            \Listeners\CleanCacheRepository::class
        ],
        \Events\RepositoryEntityUpdated::class => [
            \Listeners\CleanCacheRepository::class
        ],
        \Events\RepositoryEntityDeleted::class => [
            \Listeners\CleanCacheRepository::class
        ]
    ];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        $events = app('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
