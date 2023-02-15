<?php

/**
 * Seeder can be used to seed data into the database.
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
abstract class Seeder
{
    protected $table;
    /**
     * Runs the seeders run method
     * @return [type]
     */
    public function run()
    {
        $this->seed();
    }

    /**
     * Prunes the whole data in the table.
     * @return [type]
     */
    protected function clear()
    {
        if (count(SheetDB::from($this->table)->get()) > 0) {
            return SheetDB::table($this->table)->delete();
        }
    }

    /**
     *  Runs the array of seeders in order.
     * @param array $seeders
     * @return [type]
     */
    public static function runAll(array $seeders)
    {
        foreach ($seeders as $seeder) {
            $seeder->run();
        }
    }

    /**
     * @return [type]
     */
    abstract protected function seed();
}
