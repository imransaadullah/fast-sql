<?php

namespace FASTSQL\Traits\Tables;

trait Properties{
    private $tableName;
    private $fields = array();
    private $indexes = array();
    private $foreignKeys = array();
    private $engine;
    private $charset;
    private $collation;
    private $comment;
    private $autoIncrementValue;
    private $autoIncrementStep;
    private $temporary;
    private $ifNotExists;
    private $checkConstraints = array();
    private $tablespace;
    private $uniqueConstraints = array();
    private $defaultValues = array();
    private $triggers = array();
    private $addColumnStatements = array();
    private $newTableName;
    private $schema;

    public function temporary() {
        $this->temporary = true;
        return $this;
    }

    public function ifNotExists() {
        $this->ifNotExists = true;
        return $this;
    }

    public function tablespace($tablespace) {
        $this->tablespace = $tablespace;
        return $this;
    }

    
    public function autoIncrement($value) {
        $this->autoIncrementValue = $value;
        return $this;
    }

    public function autoIncrementStep($step) {
        $this->autoIncrementStep = $step;
        return $this;
    }

    public function engine($engine) {
        $this->engine = $engine;
        return $this;
    }

    public function charset($charset) {
        $this->charset = $charset;
        return $this;
    }

    public function collate($collation) {
        $this->collation = $collation;
        return $this;
    }

    public function comment($comment) {
        $this->comment = $comment;
        return $this;
    }
}