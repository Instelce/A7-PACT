<?php

namespace app\core;


class DBQueryBuilder
{
    public DBModel $model;

    public array $select = [];
    public array $selectCalculated = [];
    public array $filters = [];
    public array $filtersCalculated = [];
    public array $search = [];
    public array $joins = [];
    public array $order_by = [];
    public array $group_by = [];
    public ?int $limit = null;
    public ?int $offset = null;

    public ?\PDOStatement $statement;

    public function __construct(DBModel $model)
    {
        $this->model = $model;
    }

    /**
     * Make the request to the database
     */
    public function make()
    {
        $tableName = $this->model->tableName();

        // Add selected attributes or select all
        if (!empty($this->select)) {
            $select = implode(", ", array_map(fn($attr) => $attr, $this->select));
        } else {
            $select = "$tableName.*";
        }

        // Add calculated attributes
        if (!empty($this->selectCalculated)) {
            $select .= ", " . implode(", ", $this->selectCalculated);
        }

        $sql = "SELECT $select FROM $tableName";

        if (!empty($this->joins)) {
            $sql .= " " . implode(" ", $this->joins);
        }

        if (!empty($this->filters)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function ($sets) {
                [$attr, $value, $op, $specialKey] = $sets;

                $tableName = $this->model->tableName();
                $operation = '=';
                $attrBindKey = $attr;

                if ($specialKey) {
                    $attrBindKey = $specialKey;
                }

                switch ($op) {
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                    case '!=':
                    case 'is':
                    case 'IS':
                        $operation = $op;
                        break;
                    case '!':
                        $operation = '!=';
                        break;
                }

                // Check for double __ in the attribute name
                if (strpos($attr, '__') !== false) {
                    $attrName = str_replace('__', '.', $attr);
                    $modelName = implode('', array_map(fn($part) => ucfirst($part), explode('_', explode('__', $attr)[0])));

                    return "$attrName = :$attr";
                } else {
                    return "$tableName.$attr $operation :$attrBindKey";
                }
            }, $this->filters));
        }

        if (!empty($this->search)) {
            if (!empty($this->filters)) {
                $sql .= " AND ";
            } else {
                $sql .= " WHERE ";
            }

            $sql .= implode(" OR ", array_map(function($attr) {
                $tableName = $this->model->tableName();

                // Check for double __ in the attribute name
                if (str_contains($attr, '__')) {
                    $attrName = str_replace('__', '.', $attr);
                    $modelName = implode('', array_map(fn($part) => ucfirst($part), explode('_', explode('__', $attr)[0])));

                    return "LOWER($attrName) LIKE :$attr";
                } else {
                    if (str_contains($attr, 'created_at')) {
                        return "LOWER(cast($tableName.$attr as TEXT)) LIKE :$attr";
                    }

                    return "LOWER($tableName.$attr) LIKE :$attr";
                }
            }, array_keys($this->search)));
        }

        // Add group by
        if (!empty($this->group_by)) {
            $sql .= " GROUP BY " . implode(", ", $this->group_by);

            // Add having
            if (!empty($this->filtersCalculated)) {
                $sql .= " HAVING " . implode(" AND ", $this->filtersCalculated);
            }
        }

        // Add order by
        if (!empty($this->order_by)) {
            $sql .= " ORDER BY ";

            foreach ($this->order_by as $i => $attr) {
                if ($i > 0) {
                    $sql .= ", ";
                }

                $sql .= $tableName . "." . (str_starts_with($attr, '-') ? substr($attr, 1) : $attr);
            }

            if (str_starts_with($this->order_by[0], '-')) {
                $sql .= " DESC";
            }
        }

        if ($this->limit) {
            $sql .= " LIMIT $this->limit";
        }

        if ($this->offset) {
            $sql .= " OFFSET $this->offset";
        }

        $this->statement = Application::$app->db->pdo->prepare($sql);

//        echo "<pre>";
//        var_dump($this->statement->queryString);
//        echo "</pre>";

        foreach ($this->filters as $sets) {
            [$attr, $value, $op, $specialKey] = $sets;
            if ($specialKey) {
                $attr = $specialKey;
            }
            $this->statement->bindValue(":$attr", $value);
        }

        foreach ($this->search as $key => $value) {
            $this->statement->bindValue(":$key", '%' . strtolower($value) . '%');
        }

        $this->statement->execute();

        return $this->statement->fetchAll(\PDO::FETCH_CLASS, $this->model::class);
    }

    /**
     * Select the given attributes
     *
     * @param array $attrs `['id', 'title', 'content']`
     */
    public function select(array $attrs): DBQueryBuilder
    {
        $this->select = $attrs;
        return $this;
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
     * @param array $where `['id' => 1, 'rating' => 5, ['offline', 20, '!'], ['likes', 30, '>', 'special_key'], ['likes', 30, '<']]`
     */
    public function filters(array $where): DBQueryBuilder
    {
        foreach ($where as $attr => $filter) {
            if (is_array($filter)) {
                if (count($filter) == 3) {
                    $filter[] = '';
                }

                $this->filters[] = $filter;
            } else {
                $this->filters[] = [$attr, $filter, ''];
            }
        }

        return $this;
    }

    /**
     * Add a filter
     */
    public function filter(string $attr, $value, $op = '', $specialKey = ''): DBQueryBuilder
    {
        $this->filters[] = [$attr, $value, $op, $specialKey];
        return $this;
    }

    /**
     * Search the results by the given attributes
     *
     * @param array $where `['title' => "the c", 'description' => "lorem"]`
     */
    public function search(array $where): DBQueryBuilder
    {
        $this->search = $where;
        return $this;
    }

    /**
     * Join the results with the given model
     *
     * @param DBModel $model The model to join with
     */
    public function join(DBModel $model): DBQueryBuilder
    {
        $table = $model->tableName();
        $pk = $model->pk();
        $this->joins[] = "INNER JOIN $table ON $table.$pk = " . $this->model->tableName() . "." . $table . "_" . $pk;
        return $this;
    }

    public function joinString(string $join): DBQueryBuilder
    {
        $this->joins[] = $join;
        return $this;
    }

    /**
     * Group the results by the given attributes
     */
    public function group_by(array $attrs): DBQueryBuilder
    {
        $this->group_by = $attrs;
        return $this;
    }

    /**
     * Add a calculated attribute to the results
     *
     * Should be used with group_by method
     *
     * @param string $attr The name of the attribute
     * @param string $calculation The calculation to perform
     *
     * @example $query->calculate('likes', 'COUNT(*)')
     * @example $query->calculate('average_rating', 'AVG(rating)')
     */
    public function calculate(string $attr, string $calculation): DBQueryBuilder
    {
        $this->selectCalculated[] = "$calculation AS $attr";
        return $this;
    }

    /**
     * Add a calculated filter to the results
     *
     * Should be used with group_by method
     *
     * @example $query->having('likes > 10')
     * @example $query->having('average_rating < 4')
     *
     * @param string $calculation The calculation to perform
     */
    public function having(string $calculation): DBQueryBuilder
    {
        $this->filtersCalculated[] = $calculation;
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

    /**
     * Get the query string
     */
    public function queryString(): string
    {
        return $this->statement ? $this->statement->queryString : '';
    }
}