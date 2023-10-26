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

    public function getSingle($id)
    {
        $model = $this->model->find($id);
        if ($model === null) {
            throw new \Exception('Recurso pesquisado não existe');
        }
        return $model;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        $model->delete();
    }



}

