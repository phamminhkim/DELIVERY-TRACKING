<?php

namespace App\Repositories\Abstracts;

use App\User;

abstract class RepositoryAbs
{
    protected $current_user;
    protected $request;
    protected $data;
    protected $message;
    protected $errors;

    public function __construct($request)
    {
        if (Auth()->user())
            $this->current_user = User::find(Auth()->user()->id);

        $this->request = $request;
        $this->data = $request->all();
    }

    public function getErrors()
    {
        return $this->errors;
    }
    public function getMessage()
    {
        return $this->message;
    }
}
