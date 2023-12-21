<?php

namespace FASTSQL\Traits\Tables;

trait Actions{
    
    public function addField(\FASTSQL\FieldDefinition $field) {
        $this->fields[] = $field;
        return $this;
    }

    public function addFields(array $field) {
        foreach($field as $field) {
            $this->addField($field);
        }
        return $this;
    }

    public function addIndex($columns, $indexName = null) {
        $index = array('columns' => $columns);
        if ($indexName !== null) {
            $index['name'] = $indexName;
        }
        $this->indexes[] = $index;
        return $this;
    }

    public function addForeignKey($column, $foreignTable, $foreignColumn, $constraintName = null) {
        $foreignKey = array(
            'column' => $column,
            'foreignTable' => $foreignTable,
            'foreignColumn' => $foreignColumn,
        );
        if ($constraintName !== null) {
            $foreignKey['constraintName'] = $constraintName;
        }
        $this->foreignKeys[] = $foreignKey;
        return $this;
    }
    
    public function modifyAutoIncrementStatement() {
        $sql = "ALTER TABLE {$this->tableName}";

        if (isset($this->autoIncrementValue)) {
            $sql .= " AUTO_INCREMENT={$this->autoIncrementValue}";
        }
        if (isset($this->autoIncrementStep)) {
            $sql .= " AUTO_INCREMENT={$this->autoIncrementStep}";
        }

        $sql .= ";";

        return $sql;
    }

    public function addCheckConstraint($condition, $constraintName = null) {
        $constraint = array(
            'condition' => $condition,
            'name' => $constraintName,
        );
        $this->checkConstraints[] = $constraint;
        return $this;
    }

    public function addCheckConstraintStatements() {
        $sql = "";
        foreach ($this->checkConstraints as $constraint) {
            $constraintName = isset($constraint['name']) ? $constraint['name'] : null;
            $sql .= "ALTER TABLE {$this->tableName} ADD CONSTRAINT {$constraintName} CHECK ({$constraint['condition']});\n";
        }
        return $sql;
    }


    public function setTablespaceStatement() {
        if (isset($this->tablespace)) {
            return "ALTER TABLE {$this->tableName} TABLESPACE {$this->tablespace};";
        }
        return "";
    }

    public function addUniqueConstraint($columns, $constraintName = null) {
        $uniqueConstraint = array('columns' => $columns);
        if ($constraintName !== null) {
            $uniqueConstraint['name'] = $constraintName;
        }
        $this->uniqueConstraints[] = $uniqueConstraint;
        return $this;
    }

    public function addUniqueConstraintStatements() {
        $sql = "";
        foreach ($this->uniqueConstraints as $uniqueConstraint) {
            $constraintName = isset($uniqueConstraint['name']) ? $uniqueConstraint['name'] : null;
            $sql .= "ALTER TABLE {$this->tableName} ADD CONSTRAINT {$constraintName} UNIQUE (";

            if (is_array($uniqueConstraint['columns'])) {
                $sql .= implode(", ", $uniqueConstraint['columns']);
            } else {
                $sql .= $uniqueConstraint['columns'];
            }

            $sql .= ");\n";
        }
        return $sql;
    }

    public function addDefaultValue($column, $value) {
        $this->defaultValues[$column] = $value;
        return $this;
    }

    public function addDefaultValueStatements() {
        $sql = "";
        foreach ($this->defaultValues as $column => $value) {
            $sql .= "ALTER TABLE {$this->tableName} ALTER COLUMN {$column} SET DEFAULT {$value};\n";
        }
        return $sql;
    }

    public function addTrigger($name, $timing, $event, $body) {
        $trigger = array(
            'name' => $name,
            'timing' => $timing,
            'event' => $event,
            'body' => $body,
        );
        $this->triggers[] = $trigger;
        return $this;
    }

    public function addTriggerStatements() {
        $sql = "";
        foreach ($this->triggers as $trigger) {
            $sql .= "CREATE TRIGGER {$trigger['name']} {$trigger['timing']} {$trigger['event']} ON {$this->tableName} FOR EACH ROW {$trigger['body']};\n";
        }
        return $sql;
    }

    public function addColumn($column, $definition) {
        $this->addColumnStatements[] = "ALTER TABLE {$this->tableName} ADD COLUMN {$column} {$definition};";
        return $this;
    }

    public function addColumnStatements() {
        return implode("\n", $this->addColumnStatements);
    }

    public function renameTable($newTableName) {
        $this->newTableName = $newTableName;
        return $this;
    }

    public function getRenameTableStatement() {
        if (isset($this->newTableName)) {
            return "ALTER TABLE {$this->tableName} RENAME TO {$this->newTableName};";
        }
        return "";
    }

    public function setSchema($schema) {
        $this->schema = $schema;
        return $this;
    }

    public function setSchemaStatement() {
        if (isset($this->schema)) {
            return "ALTER TABLE {$this->tableName} SET SCHEMA {$this->schema};";
        }
        return "";
    }

    public function getAlterTableStatement() {
        $sql = "ALTER TABLE `{$this->tableName}`";

        if (isset($this->engine)) {
            $sql .= " ENGINE={$this->engine}";
        }
        if (isset($this->charset)) {
            $sql .= " CHARACTER SET={$this->charset}";
        }
        if (isset($this->collation)) {
            $sql .= " COLLATE={$this->collation}";
        }

        if (isset($this->comment)) {
            $sql .= " COMMENT='{$this->comment}'";
        }

        $sql .= ";";

        return $sql;
    }

    public function getDropTableStatement() {
        return "DROP TABLE IF EXISTS `{$this->tableName}`;";
    }

    public function getCreateTableStatement() {
        $sql = "CREATE";

        if ($this->temporary) {
            $sql .= " TEMPORARY";
        }

        $sql .= " TABLE";

        if ($this->ifNotExists) {
            $sql .= " IF NOT EXISTS";
        }

        $sql .= " `{$this->tableName}` (";

        $fieldStatements = array();

        foreach ($this->fields as $field) {
            $fieldStatements[] = $field->generateFieldStatement();
        }

        $sql .= implode(", ", $fieldStatements);

        $constraints = array();
        foreach ($this->fields as $field) {
            if ($field->hasPrimaryKey()) {
                $constraints[] = "PRIMARY KEY ({$field->getName()})";
            }
            if ($field->isUnique()) {
                $constraints[] = "UNIQUE ({$field->getName()})";
            }
        }

        foreach ($this->foreignKeys as $foreignKey) {
            $constraints[] = "FOREIGN KEY ({$foreignKey['column']}) REFERENCES {$foreignKey['foreignTable']} ({$foreignKey['foreignColumn']})";
        }

        if (!empty($constraints)) {
            $sql .= ", " . implode(", ", $constraints);
        }

        $sql .= ")";

        if (isset($this->engine)) {
            $sql .= " ENGINE={$this->engine}";
        }
        if (isset($this->charset)) {
            $sql .= " CHARACTER SET={$this->charset}";
        }
        if (isset($this->collation)) {
            $sql .= " COLLATE={$this->collation}";
        }

        $sql .= ";";

        return $sql;
    }

    public function getCreateIndexStatements() {
        $sql = "";
        foreach ($this->indexes as $index) {
            $indexName = isset($index['name']) ? $index['name'] : null;
            $sql .= "CREATE INDEX {$indexName} ON {$this->tableName} (";

            if (is_array($index['columns'])) {
                $sql .= implode(", ", $index['columns']);
            } else {
                $sql .= $index['columns'];
            }

            $sql .= ");\n";
        }
        return $sql;
    }
}