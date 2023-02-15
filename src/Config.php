<?php

class Config
{
    public static $items = [];

    public static function init()
    {
        if (empty(self::$items)) {
            echo __DIR__ . '/../../config/credentials.php';
            self::$items = include(__DIR__ . '/../../config/credentials.php');
        }

        return self::$items;
    }

    /**
     * Searches the $items array and returns the item
     *
     * @param   string  $item
     * @return  string
     */
    public static function get($key = null, $default = null)
    {
        return self::$items[$key] ?? $default;
    }
}
