<?php

include_once 'v1/lib/SheetDB/init.php';


/* SAMPLE USAGES
    SheetDB::use($db)->from("name")->get(); // ROWS
    SheetDB::select("col1","col2")->from("name")->get(); // ROWS
    SheetDB::from("name")->get(); // ROWS
    SheetDB::table("name")->get(); // TABLE
    SheetDB::tables(); // TABLES
    SheetDB::table()->where()->update();
*/


/**
 * SheetDB QueryBuilder.
 *
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */


 use Exception;

class Query
{
    protected $db;
    protected $tableName;
    protected $table;
    protected $columns = [];
    protected $where = [];
    protected $orderBy = [];
    protected $limit = null;
    protected $mode = "lazy"; // DO NOT CHANGE THIS IF YOU LOVE YOUR TIME
    protected $result = []; //TODO:: MAKE COLLECTION CLASS IN FUTURE

    public function __construct()
    {
        $this->db = SheetDB::SHEETDB_DB_NAME;
    }

    /**
     * Sets the db name to use. If not set, it will use the default db.
     * DB name is the name that comes after DB_PREFIX with underscores of the sheet in the tables product.
     * It is not an actual database but we call it DB to be able to use different tables for different products in the same account.
     * @param string $db
     * @return self
     */
    public function use(string $db): self
    {
        $this->db = $db;
        return $this;
    }

    /**
     * Filters the result if filter or order exists and returns the result.
     * @return mixed
     */
    public function get()
    {
        if (count($this->where) > 0) {
            $this->result =  $this->filterWhere();
        }
        if ($this->orderBy !== []) {
            $this->order();
        }

        return $this->result;
    }

    //#region FILTER FUNCTIONS

    /**
     * Order the result by the given column.
     * @param string $column
     * @param string $order
     * @return self
     */
    public function orderBy(string $column, string $order = "ASC"): self
    {
        $this->orderBy = [
            "column" => $column,
            "order" => $order,
        ];
        return $this;
    }

    /**
     * Orders the result by the $order array
     * @return [type]
     */
    public function order()
    {
        $array = [];
        foreach ($this->result as $k => $v) {
            $array[$k] = $v[$this->orderBy["column"]];
            $array[$k] = (int)$v[$this->orderBy["column"]];
        }
        if ($this->orderBy["order"] == 'ASC') {
            array_multisort($array, SORT_ASC, $this->result);
        } else {
            array_multisort($array, SORT_DESC, $this->result);
        }
    }

    /**
     * Adds where clause to query to use filter
     * @param string $col1
     * @param mixed $col2
     * @param string $operator
     * @return self
     */
    public function where(string $col1, $col2, $operator = "=")
    {
        if (count($this->where) !== 0) {
            $this->where[] = "&&";
        }
        $this->where[] = [$col1, $operator ,$col2];
        return $this;
    }

    /**
     * Adds orWhere clauses to query to use filter. Note that this function use process priority between clauses.
     * It means if you use where and orWhere, the result will be filtered by the last where clause.
     * For Example : where("title", "SheetDB")->where("id", "1")->orWhere("id", "2") means => if(title == "SheetDB" && ("id" == 1 || "id"= 2))
     * If you add another or query after this query like ->orWhere("id", "3") it means => if(title == "SheetDB" && "id" == 1 || ("id"= 2 || "id"= 3))
     * To prevent this logic failure just use combine() after using where() function that you want to combine two of them.
     * This will execute the query before the combine() method and will use the result of the first query to apply the where clauses before it.
     * For Example : where("title", "SheetDB")->combine()->where("id", "1")->orWhere("id", "2")->orWhere("id") means => if(title == "SheetDB" && ("id" == 1 || ("id"= 2 || "id" == 3)))
     * Note that the combine method should be used becarefully. It will not combine the where clauses. It will just combine the queries.
     * Thus if you use combine() too many times, the performance will be affected. It will take more time to execute all the queries.
     * @param string $col1
     * @param mixed $col2
     * @param string $operator
     * @return self
     */
    public function orWhere(string $col1, mixed $col2, $operator = "=")
    {
        if (count($this->where) !== 0) {
            $this->where[] = "||";
        }
        $this->where[] = [$col1, $operator ,$col2];
        return $this;
    }

    /**
     * Combines the where clauses with the orWhere and where clauses. Actually, it executes the query and clears the where clauses.
     * @return self
     */
    public function combine()
    {
        $this->result = $this->get();
        $this->where = [];
        return $this;
    }

    /**
     * Filters the result by the given where clauses.
     * @return [type]
     */
    private function filterWhere()
    {
        $filtered = [];
        foreach ($this->result as $result) {
            $operator = "||";
            $resultBefore = false;
            foreach ($this->where as $key => $value) { // where(book_slug, 'test')->where('up_slug' == null)->orWhere('up_slug', '');
                if (is_array($value)) {
                    $t1 = $result[$value[0]] ?? null;
                    $condition = $value[1];
                    $t2 = $value[2];
                    if ($operator === "||") {
                        $resultBefore = ($resultBefore || $this->calculateCondition($t1, $t2, $condition));
                    } else {
                        $resultBefore = ($resultBefore && $this->calculateCondition($t1, $t2, $condition));
                    }
                } else {
                    $operator = $value;
                }
            }

            if ($resultBefore === true) {
                $filtered[] = $result;
            }
        }
        return $filtered;
    }

    /**
     * Calculates the condition of the given values.
     * @param mixed $t1
     * @param mixed $t2
     * @param string $operator
     *
     * @return bool
     */
    private function calculateCondition($t1, $t2, string $operator): bool
    {
        switch ($operator) {
            case "=":
                return $t1 == $t2;
            case "!=":
                return $t1 != $t2;
            case ">":
                return $t1 > $t2;
            case "<":
                return $t1 < $t2;
            case ">=":
                return $t1 >= $t2;
            case "<=":
                return $t1 <= $t2;
            case "LIKE":
                return strpos($t1, $t2) !== false;
            case "NOT LIKE":
                return strpos($t1, $t2) === false;
            case "IN":
                return in_array($t1, $t2);
            case "NOT IN":
                return !in_array($t1, $t2);
            default:
                return false;
        }
    }

    // #region FILTER FUNCTIONS END

    /**
     * Selects which columns to get from the table.
     * @param string ...$columns
     * @return self
     */
    public function select(string ...$columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Selects which table to get from.
     * @param string $tableName
     * @return Query
     */
    public function from(string $tableName): self
    {
        return $this->table($tableName)->rows();
    }

    /**
     * Updates rows in the specified table.
     * @param array $values
     * @return array
     */
    public function update(array $values): array
    {
        if ($this->table === null) {
            throw new Exception("Table was not specified");
        }
        return $this->updateMass($values);
    }

    /**
     * Returns all tables in db.
     * @return Query
     */
    public function tables(): Query
    {
        return $this->getTables($this->db);
    }

    /**
     * Selects the table to use.
     * @param string $tableName
     * @return Query
     */
    public function table($tableName): Query
    {
        $this->tableName = $tableName;
        return $this->getTable($this->tableName, $this->db);
    }

    /**
     * Returns columns of the specified table.
     * @return self
     */
    public function columns(): self
    {
        if ($this->mode === "lazy" && SheetDB::cache()->questionsExists($this->tableName)) {
            $questions = SheetDB::cache()->getQuestions($this->tableName);
        } else {
            $questions = SheetDB::api()->getFormQuestions($this->table->id);
            SheetDB::cache()->replaceQuestions($this->tableName, $this->columns);
        }

        $questions = array_map(function ($question) {
            return [
                "column_name" => $question["name"] ?? null,
                "column_type" => $question["type"] ?? null,
                "column_id" => $question["qid"] ?? null,
                "column_nullable" => Utils::getOppositeOfString($question["required"] ?? "No"),
                "column_default" => $question["defaultValue"] ?? null
            ];
        }, $questions);
        $this->result = $questions;
        return $this;
    }

    /**
     * Returns all rows in the specified table.
     * @return self
     */
    public function rows(): self
    {
        if ($this->mode === "lazy" && SheetDB::cache()->submissionsExists($this->tableName)) {
            $submissions = SheetDB::cache()->getSubmissions($this->tableName);
        } else {
            $submissions = SheetDB::api()->getFormSubmissions($this->table->id, 0, 1000, ["status:ne" => "DELETED" ]);
            SheetDB::cache()->replaceSubmissions($this->tableName, $submissions);
        }


        $submissions = $this->mergeSubmissionsIntoOneRow($submissions);
        if ($this->columns !== []) {
            $columns = $this->columns;
            $submissions = array_map(function ($ref) use ($columns) {
                return array_filter($ref, function ($column) use ($columns) {
                    return in_array($column, $columns);
                }, ARRAY_FILTER_USE_KEY);
            }, $submissions);
        }

        $this->result = $submissions;
        return $this;
    }

    /**
     * Gets all tables from specified database.
     * @return Query
     */
    private function getTables(): Query
    {
        if (SheetDB::cache()->formsExists()) {
            $forms = SheetDB::cache()->getForms();
        } else {
            $forms = SheetDB::api()->getForms(0, 1000, ["status:ne" => "DELETED"]);
            SheetDB::cache()->replaceForms($forms);
        }


        $forms = SheetDB::SHEETDB_ZERO_CONFIG ? $forms : $this->filterSheetDBTableForms($forms, $this->db);
        foreach ($forms as &$form) {
            $form = self::serializeAsTable($form)->toArray();
            $form["name"] = Utils::decodeTableName($form["name"], $this->db);
        }

        $forms = array_filter($forms, function ($value) {
            return $value["status"] == "ENABLED";
        });

        $this->result = $forms;
        return $this;
    }

    /**
     * Gets the specified table from the specified database.
     * @param string $tableName
     * @return Query
     */
    private function getTable(string $tableName, $db = null): Query
    {
        $tables = $this->getTables()->get();

        $tableArray = array_values(array_filter($tables, function ($tableArray) use ($tableName) {
            $accessor = is_numeric($tableName) ? 'id' : 'name';
            return $tableArray[$accessor] === $tableName;
        }));

        $tableFactory = new TableFactory();
        if (count($tableArray) > 0) {
            $table = $tableFactory->createFromTableArray($tableArray[0]);
            $this->table = $table;
            $this->result = $tableArray[0];
        } else {
            throw new Exception("Table $tableName not found");
        }
        return $this;
    }

    public function withColumns()
    {
        $columns = $this->columns()->get();
        if (count($this->result) == 1) {
            if (array_key_exists("columns", $this->result[0])) {
                $this->result[0]["columns"] = $columns;
            }
        }
        return $this;
    }


    // curl -X PUT -d '[{"10":"page1","13":1,"14":"page2","15":"","11":""}]' "https://api.jotform.com/form/221851556878065/submissions?apiKey=22dc62dc59c34b83f545946bfc7fcd88"

    /**
     * Inserts rows into the specified table.
     * @param array ...$values
     * @return Query
     */
    public function insert(array ...$values): array
    {
        if ($this->table === null) {
            throw new Exception("Table was not specified");
        }

        $submissions = $this->convertValuesToSubmissions($this->table, "new", ...$values);
        $submissions = json_encode($submissions);

        $insertion = SheetDB::api()->createFormSubmissions($this->table->id, $submissions);

        if (SheetDB::cache()->submissionsExists($this->tableName)) {
            SheetDB::cache()->free("submissions", $this->tableName);
        }

        return $insertion;
    }

    /**
     * Deletes rows from the specified table. The rows are specified by the where clause.
     * LIMIT with where() or|and orWhere() methods before use. Otherwise all data will be pruned!
     * @return array
     */
    public function delete($cacheMode = null): array
    {
        if ($this->table === null) {
            throw new Exception("Table was not specified");
        }
        $submissions = $this->rows()->get();
        $submissions = array_map(function ($submission) {
            return $submission["id"];
        }, $submissions);
        $results = [];
        foreach ($submissions as $submission) {
            $deletion = SheetDB::api()->deleteSubmission($submission);
            if ($deletion === "Submission #{$submission} deleted successfully.") {
                $results[] = true;
            } else {
                $results[] = false;
            }
            usleep(250);
        }
        if (SheetDB::cache()->submissionsExists($this->tableName)) {
            if ($cacheMode !== "stop") {
                SheetDB::cache()->free("submissions", $this->tableName);
            }
            return $results;
        }
    }

    /**
     * Deletes unnecessary fields from form before returning rows or columns from the specified table.
     * @param array $columns
     * @return array
     */
    private function filterUnnecessaryColumns(array $columns): array
    {
        $unnecassaryColumns = [
            'submit',
        ];
        foreach ($columns as &$column) {
            $column = array_filter($column, function ($key) use ($unnecassaryColumns) {
                return !in_array($key, $unnecassaryColumns);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $columns;
    }

    /**
     * Updates rows in the specified table. The rows are specified by the where clause.
     * LIMIT with where() or|and orWhere() methods before use. Otherwise all data will be updated!
     * @param array ...$values
     * @return array
     */
    private function updateMass(array ...$values): array
    {
        $selectedSubmissions = $this->rows()->get();
        $selectedSubmissions = $this->filterUnnecessaryColumns($selectedSubmissions);
        $results = [];
        foreach ($selectedSubmissions as $submission) {
            //$submission = array_merge($submission, ...$values);
            $submissionId = $submission["id"];
            unset($submission["id"]);
            $converted = $this->convertValuesToSubmissions($this->table, "new", $values);
            unset($converted[0]["created_at"]);
            $result = SheetDB::api()->editSubmission($submissionId, $converted[0]);
            $results[] = $result;
        }

        if (SheetDB::cache()->submissionsExists($this->tableName)) {
            SheetDB::cache()->free("submissions", $this->tableName);
        }
        return $results;
    }

    /**
     * Converts arrays to submissions.
     * @param Table $table
     * @param array ...$values
     * @return array
     */
    private function convertValuesToSubmissions(Table $table, $opt = "new", array ...$values): array
    {
        $questions = SheetDB::api()->getFormQuestions($table->id);
        $submissions = [];
        foreach ($values as $value) {
            $answer = [];
            foreach ($value as $prop) {
                foreach ($prop as $key => $value) {
                    if ($opt === "new") {
                        $qid = $this->findQuestionIdFromTitle($key, $questions);
                        $answer[$qid] = $value;
                        if ($qid === null && $key !== 'collaboratorLinki') {
                            throw new Exception("Column '$key' not found" . " Value => '$value");
                        }
                    } else {
                        $column_id = $this->findQuestionNameFromTitle($key, $questions);
                        $answer[$name] = $value;
                        if ($name === null) {
                            throw new Exception("Column '$key' not found" . " Value => '$value");
                        }
                    }
                }
                $submissions[] = $answer;
            }
        }
        return $submissions;
    }

    /**
     * Finds question id from title.
     * @param string $title
     * @param array $questions
     *
     * @return mixed
     */
    private function findQuestionIdFromTitle(string $title, array $questions)
    {
        foreach ($questions as $question) {
            if ($question["name"] === $title) {
                return $question["qid"];
            }
        }
        return null;
    }

    /**
     * Finds question name from title.
     * @param string $title
     * @param array $questions
     *
     * @return mixed
     */
    private function findQuestionNameFromTitle(string $title, array $questions): mixed
    {
        foreach ($questions as $question) {
            if ($question["text"] === $title) {
                return $question["column_id"];
            }
        }
        return null;
    }


    /**
     * converts a form to a table
     * @param array $form
     * @return Table
     */
    private static function serializeAsTable(array $form): Table
    {
        return (new TableFactory())->createFromFormArray($form);
    }

    /**
     * Combines different parts of a submission into one array
     * @param array $submissions
     * @return array
     */
    private function mergeSubmissionsIntoOneRow(array $submissions): array
    {
        $rows = array_map(function ($submission) {
            $one = [];
            if ($submission["status"] === "ACTIVE" || $submission["status"] === "CUSTOM") {
                $one["id"] = $submission["id"];
                $one["created_at"] = $submission["created_at"];
                $one["updated_at"] = $submission["updated_at"];
            }


            array_map(function ($answer) use (&$one, $submission) {
                if ($submission["status"] === "ACTIVE" || $submission["status"] === "CUSTOM") {
                    $serializedRow = $this->serializeAsRow($answer);
                    if ($serializedRow !== null) {
                        $this->mergeIntoOne($serializedRow, $one);
                    }
                }

                unset($answer);
            }, $submission['answers']);
            return $one;
        }, $submissions);

        return array_filter($rows, function ($row) {
            return $row !== null;
        });
    }


    /**
     * Filters out the forms that are not using the SheetDB config. Thus we can only use the forms that are created for SheetDB.
     * @param array $tableForms
     * @param string|null $db
     *
     * @return array
     */
    private function filterSheetDBTableForms(array $tableForms, string $db = null): array
    {
        $tableForms = array_filter($tableForms, function ($tableForm) use ($db) {
            return (strpos($tableForm["title"], SheetDB::SHEETDB_DB_PREFIX . "_") === 0)
            && ($db !== null ? Utils::getDatabaseNameFromTableName($tableForm["title"]) == $db
            : Utils::getDatabaseNameFromTableName($tableForm["title"]) == SheetDB::SHEETDB_DB_NAME);
        });
        return $tableForms;
    }

    /**
     * Filters a question with neccessary columns
     * @param array $question
     * @return array
     */
    private function serializeAsColumn(array $question): array
    {
        return [
            'col_name' => $question["title"],
            'col_type' => $question["type"],
            'col_id' => $question["qid"],
        ];
    }

    /**
     * Filters a submission as name => value
     * @param array $row
     * @return mixed
     */
    private static function serializeAsRow(array $row)
    {
        if (!array_key_exists('name', $row)) {
            return null;
        }

        if ($row["name"] === "submit") {
            return null;
        }

        return [
            $row["name"] => $row["answer"] ?? null,
        ];
    }


    /**
     * Merges two arrays into one
     * @param array $array
     * @param array $one
     * @return [type]
     */
    private function mergeIntoOne(array $array, array &$one)
    {
        foreach ($array as $key => $value) {
            $one[$key] = $value;
        }
    }

    /**
     * Exports current query to another query means to create a new query from the current query.
     * @param Query $query
     * @return [type]
     */
    private function export(Query &$query)
    {
        $query->db = $this->db;
        $query->tableName = $this->tableName;
        $query->table = $this->table;
        $query->columns = $this->columns;
        $query->where = $this->where;
        $query->orderBy = $this->orderBy;
        $query->limit = $this->limit;
        $query->result = $this->result;
    }

    /**
     * Sets the result of the query to use additional filter if needed.
     * @param Query $query
     * @return [type]
     */
    public function result($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Select specific columns from the table.
     * @return mixed
     */
    private function selectColumns(): mixed
    {
        if (!empty($this->columns)) {
            return array_filter($this->result, function ($column) {
                return in_array($column, $this->columns);
            });
        }
        return null;
    }
}
