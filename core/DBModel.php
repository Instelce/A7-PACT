<?php
// map db table to dbmodel class

namespace app\core;

/**
 * Class DBModel, represents a database model
 */
abstract class DBModel extends Model
{
    abstract public static function tableName(): string;

    abstract public function attributes(): array;

    public function updateAttributes(): array
    {
        return $this->attributes();
    }

    public static function pk(): string
    {
        return 'id';
    }

    /**
     * Save the model to the database
     */
    public function save(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        // If a model has created_at or updated_at class attribute are not set, set them to the current time
        if (property_exists($this, 'created_at') && !$this->created_at) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date('Y-m-d H:i:s');
        }

        // Add the created_at and updated_at attributes to the model
        if (property_exists($this, 'created_at') && !in_array('created_at', $attributes)) {
            $attributes[] = 'created_at';
            $params[] = ':created_at';
        }
        if (property_exists($this, 'updated_at') && !in_array('updated_at', $attributes)) {
            $attributes[] = 'updated_at';
            $params[] = ':updated_at';
        }

        // Prepare the statement
        $statement = self::prepare("INSERT INTO $tableName (" . implode(",", $attributes) . ") VALUES (" . implode(",", $params) . ")");

        // Load the model values into the statement
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        // Bind created_at and updated_at values
        if (property_exists($this, 'created_at')) {
            $statement->bindValue(':created_at', $this->created_at);
        }
        if (property_exists($this, 'updated_at')) {
            $statement->bindValue(':updated_at', $this->updated_at);
        }

        $result = $statement->execute();

        if ($result && property_exists($this, 'id')) {
            $this->{static::pk()} = Application::$app->db->pdo->lastInsertId();
        }

        return true;
    }

    /**
     * Update the model in the database
     */
    public function update()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $set = array_map(fn($attr) => "$attr = :$attr", $attributes);
        $pk = static::pk();
        $pkValue = $this->{$pk};

        // Add updated_at to set
        if (property_exists($this, 'updated_at')) {
            $set[] = 'updated_at = :updated_at';
        }

        // Prepare the statement
        $statement = self::prepare("UPDATE $tableName SET " . implode(",", $set) . " WHERE $pk = $pkValue;");

        // Load the model values into the statement
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date('Y-m-d');
            $statement->bindValue(':updated_at', $this->updated_at);
        }

        $statement->execute();
    }

    /**
     * Delete the model from the database
     */
    public function destroy()
    {
        $tableName = static::tableName();
        $pk = static::pk();
        $pkValue = $this->{$pk};

        $statement = self::prepare("DELETE FROM $tableName WHERE $pk = $pkValue;");
        $statement->execute();

        return true;
    }

    /**
     * Find one model in the database with the given where clause
     */
    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);

        $whereStr = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE " . $whereStr);

        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }

        $statement->execute();

        return $statement->fetchObject(static::class);
    }

    /**
     * Find one model in the database with the given primary key
     */
    public static function findOneByPk($pkValue)
    {
        $tableName = static::tableName();
        $pk = static::pk();

        $statement = self::prepare("SELECT * FROM $tableName WHERE $pk = $pkValue");
        $statement->execute();

        return $statement->fetchObject(static::class);
    }

    /**
     * Find all models in the database with the given where clause
     */
    public static function find($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);

        $whereStr = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE " . $whereStr);
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    /**
     * Find all models in the database
     */
    public static function all()
    {
        $tableName = static::tableName();
        $statement = self::prepare("SELECT * FROM $tableName");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public static function query(): DBQueryBuilder
    {
        return new DBQueryBuilder(new static());
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public function toJson()
    {
        $res = [];
        $attributes = get_class_vars(get_class($this));
        unset($attributes['errors']);

        foreach (array_keys($attributes) as $key) {
            $res[$key] = $this->{$key};
        }

        if (property_exists($this, 'created_at')) {
            $res['created_at'] = $this->created_at;
        }

        if (property_exists($this, 'updated_at')) {
            $res['updated_at'] = $this->updated_at;
        }

        return $res;
    }
}