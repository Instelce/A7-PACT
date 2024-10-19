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

    public function updateAttributes(): array {
        return $this->attributes();
    }

    public static function pk(): string
    {
        return 'id';
    }

    /**
     * Save the model to the database
     */
    public function save(): true
    {
        $tableName = $this->tableName();
        $attributes = $this->updateAttributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        // Prepare the statement
        $statement = self::prepare("INSERT INTO $tableName (".implode(",", $attributes).") VALUES (".implode(",", $params).")");

        // Load the model values into the statement
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    /**
     * Update the model in the database
     */
    public function update() {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $set = array_map(fn($attr) => "$attr = :$attr", $attributes);
        $pk = static::pk();
        $pkValue = $this->{$pk};

        // Prepare the statement
        $statement = self::prepare("UPDATE $tableName SET ".implode(",", $set)." WHERE $pk = $pkValue;");

        // Load the model values into the statement
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
    }

    /**
     * Delete the model from the database
     */
    public function destroy() {
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

        $whereStr = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE ".$whereStr);

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

        $whereStr = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE ".$whereStr);
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

        return $statement->fetchAll();
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}