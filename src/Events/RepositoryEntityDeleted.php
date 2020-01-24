<?php

namespace NascentAfrica\EloquentRepository\Events;


/**
 * Class RepositoryEntityDeleted
 *
 * @package NascentAfrica\EloquentRepository\Events
 * @author Anitche Chisom
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleted";
}
