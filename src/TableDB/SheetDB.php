<?php

include_once 'init.php';

/**
 * SheetDB is a simple api wrapper for Jotform Tables.
 * It provides a simple interface to access and manage Jotform Tables like databases.
 *
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */


final class SheetDB
{
    const SHEETDB_VERSION = '1.0.0';
    const SHEETDB_VERSION_DATE = '2022-06-30';
    const SHEETDB_DB_PREFIX = '';
    const SHEETDB_TABLE_PREFIX = '';
    const SHEETDB_DB_NAME = "";
    const SHEETDB_ZERO_CONFIG = true;
    const JF_API_KEY = '';
    public static $cache;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
    }

    /**
     * Seed the tables with the default data.
     * @return [type]
     */
    public static function seed()
    {
        $seeders = [];
        foreach (glob("SheetDB/Database/Seeders/*.php") as $filename) {
            include_once $filename;
            $className = str_replace('.php', '', basename($filename));
            $seeders[] = new $className();
        }
        return Seeder::runAll($seeders);
    }


    /**
     * @param string $db
     *
     * @return Query
     */
    public static function use(string $db): Query
    {
        return (new Query())
        ->use($db);
    }

    /**
     * @param string ...$columns
     *
     * @return Query
     */
    public static function select(string ...$columns): Query
    {
        return (new Query())->select(...$columns);
    }

    /**
     * @param string $tableName
     *
     * @return Query
     */
    public static function from(string $tableName): Query
    {
        return (new Query())->from($tableName);
    }

    /**
     * @param string $tableName
     *
     * @return Query
     */
    public static function table(string $tableName): Query
    {
        return (new Query())->table($tableName);
    }

    /**
     * @return Query
     */
    public static function tables(): Query
    {
        return (new Query())->tables();
    }

    /**
     * @param string $tableName
     * @param mixed $tableBuilder
     * @param string|null $db
     * This function can hit the limits of the Jotform API. So, it is recommended to create a new table from jotform.com
     * instead of using this function.
     * @return [type]
     */
    public static function create(string $tableName, $tableBuilder, string $db = null)
    {
        return Schema::create($tableName, function (TableBuilder &$table) {
            // This is an example of a table builder. You can use this template to create a table.
            $table->text("Textbox Field");
            $table->boolean("test-boolean-field");
        }, $db);
    }

    /**
     * @return Jotform
     */
    public static function api(): Jotform
    {
        return new Jotform(self::JF_API_KEY);
    }

    /**
     * @return Cache
     */
    public static function cache(): Cache
    {
        if (self::$cache === null) {
            self::$cache = new Cache();
        }
        return self::$cache;
    }
}
