<?php

namespace App\Repositories;

abstract class Repository
{
    protected static $model;

    public function Model()
    {
        return app(static::$model);
    }

    public function findAll()
    {
        $model = self::Model()::all();
        if ($model->isEmpty()) {
            throw new \Exception('Nenhum dado encontrado', 404);
        }
        return  $model;
    }

    public function findById(int $id)
    {
        $model = self::Model()::find($id);
        if ($model == null) {
            throw new \Exception('Item solicitado não existe', 404);
        }
        return $model;
    }

    public function create(array $attributes = [])
    {
        return self::Model()::create($attributes);
    }

    public function update(int $id, array $attributes = [])
    {
        $model  = $this->findById($id);
        $update = $model->update($attributes);

        if($update){
            return $model;
        }

        throw new \Exception('Erro inesperado', 500);
    }

    public  function delete(int $id)
    {

        $model   = $this->findById($id);
        $delete  = $model->delete();

        if($delete){
            return $delete;
        }

        throw new \Exception('Erro inesperado', 500);
    }


}

