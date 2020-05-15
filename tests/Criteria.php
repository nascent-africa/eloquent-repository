<?php

namespace Test;

use NascentAfrica\EloquentRepository\Contracts\CriteriaInterface;
use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;

/**
 * Class Criteria
 *
 * @package Test
 * @author Anitche Chisom
 */
class Criteria implements CriteriaInterface
{

    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('id','=', 2 );
        return $model;
    }
}
