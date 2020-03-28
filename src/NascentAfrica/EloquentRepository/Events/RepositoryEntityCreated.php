<?php

namespace NascentAfrica\EloquentRepository\Events;


/**
 * Class RepositoryEntityCreated
 *
 * @package NascentAfrica\EloquentRepository\Events
 * @author Anitche Chisom
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "created";
}
