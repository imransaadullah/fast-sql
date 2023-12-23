<?php

namespace FASTSQL;

/**
 * Class SecureSQLGenerator
 *
 * A secure and flexible SQL query generator with support for parameter binding, transactions, and caching.
 */
class SecureSQLGenerator
{
    /**
     * @var PDO The PDO instance for database connection.
     */
    protected $pdo;

    /**
     * @var string The generated SQL query.
     */
    protected $query;

    /**
     * @var array The parameters for the SQL query.
     */
    protected $params;

    /**
     * @var string The subquery used in the SQL statement.
     */
    protected $subquery;

    /**
     * @var bool Indicates whether the SELECT keyword has been added to the query.
     */
    private $selectAdded = false;

    /**
     * @var bool Indicates whether a transaction is currently in progress.
     */
    private $inTransaction = false;

    /**
     * @var array Cache for storing query results.
     */
    private $cache = [];

    /**
     * SecureSQLGenerator constructor.
     *
     * @param PDO $pdo The PDO instance for database connection.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->clearQuery();
    }

    /**
     * Adds SELECT clause to the query.
     *
     * @param array|string $columns The columns to select.
     * @return $this
     */
    public function select($columns)
    {
        if (!$this->selectAdded) {
            $this->query = "SELECT " . $this->query;
            $this->selectAdded = true;
        }

        $columnsString = $this->parseColumns($columns);
        $this->query .= " {$columnsString}";

        return $this;
    }


    /**
     * Adds DISTINCT clause to the query.
     *
     * @param array|null $columns The columns to select distinct values from.
     * @return $this
     */    public function distinct($columns = null)
    {
        if (!$this->selectAdded) {
            $this->query = "SELECT " . $this->query;
            $this->selectAdded = true;
        }

        $distinct = 'DISTINCT';

        if ($columns !== null) {
            $columnsString = $this->parseColumns($columns);
            $this->query .= " {$distinct} {$columnsString}";
        } else {
            $this->query .= " {$distinct} *";
        }

        return $this;
    }

    private function parseColumns($columns)
    {
        if (is_array($columns)) {
            return implode(', ', $columns);
        }

        return $columns;
    }

    public function from($table)
    {
        $this->query .= " FROM " . $this->sanitizeIdentifier($table);
        return $this;
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_map([$this, 'sanitizeIdentifier'], array_keys($data)));
        $values = implode(', ', array_map([$this, 'bindValue'], array_values($data)));

        $this->query = "INSERT INTO " . $this->sanitizeIdentifier($table) . " ({$columns}) VALUES ({$values})";
        return $this;
    }

    public function update($table, $data)
    {
        $setClause = implode(', ', array_map(function ($column, $value) {
            return $this->sanitizeIdentifier($column) . ' = ' . $this->bindValue($value);
        }, array_keys($data), array_values($data)));

        $this->query = "UPDATE " . $this->sanitizeIdentifier($table) . " SET {$setClause}";
        return $this;
    }

    public function delete($table)
    {
        $this->query = "DELETE FROM " . $this->sanitizeIdentifier($table);
        return $this;
    }

    public function where($conditions)
    {
        $whereClause = implode(' AND ', array_map(function ($column, $value) {
            return $this->sanitizeIdentifier($column) . ' = ' . $this->bindValue($value);
        }, array_keys($conditions), array_values($conditions)));

        $this->query .= " WHERE {$whereClause}";
        return $this;
    }

    public function orderBy($column, $order = 'ASC')
    {
        $this->query .= " ORDER BY " . $this->sanitizeIdentifier($column) . " {$order}";
        return $this;
    }

    public function limit($limit)
    {
        $this->query .= " LIMIT :limit";
        $this->params[':limit'] = $limit;
        return $this;
    }

    public function andWhere($conditions, $compoundOperator = null)
    {
        return $this->addCondition('AND', $conditions, $compoundOperator);
    }

    public function orWhere($conditions, $compoundOperator = null)
    {
        return $this->addCondition('OR', $conditions, $compoundOperator);
    }

    public function notWhere($conditions, $compoundOperator = null)
    {
        return $this->addCondition('NOT', $conditions, $compoundOperator);
    }

    protected function addCondition($logicalOperator, $conditions, $compoundOperator = null)
    {
        $conditionClause = '';
        foreach ($conditions as $column => $value) {
            $column = $this->sanitizeIdentifier($column);
            $conditionClause .= "{$column} = :{$column} {$logicalOperator} ";
            $this->params[":{$column}"] = $value;
        }
        $conditionClause = rtrim($conditionClause, " {$logicalOperator} ");

        if ($compoundOperator) {
            $this->query .= " {$compoundOperator} ({$conditionClause})";
        } else {
            $this->query .= " {$logicalOperator} ({$conditionClause})";
        }

        return $this;
    }

    protected function buildConditions($conditions, $logicalOperator = 'AND')
    {
        $conditionClause = '';
        foreach ($conditions as $column => $value) {
            $column = $this->sanitizeIdentifier($column);
            $conditionClause .= "{$column} = :{$column} {$logicalOperator} ";
            $this->params[":{$column}"] = $value;
        }
        return rtrim($conditionClause, " {$logicalOperator} ");
    }

    public function subquery($selectQuery)
    {
        $this->subquery = "($selectQuery)";
        return $this;
    }

    public function alias($alias)
    {
        $this->query .= " AS " . $this->sanitizeIdentifier($alias);
        return $this;
    }

    public function innerJoin($table, $onConditions)
    {
        return $this->join('INNER JOIN', $table, $onConditions);
    }

    public function leftJoin($table, $onConditions)
    {
        return $this->join('LEFT JOIN', $table, $onConditions);
    }

    public function rightJoin($table, $onConditions)
    {
        return $this->join('RIGHT JOIN', $table, $onConditions);
    }

    public function fullJoin($table, $onConditions)
    {
        return $this->join('FULL JOIN', $table, $onConditions);
    }

    protected function join($type, $table, $onConditions)
    {
        $table = $this->sanitizeIdentifier($table);
        $onClause = $this->buildConditions($onConditions, 'AND');
        $this->query .= " {$type} {$table} ON {$onClause}";
        return $this;
    }

    public function intersect($selectQuery)
    {
        $this->setQueryWithSetOperation("INTERSECT", $selectQuery);
        return $this;
    }

    public function union($selectQuery)
    {
        $this->setQueryWithSetOperation('UNION', $selectQuery);
        return $this;
    }

    public function except($selectQuery)
    {
        $this->setQueryWithSetOperation('EXCEPT', $selectQuery);
        return $this;
    }

    private function setQueryWithSetOperation($operation, $selectQuery)
    {
        if (!empty($this->query)) {
            $this->query .= " {$operation} ({$selectQuery})";
        } else {
            $this->query = $selectQuery;
        }
    }

    public function setQuery(string $selectQuery){
        $this->query = $selectQuery;
        return $this;
    }

    public function beginTransaction()
    {
        if (!$this->inTransaction) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
        return $this;
    }

    public function commit()
    {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;
        }
        return $this;
    }

    public function rollback()
    {
        if ($this->inTransaction) {
            $this->pdo->rollBack();
            $this->inTransaction = false;
        }
        return $this;
    }

    public function execute($useCache = false)
    {
        $cacheKey = $this->generateCacheKey();

        if ($useCache && isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        try {
            if ($this->isTableCreationQuery()) {
                $this->executeTableCreationQuery();
                $result = true; // Assuming success for table creation
            }elseif ($this->isInsertQuery()) {
                $result = $this->executeInsertQuery();
                // ; // Assuming success for table creation
            } else {
                $result = $this->executeSelectQuery();
                if ($useCache) {
                    $this->cache[$cacheKey] = $result;
                }
            }

            $this->clearQuery();
            return $result;
        } catch (\PDOException $e) {
            if (!$this->isTableCreationQuery()) {
                $this->rollback();
            }
            throw $e;
        }
    }

    protected function executeSelectQuery()
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function executeTableCreationQuery()
    {
        $statement = $this->pdo->prepare($this->query);
        return $statement->execute();
    }

    protected function executeInsertQuery()
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->params);
        if ($this->pdo->lastInsertId()) return $this->pdo->lastInsertId();
        return false;
    }

    public function isTableCreationQuery()
    {
        return strpos(strtoupper($this->query), 'CREATE TABLE') !== false;
    }

    public function isInsertQuery()
    {
        return strpos(strtoupper($this->query), 'INSERT INTO') !== false;
    }

    private function clearQuery()
    {
        $this->query = '';
        $this->params = [];
        $this->selectAdded = false;
    }

    public function count($column = '*')
    {
        $this->query .= "COUNT({$column})";
        return $this;
    }

    public function sum($column)
    {
        $this->query .= "SUM({$column})";
        return $this;
    }

    public function avg($column)
    {
        $this->query .= "AVG({$column})";
        return $this;
    }

    public function min($column)
    {
        $this->query .= "MIN({$column})";
        return $this;
    }

    public function max($column)
    {
        $this->query .= "MAX({$column})";
        return $this;
    }

    public function concat($columns)
    {
        $sanitizedColumns = array_map([$this, 'sanitizeIdentifier'], $columns);
        $columnsList = implode(', ', $sanitizedColumns);
        $this->query .= "CONCAT({$columnsList})";
        return $this;
    }

    public function substring($column, $start, $length = null)
    {
        $column = $this->sanitizeIdentifier($column);
        if ($length !== null) {
            $this->query .= "SUBSTRING({$column}, :start, :length)";
            $this->params[':start'] = $start;
            $this->params[':length'] = $length;
        } else {
            $this->query .= "SUBSTRING({$column}, :start)";
            $this->params[':start'] = $start;
        }
        return $this;
    }

    public function dateFunction($function, $column, $dateValue)
    {
        $column = $this->sanitizeIdentifier($column);
        $this->query .= "{$function}({$column}, :dateValue)";
        $this->params[':dateValue'] = $dateValue;
        return $this;
    }

    public function add($column, $value)
    {
        $column = $this->sanitizeIdentifier($column);
        $this->query .= "{$column} + :value";
        $this->params[':value'] = $value;
        return $this;
    }

    public function subtract($column, $value)
    {
        $column = $this->sanitizeIdentifier($column);
        $this->query .= "{$column} - :value";
        $this->params[':value'] = $value;
        return $this;
    }

    public function multiply($column, $value)
    {
        $column = $this->sanitizeIdentifier($column);
        $this->query .= "{$column} * :value";
        $this->params[':value'] = $value;
        return $this;
    }

    public function divide($column, $value)
    {
        $column = $this->sanitizeIdentifier($column);
        $this->query .= "{$column} / :value";
        $this->params[':value'] = $value;
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getParams()
    {
        return $this->params;
    }

    protected function sanitizeIdentifier($identifier)
    {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }

    protected function bindValue($value)
    {
        $param = ':param' . count($this->params);
        $this->params[$param] = $value;
        return $param;
    }

    private function generateCacheKey()
    {
        return md5($this->query . json_encode($this->params));
    }
}
