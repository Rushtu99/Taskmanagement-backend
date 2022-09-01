<?php
namespace App\Http\Controllers;

use App\ResetsPasswords;

class PasswordController extends ApiController
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->broker = 'users';
    }
}