<?php

include_once 'v1/lib/SheetDB/init.php';

/**
 * Creates table from different array types.
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */

class TableFactory
{
    public function createFromFormArray($formArray)
    {
        $table = new Table();
        $table->id = $formArray["id"];
        $table->name = $formArray["title"];
        $table->status = $formArray["status"];
        $table->created_at = $formArray["created_at"];
        $table->updated_at = $formArray["updated_at"];
        $table->last_submission = $formArray["last_submission"];
        $table->columns = $formArray["columns"] ?? [];
        return $table;
    }

    public function createFromTableArray($tableArray)
    {
        $table = new Table();
        $table->id = $tableArray["id"];
        $table->name = $tableArray["name"];
        $table->status = $tableArray["status"];
        $table->created_at = $tableArray["created_at"];
        $table->updated_at = $tableArray["updated_at"];
        $table->last_submission = $tableArray["last_submission"];
        $table->columns = $tableArray["columns"] ?? [];
        return $table;
    }
}
