<?php

namespace Core\Database;

use PDO;

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $query = [];
    protected $dataArr = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select(string ...$columns)
    {
        $this->query['select'] = is_array($columns) ? implode(',', $columns) : '*';
        return $this;
    }

    public function where($column, $operator, $value, $mode = 'and')
    {
        if ($mode == 'and') {
            $this->query['where'][] = " $column $operator :$column ";
        } elseif ($mode == 'or') {
            $this->query['orWhere'][] = " $column $operator :$column";
        }

        $this->dataArr[$column] = $value;
        return $this;
    }

    public function andWhere($column, $operator, $value)
    {
        $this->where($column, $operator, $value);
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->where($column, $operator, $value, 'or');
        return $this;
    }

    public function limit(int $count): QueryBuilder
    {
        $this->query['limit'] = " LIMIT $count ";
        return $this;
    }

    public function offset(int $count): QueryBuilder
    {
        $this->query['offset'] = " OFFSET $count ";
        return $this;
    }

    public function orderBy(string $column, string $type = 'DESC'): QueryBuilder
    {
        $this->query['order'] = " ORDER BY $column $type ";
        return $this;
    }

    public function insert(array $data)
    {
        $this->dataArr = array_merge($this->dataArr, $data);

        $sql = $this->buildQuery('insert', $data);
        $this->runQuery($sql);
        return $this->pdo->lastInsertId();
    }

    public function update(array $data)
    {
        $this->dataArr = array_merge($this->dataArr, $data);

        $sql =  $this->buildQuery('update', $data);
        return $this->runQuery($sql)->rowCount();
    }

    public function delete()
    {
        $sql =  $this->buildQuery('delete');
        return $this->runQuery($sql)->rowCount();
    }

    public function get()
    {
        $sql = $this->buildQuery();
        return $this->runQuery($sql)->fetch(PDO::FETCH_OBJ);
    }

    public function getAll()
    {
        $sql = $this->buildQuery();
        return $this->runQuery($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    protected function buildQuery(string $mode = 'select', array $data = [])
    {
        switch ($mode) {
            case 'select':
                # code...
                $sql = 'SELECT ' . $this->query['select'] . ' FROM ' . $this->table;
                if (!empty($this->query['where'])) {
                    $sql .= ' WHERE ' . implode(' AND ', $this->query['where']);
                }
                if (!empty($this->query['orWhere'])) {
                    if (!empty($this->query['where'])) {
                        $sql .= ' OR ' . implode(' OR ', $this->query['orWhere']);
                    } else {
                        $sql .= ' WHERE ' . implode(' OR ', $this->query['orWhere']);
                    }
                }
                break;

            case 'update':
                # code...
                $sql = "UPDATE " .  $this->table . " SET ";

                foreach ($data as $key => $value) {
                    $sql .= " $key = :$key,";
                }

                $sql = rtrim($sql, ",");

                if (!empty($this->query['where']))
                    $sql .= ' WHERE ' . implode(' AND ', $this->query['where']);

                if (!empty($this->orwhere)) {

                    if (!empty($this->where))
                        $sql .= ' OR ' . implode(' OR ', $this->query['orWhere']);

                    else
                        $sql .= ' WHERE ' . implode(' OR ', $this->query['orWhere']);
                }
                break;

            case 'insert':
                # code...
                $keys = array_keys($data);
                $sql = "INSERT INTO " .  $this->table . ' (' . implode(',', $keys) . ') VALUES (:' . implode(',:', $keys) . ') ';

                break;

            case 'delete':
                # code...
                $sql = "DELETE FROM  " . $this->table . " ";
                if (!empty($this->query['where']))
                    $sql .= ' WHERE ' . implode(' AND ', $this->query['where']);

                if (!empty($this->orwhere)) {

                    if (!empty($this->where))
                        $sql .= ' OR ' . implode(' OR ', $this->query['orWhere']);

                    else
                        $sql .= ' WHERE ' . implode(' OR ', $this->query['orWhere']);
                }
                break;

            default:
                # code...
                break;
        }

        if ($mode != "insert") {

            if (!empty($this->query['order']))
                $sql .= $this->query['order'];

            if (!empty($this->query['limit']))
                $sql .= $this->query['limit'];

            if (!empty($this->query['offset']))
                $sql .= $this->query['offset'];
        }

        return $sql = preg_replace("/\s+/", " ", $sql);
    }

    protected function runQuery(string $sql)
    {
        /** prepare the pdo statement and execute */
        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->dataArr);

        /** reset query to default */
        $this->resetToDefault();

        return $statement;
    }

    protected function resetToDefault()
    {
        $this->query = [];
        $this->dataArr = [];
    }
}
