<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of User
 *
 * @author Anitche Chisom
 */
class User extends Model {
    protected $fillable = [
        'name', 'email', 'password'
    ];
}
