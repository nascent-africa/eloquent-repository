<?php

namespace NascentAfrica\EloquentRepository\Contracts;

use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;

/**
 * Interface CriteriaInterface
 *
 * @package NascentAfrica\EloquentRepository\Contracts
 * @author Anitche Chisom
 */
interface CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository);
}
