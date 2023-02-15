<?php

include_once 'v1/lib/SheetDB/init.php';
/**
 * SheetDB TableBuilder.
 *
 * @author      Onur Yüksel <ce.onuryuksel@gmail.com>
 * @copyright   2022 JotForm, Inc.
 * @link        http://www.jotform.com
 * @version     1.0.0
 * @package     SheetDB
 */
class TableBuilder
{
    protected $properties = [];
    protected $questions = [];

    public function getProperties()
    {
        return $this->properties;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function text(string $columnName): self
    {
        $this->questions[]["type"] = ColumnTypes::TEXT;
        $this->setNameAndText($columnName);
        return $this;
    }

    public function number(string $columnName): self
    {
        $this->questions[]["type"] = ColumnTypes::NUMBER;
        $this->setNameAndText($columnName);
        return $this;
    }

    public function boolean(string $columnName): self
    {
        $this->questions[]["type"] = ColumnTypes::BOOLEAN;
        $this->setNameAndText($columnName);
        return $this;
    }

    public function file(string $columnName): self
    {
        $this->questions[]["type"] = ColumnTypes::FILE;
        $this->setNameAndText($columnName);
        return $this;
    }

    public function uuid(string $columnName): self
    {
        $this->questions[]["type"] = ColumnTypes::UUID;
        $this->setNameAndText($columnName);
        return $this;
    }

    public function datetime(string $columnName): self
    {
        $this->questions[]["type"] = ColumnTypes::DATETIME;
        $this->setNameAndText($columnName);
        return $this;
    }

    public function required(): self
    {
        $this->questions[array_key_last($this->questions)]["required"] = "Yes";
        return $this;
    }

    public function default($defaultValue): self
    {
        $this->questions[array_key_last($this->questions)]["default"] = $defaultValue;
        return $this;
    }

    private function setNameAndText($columnName)
    {
        $this->questions[array_key_last($this->questions)]["name"] = $columnName;
        $this->questions[array_key_last($this->questions)]["text"] = $columnName;
    }
}
