<?php

/**
 * Applies simple object pooling to a class.
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
abstract class ObjectPool
{
    protected $pool = [];

    /**
     * Adds an object to the pool.
     * @param mixed $object
     * @return mixed
     */
    public function acquire($object): mixed
    {
        $this->pool[] = $object;
        return $object;
    }

    /**
     * Finds the object in the pool and releases them.
     * @param mixed $object
     * @return [type]
     */
    public function release($object)
    {
        $this->pool = array_filter($this->pool, function ($pooledObject) use ($object) {
            return $pooledObject !== $object;
        });
    }


    /**
     * Returns the item from the pool.
     * @param mixed $object
     * @return mixed
     */
    public function get($object): mixed
    {
        $item = array_filter($this->pool, function ($pooledObject) use ($object) {
            return $pooledObject === $object;
        });
        if (count($item) > 0) {
            return $item[0];
        } else {
            return null;
        }
    }

    /**
     * Returns all of the items from the pool.
     * @return array
     */
    public function all(): array
    {
        return $this->pool;
    }

    /**
     * Clears the pool.
     * @return [type]
     */
    public function clear()
    {
        $this->pool = [];
    }
}
