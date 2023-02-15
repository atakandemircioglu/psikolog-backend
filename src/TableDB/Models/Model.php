<?php

include_once 'v1/lib/SheetDB/init.php';
use DateTime;
use DateInterval;
use Exception;
use ReflectionProperty;
use ModelPool;
use Query;
use Exceptions\NotFoundException;
use Exceptions\ImproperPoolException;

/**
 * To use the ModelPool, you need to define protected static ModelPool $pool = null in your model class.
 * The purpose of the pool is bulk insert and update. Doesn't includes any worker logic
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
abstract class Model
{
    protected static $tableName; // The table name of the model.
    protected static $tableID; // The table id of the model.
    protected $primaryKey; // The primary key of the model.
    protected $mutators = []; // The mutators of the model. (Mutators used to convert the data to the correct format.)
    protected $customSetters = []; // The custom setters of the model. (Custom setters used to convert the data to the correct format with params.)
    protected $accessors = []; // The accessors of the model. (Accessors used to get the data in the correct format.)
    protected $fillables; // The fillables of the model. (The fillables are the columns of the table that can be filled.)
    protected $onDelete = []; // relationships that will be deleted when the model is deleted.
    private $defaultMutators = [ // The default mutators of the model. (The default mutators are the mutators that are used by default.)
        "created_at" => "mutateCreatedAt",
        "updated_at" => "mutateUpdatedAt"
    ];
    private $defaultAcessors = [ // The default accessors of the model. (The default accessors are the accessors that are used by default.)
        "created_at" => "getCreatedAt",
        "updated_at" => "getUpdatedAt"
    ];
    private $initialized = false; // Whether the model is initialized or not.
    protected $usePool = false; // Whether the model should use the pool or not.

    /**
     * Initializes the model.
     * @return void
     */
    public function __construct(array $values = [], bool $usePool = false)
    {
        $this->initialize(); // Initializes the model.
        $this->fill($values); // Fills the model with the values.
        $usePool === true ? $this->usePool() : $this->dontUsePool(); // Sets the usePool property.
        if (!empty(static::$tableID)) {
            static::$tableName = static::$tableID;
        }
    }

    /**
     * Initializes the model.
     * @return [type]
     */
    protected function initialize()
    {
        $this->reverseFillables();
        $this->mutators = array_merge($this->mutators, $this->defaultMutators);
        $this->accessors = array_merge($this->accessors, $this->defaultAcessors);
        $this->initialized = true;
    }

    /**
     * Fills the model with the values.
     * @param array $values
     * @return [type]
     */
    public function fill(array $values = [])
    {
        foreach ($values as $key => $value) {
            if (in_array($key, array_keys($this->accessors))) {
                $values[$key] =  $this->{$this->accessors[$key]}($value);
            }
        }
        $this->fillables = $values;
    }

    /**
     * Find the the model with the given primary key value.
     * @param mixed $value
     * @return mixed
     */
    public function findByPrimaryKey($value)
    {
        $model = $this->where($this->primaryKey, $value)->get()[0] ?? null;
        if ($model) {
            return new static($model);
        }
        return null;
    }

    /**
     * Find the the model with the given primary key value. If it not exists, @throws NotFoundException.
     * @param mixed $value
     * @throws NotFoundException
     * @return model
     */
    public function findByPrimaryKeyOrFail($value): model
    {
        $model = $this->findByPrimaryKey($value);
        if ($model) {
            return $model;
        }
        $modelName = static::class;
        $modelName = explode('\\', $modelName);
        $modelName = array_pop($modelName);
        $modelName = str_replace("Model", '', $modelName);
        throw new NotFoundException("{$modelName}-{$value} not found", 404);
    }

    /**
     * Checks the given model is exists with primaryKey
     * @return [type]
     */
    public function isExists()
    {
        if (empty($this->fillables[$this->primaryKey])) {
            return false;
        }
        return count(SheetDB::table(static::$tableName)
        ->where($this->primaryKey, $this->fillables[$this->primaryKey])
        ->rows()
        ->get()) > 0;
    }

    /**
     * Creates the model. If the pool is enabled, this will release the model from the pool.
     * @return mixed
     */
    public function create()
    {
        if ($this->isPoolEnabled()) {
            $this->pool()->release($this);
        }

        $insertion = SheetDB::table(static::$tableName)->insert([$this->fillables]);
        if (!empty($insertion)) {
            usleep(500); // Sleeps for 500 milisecond to make sure the model is created.
            $model = SheetDB::from(static::$tableName)->where('id', $insertion[0]["submissionID"])->get();
            if ($model) {
                if ($model[0]) {
                    return new static($model[0]);
                }
            }
        }
        return null;
    }

    /**
     * Updates the model. If the pool is enabled, this will release the model from the pool.
     * @return array
     */
    public function update(): array
    {
        $id = $this->fillables[$this->primaryKey];
        unset($this->fillables["id"], $this->fillables["created_at"], $this->fillables["updated_at"]);

        if ($this->isPoolEnabled()) {
            $this->pool()->release($this);
        }


        return SheetDB::table(static::$tableName)
            ->where($this->primaryKey, $id)
            ->update($this->fillables);
    }

    /**
     * If the model is exists stated with the $primaryKey property, it will update the model, otherwise it will insert the model.
     * @param string|null $primaryKey is a variable to compare the model with the preferred column. It is not mandatory to use the actual primary key of the table.
     * @return mixed
     */
    public function save(string $primaryKey = null)
    {
        $primaryKey = $primaryKey ?? $this->primaryKey;
        if ($this->isExists()) {
            if (array_key_exists($primaryKey, $this->fillables)) {
                return  $this->update();
            }
        } else {
            return $this->create();
        }
    }

    /**
     * Deletes the model. If the pool is enabled, this will release the model from the pool.
     * @return mixed
     */
    public function destroy($cacheMode = null)
    {
        if ($this->isPoolEnabled()) {
            $this->pool()->release($this);
        }

        if (count($this->onDelete) > 0) {
            $this->deleteRelations();
        }

        $deletion = SheetDB::table(static::$tableName)
        ->where($this->primaryKey, $this->fillables[$this->primaryKey])
        ->delete($cacheMode);
        return $deletion[0] ?? false;
    }

    /**
     * Inverse of the hasMany function. Gets the model's parent model.
     * @param string $tableClassName The class name of the parent model.
     * @param string $primaryKey The primary key of the parent model.
     * @param mixed $value The value of the parent model's primary key.
     * @return mixed
     */
    protected function belongsTo(string $tableClassName, string $primaryKey = "id", $value = "")
    {
        $array = $this->whereFrom($tableClassName::$tableName, $primaryKey, $value)->get();
        if (!empty($array[0])) {
            return new $tableClassName($array[0]);
        } else {
            return null;
        }
    }

    /**
     * Inverse of the belongsTo function. Gets the model's child models.
     * @param string $tableClassName The class name of the child model.
     * @param string $primaryKey The primary key of the parent model (Used as foreign key in the child model's table).
     * @param string $value The value of the child model's primary key.
     * @param array $orderBy ["column_name", "ASC" or "DSC"]
     * @return [type]
     */
    protected function hasMany(
        string $tableClassName,
        string $primaryKey = "id",
        $value = "",
        array $orderBy = ["created_at", "ASC"]
    ): array {
        $array = $this->whereFrom($tableClassName::$tableName, $primaryKey, $value)->orderBy($orderBy[0], $orderBy[1])->get();
        $relations = [];
        foreach ($array as $relation) {
            $relations[] = new $tableClassName($relation);
        }
        return $relations;
    }

    /**
     * Creates a query builder for the given table to get the rows where the given column equals to given $value.
     * @param string $tableName The name of the table.
     * @param string $col The name of the column.
     * @param mixed $value The value of the column.
     * @return Query
     */
    public function whereFrom(string $tableName, string $col, mixed $value)
    {
        return SheetDB::from($tableName)->where($col, $value);
    }

    /**
     * Creates a query builder for the current models table to get the rows where the given column equals to given $value.
     * @param string $col
     * @param mixed $value
     * @return Query
     */
    public function where(string $col, $value)
    {
        $tableName = static::$tableName;
        return SheetDB::from($tableName)->where($col, $value);
    }

    private function parseFilter($data, $delimiter = ',')
    {
        $return = [];
        foreach ($data as $k => $each) {
            $tmp = explode($delimiter, $each);
            $tmp = array_unique($tmp);
            $return[$k] = $tmp;
        }
        return $return;
    }

    public function getByFilter($filter = []) {
        $filters = $this->parseFilter($filter);
        $query = $this->all();
        $filterKeys = array_keys($filters);

        if (empty($filters)) {
            return $query;
        }

        $mapIds = [];
        foreach ($filterKeys as $filterKey) {
            foreach ($query as $qKey => $qValue) {
                foreach ($qValue as $k => $v) {
                    if ($k === $filterKey) {
                        if (!is_array($qValue[$k])) {
                            $aValues = [$qValue[$k]];
                        } else {
                            $aValues = array_values($qValue[$k]);
                        }
                        if (!empty(array_intersect($aValues, $filters[$k]))) {
                            $mapIds[$qKey][$filterKey] = true;
                        }
                    }
                }
            }
        }

        $response = [];
        foreach ($mapIds as $id => $value) {
            $keys = array_keys($value);
            if ($keys == $filterKeys) {
                $response[] = $query[$id];
            }
        }
        return $response;
    }

    /**
     * Populates the model with the the given array.
     * The difference between this method and the fill method is that this method can change every dynamic attribute of the model.
     * @param mixed $array
     * @return self
     */
    public function populate(array $array): self
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Gets the pool of the model.
     * @return ModelPool
     */
    public function pool(): ModelPool
    {
        if (static::$pool === null) {
            static::$pool = new ModelPool(static::$tableName);
        }
        return static::$pool;
    }

    /**
     * Default mutator for the created_at column.
     * @param ?string $created_at
     * @return mixed
     */
    public function getCreatedAt(string $created_at)
    {
        return $this->handleDate($created_at);
    }

    /**
     * Default mutator for the updated_at column.
     * @param ?string $updated_at
     * @return mixed
     */
    public function getUpdatedAt(string $updated_at)
    {
        return $this->handleDate($updated_at);
    }

    /**
     * Checkes whether the model is using the pool or not.
     * @return bool
     */
    public function isPoolEnabled(): bool
    {
        return $this->usePool === true;
    }

    /**
     * Sets the usePool property to true if the model is not using the pool.
     * @return [type]
     */
    public function usePool()
    {
        if (!$this->isPoolEnabled()) {
            if (property_exists(static::class, "pool")) {
                $reflection = new ReflectionProperty(static::class, "pool");
                $reflectionType = $reflection->getType()->getName(); // Gets the property's definition type
                if ($reflectionType === ModelPool::class) {
                    $this->usePool = true;
                    $this->pool()->acquire($this);
                } else {
                    throw new ImproperPoolException("The pool is not a ModelPool");
                }
            } else {
                throw new ImproperPoolException("You need to define protected static ?ModelPool \$pool in your model class.");
            }
        }
    }

    /**
     * Sets the usePool property to false and releases the model from its pool if the model is using the pool.
     * @return [type]
     */
    public function dontUsePool()
    {
        if ($this->usePool === true) {
            $this->usePool = false;
            $this->pool()->release($this);
        }
    }

    /**
     * Handles the date time mutator.
     * @param string|null $dateTimeStr
     * @return mixed
     */
    public function handleDate(string $dateTimeStr)
    {
        if ($dateTimeStr === null) {
            return null;
        }
        $date =  new DateTime($dateTimeStr);
        $date = $date->add(new DateInterval('PT7H')); // Adds 7 hours because the jotform sends the time as the difference of 7 hours.
        return $date->format("Y-m-d H:i:s");
    }

    /**
     * Reverses the fillables. The purpose of this method is when the fillables are defined in the model,
     * there are just the column names of the table, not values.
     * So this method reverses the fillables thus the values turns into the values
     * @return [type]
     */
    protected function reverseFillables()
    {
        $array = [];
        foreach ($this->fillables as $fillable) {
            $array[] = $fillable;
        }
        $this->fillables = $array;
    }


    /**
     * Imports given relation|relations to the model.
     * Callable can be used to transform models when they are included.
     * If $relation is an array, $callable will run on every array item.
     * @param mixed $relation
     * @param callable|null callable
     * @return [type]
     */
    public function with(mixed $relation, callable $callable = null): self
    {
        if (is_array($relation)) {
            $this->getRelations($relation, $callable);
        } else {
            $this->getRelation($relation, $callable);
        }
        return $this;
    }

    protected function deleteRelations()
    {
        foreach ($this->onDelete as $relations) {
            $rel = $this->$relations();
            if (is_array($rel)) {
                foreach ($rel as $relation) {
                    $this->destroyModel($relation);
                }
            } else {
                $this->destroyModel($rel);
            }
        }
    }

    private function destroyModel($model)
    {
        if ($model instanceof Model) {
            $model->destroy("stop");
        }
    }

    /**
     * Imports given relation|relations to the model.
     * Callable can be used to transform models when they are included.
     * @param mixed $relations
     * @param callable|null $callable
     *
     * @return [type]
     */
    protected function getRelations(mixed $relations, callable $callable = null)
    {
        foreach ($relations as $relation) {
            $this->getRelation($relation, $callable);
        }
    }

    /**
     * Imports a single relation to the model. Callable can be used to transform model.
     * @param string $relation
     * @param ?callable|null $callable
     *
     * @return [type]
     */
    protected function getRelation(string $relation, callable $callable = null)
    {
        if (method_exists($this, $relation)) {
            $model = $this->$relation();
        } else {
            throw new NotFoundException("The relation '$relation' does not exist in the model.");
        }

        if ($model && $callable !== null) {
            if (is_array($model)) {
                foreach ($model as $key => $value) {
                    $model[$key] = $callable($value);
                }
            } elseif ($model) {
                $model = $callable($model);
            }
        }

        $this->fillables[$relation] = $model;
    }

    /**
     * Excludes the given columns from the model.
     * @param mixed ...$relations
     * @return self
     */
    public function except(...$relations): self
    {
        foreach ($relations as $relation) {
            unset($this->fillables[$relation]);
        }

        return $this;
    }

    /**
     * Mutates the model's created_at attribute to the current time.
     * @return string
     */
    public function mutateCreatedAt(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Mutates the model's updated_at attribute to the current time.
     * @return string
     */
    public function mutateUpdatedAt(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Checks whether the model has the given mutator or not.
     * @param string $mutatorName
     * @return bool
     */
    protected function mutatorExists(string $mutatorName): bool
    {
        return array_key_exists($mutatorName, $this->mutators);
    }

    /**
     * Checks whether the model has the given setter or not.
     * @param string $key
     * @return bool
     */
    protected function customSetterExists(string $key): bool
    {
        return array_key_exists($key, $this->customSetters);
    }

    /**
     * Checks whether the model has the given column in the fillables array or not.
     * @param string $key
     * @return bool
     */
    protected function fillableExists(string $key): bool
    {
        return array_key_exists($key, $this->fillables);
    }

    /**
     * Gets the table of the current model.
     * @return array
     */
    public function getTable()
    {
        return SheetDB::table(static::$tableName)->get();
    }

    /**
     * Gets the all the rows of the current model's table.
     * @return array
     */
    public function all(): array
    {
        return SheetDB::from(static::$tableName)->get();
    }

    /**
     * Includes mutators to model's fillable attribute. Only combine with toArray() to get the model's attributes.
     * @return self
     */
    public function includeMutators(): self
    {
        $mutators = [];
        $this->fillables = array_merge($this->getMutatorsKeys(), $this->fillables);
        return $this;
    }

    /**
     * gets the keys of the model's mutators.
     * @return array
     */
    public function getMutatorsKeys(): array
    {
        $mutators = [];
        foreach ($this->mutators as $mutator => $function) {
            $mutators[] = $mutator;
        }
        return $mutators;
    }

    /**
     * Includes accessors to model's fillable attribute. Only combine with toArray() to get the model's attributes.
     * @return self
     */
    public function includeAccessors(): self
    {
        $this->fillables = array_merge($this->accessors, $this->fillables);
        return $this;
    }

    /**
     * gets the keys of the model's accessors.
     * @return array
     */
    public function getAccessorsKeys(): array
    {
        $accessors = [];
        foreach ($this->accessors as $accessor => $function) {
            $accessors[] = $accessor;
        }
        return $accessors;
    }

    /**
     * Includes custom setters to model's fillable attribute. Only combine with toArray() to get the model's attributes.
     * @return self
     */
    public function includeCustomSetters(): self
    {
        $this->fillables = array_merge($this->customSetters, $this->fillables);

        return $this;
    }

    /**
     * gets the keys of the model's custom setters.
     * @return array
     */
    public function getCustomSettersKeys(): array
    {
        $customSetters = [];
        foreach ($this->customSetters as $customSetter => $function) {
            $customSetters[] = $customSetter;
        }
        return $customSetters;
    }

    /**
    * Magic method to get the value of the given property
    * @param string $key
    */
    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        } elseif ($this->fillableExists($key)) {
            if (array_key_exists($key, $this->accessors)) {
                return $this->accessors[$key];
            }
            return $this->fillables[$key] ?? null;
        }
    }

    /**
     * Magic method to set the value of the given property.
     * @param mixed $key
     * @param mixed $value
     * @return [type]
     */
    public function __set($key, $value)
    {
        if ($key === "initialized") {
            return;
        }

        if ($key === "pool") {
            return;
        }

        if ($this->initialized === false) {
            $this->initialize();
        }

        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            if ($this->customSetterExists($key)) {
                $this->fillables[$key] = call_user_func([$this, $this->customSetters[$key]], $value);
            } elseif ($this->mutatorExists($key)) {
                $this->fillables[$key] = call_user_func([$this, $this->mutators[$key]]);
            } else {
                $this->fillables[$key] = $value;
            }
        }
    }

    /**
     * Converts model to array.
     * @return [type]
     */
    public function toArray()
    {
        return $this->fillables;
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}
