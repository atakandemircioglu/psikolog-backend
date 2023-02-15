<?php

include_once 'v1/lib/SheetDB/init.php';

/**
 * SheetDB Schema creation and management class
 *
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
class Schema
{
    /**
     * Creates a new schema. Actually this method just creates a new form using the default naming conventions.
     * @param string $tableName
     * @param closure $builderClosure
     * @param string|null $db
     * @return [type]
     */
    public static function create(string $tableName, closure $builderClosure, ?string $db = null)
    {
        $db = $db ?? SheetDB::SHEETDB_DB_NAME;
        $tableBuilder = new TableBuilder();
        $builderClosure($tableBuilder);
        $properties = $tableBuilder->getProperties();
        $properties["title"] = Utils::encodeTableName($tableName, $db);
        $properties["height"] = 600;
        $table = [ // Form'a ait veriyi burada sarmalıyorum. questions datası bir builderdan dönüyor. var_dump'ta veri doğru görünüyor.
            'properties' => $properties,
            'questions' => $tableBuilder->getQuestions(),
        ];

        self::dropIfExists($tableName, $db);
        $form = SheetDB::api()->createForm($table); // createForm($table) array alıyor, post atıyor. createForms($table) json alıyor put atıyor.
        $questions = json_encode($tableBuilder->getQuestions()); // Form question olmadan oluşturulduğu için burada question'ı ayrıca göndereceğim o yüzden json'a çeviriyorum.
        $formId = $form["id"] ?? null;
        if (!isset($formId)) { // Formun oluşturulup oluşturulmadığını kontrol etmek için id kontrolü yapıyorum.
            throw new Exception("Form creation failed");
        }

        //$tableQuestions = SheetDB::api()->createFormQuestions($formId, $questions); // Soruları ilgili id'ye eklemeye çalışıyorum. Put request atıyor.
        return $form;
    }

    /**
     * Drops a schema. Actually this method just drops a form using the default naming conventions.
     * @param mixed $tableName
     * @param null $db
     * @return [type]
     */
    public static function dropIfExists($tableName, $db = null)
    {
        $db = $db ?? SheetDB::SHEETDB_DB_NAME;
        if (self::isTableExists($tableName, $db)) {
            return self::drop($tableName, $db);
        }
        return false;
    }

    /**
     * Checks if a schema exists. Actually this method just checks if a form exists using the default naming conventions.
     * @param mixed $tableName
     * @param null $db
     * @return [type]
     */
    public static function isTableExists($tableName, $db = null)
    {
        $db = $db ?? SheetDB::SHEETDB_DB_NAME;
        $table = SheetDB::use($db)->table($tableName)->get();
        return count($table) > 0;
    }

    /**
     * Deletes a table.
     * @param string $tableName
     * @param string|null $db
     * @return bool
     */
    private static function drop(string $tableName, string $db = null): bool
    {
        $db = $db ?? SheetDB::SHEETDB_DB_NAME;
        $table = SheetDB::use($db)->table($tableName)->get();
        try {
            SheetDB::api()->deleteForm($table["tbl_id"]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
