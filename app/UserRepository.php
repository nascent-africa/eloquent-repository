<?php

namespace App;

use NascentAfrica\EloquentRepository\BaseRepository;
use NascentAfrica\EloquentRepository\Contracts\RepositoryInterface;

/**
 * Description of UserRepository
 *
 * @author Anitche Chisom
 */
class UserRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * Get model path
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }
}
