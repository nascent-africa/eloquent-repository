<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test;

use Illuminate\Foundation\Testing\TestCase;


/**
 * Description of BaseRepositoryTestCase
 *
 * @author Anitche Chisom
 */
abstract class BaseTestCase extends TestCase
{
    /**
    * Boots the application.
    *
    * @return \Illuminate\Foundation\Application
    */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(\NascentAfrica\EloquentRepository\RepositoryServiceProvider::class);

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    /**
    * Setup DB before each test.
    *
    * @return void
    */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('database.default','sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->migrate();
    }

    /**
    * run package database migrations
    *
    * @return void
    */
    public function migrate()
    {

        (new Migrations\CreateUsersTable)->up();
    }
}
