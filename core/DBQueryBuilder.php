<?php

namespace app\core;

class DBQueryBuilder
{
    public Model $model;

    public array $filters = [];
    public array $order_by = [];
    public ?int $limit;
    public ?int $offset;

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

        $sql = "SELECT * FROM $tableName";

        if (!empty($this->filters)) {
            $sql .= " WHERE " . implode(" AND ", array_map(fn($attr) => "$attr = :$attr", array_keys($this->filters)));
        }

        if (!empty($this->order_by)) {
            $sql .= " ORDER BY " . implode(",", $this->order_by);
        }

        if ($this->limit) {
            $sql .= " LIMIT $this->limit";
        }

        if ($this->offset) {
            $sql .= " OFFSET $this->offset";
        }

        $statement = Application::$app->db->pdo->prepare($sql);

        foreach ($this->filters as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->model::class);
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
     * @param int | null $limit The number of results to return
     */
    public function limit(int $limit = null): DBQueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Offset the results
     *
     * @param int | null $offset The number of results to skip
     */
    public function offset(int $offset = null): DBQueryBuilder
    {
        $this->offset = $offset;
        return $this;
    }
}