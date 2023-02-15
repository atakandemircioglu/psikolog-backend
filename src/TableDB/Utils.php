<?php

include_once 'v1/lib/SheetDB/init.php';

/**
 * Includes common operations on SheetDB tables and columns.
 *
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */

class Utils
{
    /**
     * @param string $tableName
     * @param string|null $db
     *
     * @return string
     */
    public static function encodeTableName(string $tableName, string $db = null): string
    {
        $dbName = $db === null ? SheetDB::SHEETDB_DB_NAME : $db;
        return SheetDB::SHEETDB_DB_PREFIX
        . "_"
        . $dbName
        . "_"
        . SheetDB::SHEETDB_TABLE_PREFIX
        . "_" . $tableName;
    }

    /**
     * @param string $encodedTableName
     * @param string|null $db
     *
     * @return string
     */
    public static function decodeTableName(string $encodedTableName, string $db = null): string
    {
        $dbName = $db === null ? SheetDB::SHEETDB_DB_NAME : $db;
        $tableName = str_replace(
            SheetDB::SHEETDB_DB_PREFIX
            . "_"
            . $dbName
            . "_"
            . SheetDB::SHEETDB_TABLE_PREFIX
            . "_",
            "",
            $encodedTableName
        );
        return $tableName;
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    public static function getDatabaseNameFromTableName(string $tableName): string
    {
        $dbPrefix = strpos($tableName, SheetDB::SHEETDB_DB_PREFIX . "_");
        $tablePrefix = strpos($tableName, "_" . SheetDB::SHEETDB_TABLE_PREFIX);
        return substr(
            $tableName,
            $dbPrefix +
            (strlen(SheetDB::SHEETDB_DB_PREFIX) + 1),
            ($tablePrefix - ($dbPrefix + (strlen(SheetDB::SHEETDB_TABLE_PREFIX))))
        );
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    public static function getTableNameIndependentFromDBName(string $tableName): string
    {
        $tablePrefix = strpos($tableName, "_" . SheetDB::SHEETDB_TABLE_PREFIX);
        $tableName = substr(
            $tableName,
            $tablePrefix +
            (strlen(SheetDB::SHEETDB_TABLE_PREFIX) + 1)
        );
        return $tableName;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function getOppositeOfString(string $text): string
    {
        $text = trim($text);
        $text = mb_strtolower($text);
        $text = preg_replace('/\s+/', '', $text);
        switch ($text) {
            case "true":
                return "false";
            case "false":
                return "true";
            case "yes":
                return "no";
            case "no":
                return "yes";
            default:
                return "not " . $text;
        }
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function slugify(string $str): string
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        return $str;
    }

    /**
     * @param array $array
     * @param array $columns
     *
     * @return array
     */
    public static function array_only(array $array, array $columns): array
    {
        return array_intersect_key($array, array_flip($columns));
    }

    /**
     * @param string $text
     * @param string $salt
     *
     * @return string
     */
    public static function encrypt(string $text, string $salt = ""): string
    {
        return hash('sha256', $salt . $text);
    }
}
