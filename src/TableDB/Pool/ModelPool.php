<?php

include_once 'v1/lib/SheetDB/Pool/ObjectPool.php';

/**
 * Provides bulkInsert method to models using a simple object pool.
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
class ModelPool extends ObjectPool
{
    protected $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Inserts all of the FILLED models in the pool into the database. Uses $model->fillables attribute
     * @return array
     */
    public function bulkInsertFromPool(): array
    {
        $objectFillables = [];
        foreach ($this->all() as $object) {
            $objectArray = $object->toArray();
            if (!empty($objectArray)) {
                $objectFillables[] = $objectArray;
            }
        }

        return SheetDB::table($this->tableName)->insert($objectFillables);
    }
}
