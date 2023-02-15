<?php

/**
 * File cache for Forms, submission and questions
* @author      Onur Yüksel <ce.onuryuksel@gmail.com>
* @copyright   2022 JotForm, Inc.
* @link        http://www.jotform.com
* @version     1.0.0
* @package     SheetDB
*/
class Cache
{
    protected $forms = [];
    protected $submissions = [];
    protected $questions = [];

    public function __construct()
    {
        $this->initialize();
    }

    public function __destruct()
    {
        $this->setJsonCache();
    }

    /**
     * Initializes cache
     * @return [type]
     */
    protected function initialize()
    {
        $this->getJsonCache();
    }

    /**
     * Gets cache from json file if file time is less than 30sec
     * @return [type]
     */
    protected function getJsonCache()
    {
        $cache = null;
        if (file_exists(__DIR__ . '/Cache.json')) {
            $filemtime = filemtime(__DIR__ . '/Cache.json');
            $now = time();
            if ($now - $filemtime <= 30) {
                $dateStr = date('Y-m-d H:i:s', $filemtime);
                $cache = file_get_contents(__DIR__ . '/Cache.json');
            }
        }
        
        if ($cache !== null) {
            $cache = json_decode($cache, true, 512, JSON_OBJECT_AS_ARRAY);
            $this->forms = $cache["forms"] ?? [];
            $this->questions = $cache["questions"] ?? [];
            $this->submissions = $cache["submissions"] ?? [];
        }
    }

    /**
     * Writes current cache to json file
     * @return [type]
     */
    protected function setJsonCache()
    {
        $cache = [
            "forms" => $this->forms,
            "questions" => $this->questions,
            "submissions" => $this->submissions
        ];

        file_put_contents(__DIR__ . '/Cache.json', json_encode($cache));
    }

    /**
     * Checks whether the row exists or not in cache.
     * @param string $tableName
     * @param string $primaryKey
     * @return bool
     */
    public function rowExists(string $tableName, string $primaryKey): bool
    {
        if (array_key_exists($tableName, $this->submissions)) {
            return array_walk($this->submissions[$tableName], function ($row) use ($primaryKey) {
                return $row[$primaryKey] === $primaryKey;
            });
        }

        return false;
    }

    /**
     * Checks whether the submission exists or not in cache.
     * @param string $tableName
     * @return bool
     */
    public function submissionsExists(string $tableName): bool
    {
        return array_key_exists($tableName, $this->submissions);
    }

    /**
     * Checks whether the forms has been cached or not.
     * @return bool
     */
    public function formsExists(): bool
    {
        return !empty($this->forms);
    }

    /**
     * Checks whether the question exists or not in cache.
     * @param mixed $tableName
     * @return bool
     */
    public function questionsExists($tableName): bool
    {
        return array_key_exists($tableName, $this->questions);
    }

    /**
     * Gets the submissions of a specified table from cache.
     * @param string $tableName
     * @param string|null $primaryKey
     * @param mixed $value
     *
     * @return array
     */
    public function getSubmissions(string $tableName): array
    {
        if ($this->submissionsExists($tableName)) {
            return $this->submissions[$tableName];
        }
    }

    /**
     * Returns the forms from cache.
     * @return array
     */
    public function getForms(): array
    {
        return $this->forms;
    }

    /**
     * Returns the questions of a specified table from cache.
     * @param string $tableName
     * @return array
     */
    public function getQuestions(string $tableName): array
    {
        return $this->questions[$tableName];
    }

    /**
     * Replaces the current table's submissions with new ones.
     * @param string $tableName
     * @param array $table
     * Replaces the submissions in the table
     */
    public function replaceSubmissions(string $tableName, array $submissions)
    {
        $this->submissions[$tableName] = $submissions;
    }

    /**
     * Replaces the forms with new ones.
     * @param array $forms
     */
    public function replaceForms(array $forms)
    {
        $this->forms = $forms;
    }

    /**
     * Replaces the questions of a specified table with new ones.
     * @param string $tableName
     * @param array $questions
     */
    public function replaceQuestions(string $tableName, array $questions)
    {
        $this->questions[$tableName] = $questions;
    }

    /**
     * Release the specified cache.
     * @param string $cache
     * @param string|null $key
     * @return [type]
     */
    public function free(string $cache, string $key = null)
    {
        if ($key) {
            unset($this->$cache[$key]);
        } else {
            unset($this->$cache);
        }
    }
}
