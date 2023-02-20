<?php

class CustomModel extends Model
{
    protected static $tableName = "";
    protected static $tableID = "";

    public static function set($key, $value) {
        self::${$key} = $value;
    }
}
