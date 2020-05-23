<?php

namespace Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 *
 * @package Test
 * @author Anitche Chisom
 */
class User extends Model {

    protected $fillable = [
        'name', 'email', 'password'
    ];
}

/**
 * Class SoftDeleteUser
 *
 * @package Test
 * @author Anitche Chisom
 */
class SoftDeleteUser extends Model {

    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password'
    ];
}
