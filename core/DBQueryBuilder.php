<?php

namespace app\core;

class DBQueryBuilder
{
    public Model $model;

    /**
     * @var 'select' | 'create' | 'update' | 'delete'
     */
    public string $action = 'select';

    public array $filters = [];
    public array $order_by = [];
    public int $limit = 0;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Make the request to the database
     */
    public function make()
    {
        $tableName = $this->model->tableName();
        $attributes = $this->model->attributes();

        $sql = match ($this->action) {
            'select' => "SELECT * FROM $tableName",
            'create' => "INSERT INTO $tableName (" . implode(",", $attributes) . ") VALUES (" . implode(",", array_map(fn($attr) => ":$attr", $attributes)) . ")",
            'update' => "UPDATE $tableName SET " . implode(",", array_map(fn($attr) => "$attr = :$attr", $this->model->updateAttributes())) . " WHERE id = :id",
            'delete' => "DELETE FROM $tableName WHERE id = :id",
        };

        if (!empty($this->filters)) {
            $sql .= " WHERE " . implode(" AND ", array_map(fn($attr) => "$attr = :$attr", array_keys($this->filters)));
        }

        if (!empty($this->order_by)) {
            $sql .= " ORDER BY " . implode(",", $this->order_by);
        }

        if ($this->limit) {
            $sql .= " LIMIT $this->limit";
        }

        $statement = self::prepare($sql);

        foreach ($this->filters as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        if ($this->action === 'create' || $this->action === 'update') {
            foreach ($attributes as $attribute) {
                $statement->bindValue(":$attribute", $this->model->{$attribute});
            }
        }

        if ($this->action === 'update' || $this->action === 'delete') {
            $statement->bindValue(":id", $this->model->pk());
        }

        $statement->execute();

        return $this->action === 'select' ? $statement->fetchAll($this->model::class) : null;
    }

    /**
     * Order the results by the given attributes
     *
     * @param array $attrs `['id', '-rating']`
     */
    public function order_by(array $attrs): DBQueryBuilder
    {
        $this->order_by = $attrs;
        return $this;
    }

    /**
     * Filter the results by the given attributes
     *
     * @param array $where `['id' => 1, 'rating' => 5]`
     */
    public function filters(array $where): DBQueryBuilder
    {
        $this->filters = $where;
        return $this;
    }

    /**
     * Limit the number of results
     *
     * @param int $limit The number of results to return
     */
    public function limit(int $limit): DBQueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    public function create(): DBQueryBuilder
    {
        $this->action = 'create';
        return $this;
    }

    public function update(): DBQueryBuilder
    {
        $this->action = 'update';
        return $this;
    }

    public function delete(): DBQueryBuilder
    {
        $this->action = 'delete';
        return $this;
    }
}