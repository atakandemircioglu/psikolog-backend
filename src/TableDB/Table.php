<?php

include_once 'v1/lib/SheetDB/init.php';

/**
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
class Table
{
    private $id;
    private $name;
    private $status;
    private $created_at;
    private $updated_at;
    private $last_submission;
    private $columns = [];

    // CONVERT TO SIMPLE FACTORY
    public function __construct()
    {
    }

    public function __get($key)
    {
        return $this->$key ?? null;
    }

    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "last_submission" => $this->last_submission,
            'columns' => $this->columns,
        ];
    }
}
