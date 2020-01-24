<?php

namespace NascentAfrica\EloquentRepository\Events;


/**
 * Class RepositoryEntityUpdated
 *
 * @package NascentAfrica\EloquentRepository\Events
 * @author Anitche Chisom
 */
class RepositoryEntityUpdated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "updated";
}
