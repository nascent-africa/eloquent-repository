<?php

namespace Test;

use Illuminate\Database\Eloquent\Model;

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
