<?php

namespace App;

use NascentAfrica\EloquentRepository\Contracts\CriteriaInterface;
use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;

/**
 * Description of Criteria
 *
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
