<?php

namespace App\Repositories;

abstract class Repository
{
    private $model;
    public function setModel($modelClass)
    {
        $this->model = app($modelClass);
        return $this;
    }

    public function getAll()
    {
        $model = $this->model->All();

        if ($model->isEmpty()) {
            throw new \Exception('Recurso pesquisado não existe');
        }
        return $model;
    }

    // $users = User::all();
    // return response()->json($users, 200);
    // // dd($users -> toArray());
}

