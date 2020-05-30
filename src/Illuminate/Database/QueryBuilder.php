<?php

namespace API\Illuminate\Database;

class QueryBuilder
{
    /**
     * Sql query injection
     *
     * @var array
     */
    protected $sql = [];
    /**
     * Values for injecting
     *
     * @var array
     */
    public $values = [];
    /**
     * Selector
     */
    public function select($fields = '*')
    {
        $this->reset();
        $this->sql['select'] = "SELECT {$fields} ";
        return $this;
    }
    /**
     * From query
     *
     * @param [type] $table
     * @return void
     */
    public function from($table)
    {
        $this->sql['from'] = " FROM {$table} ";
        return $this;
    }
    /**
     * Where query
     *
     * @param [type] $column
     * @param [type] $value
     * @param string $operator
     * @return void
     */
    public function where($column, $value, $operator = "=")
    {
        $this->sql['where'][] = "{$column} {$operator} ? ";
        $this->values[] = $value;
        return $this;
    }
    /**
     * Data fetching in the order by sequences
     *
     * @param [type] $field
     * @param [type] $order
     * @return void
     */
    public function orderBy($field, $order)
    {
        $this->sql['order_by'] = "ORDER BY {$field} {$order} ";
        return $this;
    }
    /**
     * Limit query orders
     *
     * @param [type] $number
     * @return void
     */
    public function limit($number)
    {
        $this->sql['limit'] = " LIMIT {$number}";
        return $this;
    }
    public function update($table)
    {
        $this->reset();
        $this->sql['update'] = "UPDATE {$table} ";
        return $this;
    }
    /**
     * Setter
     *
     * @param array $data
     * @return void
     */
    public function set($data = [])
    {
        $this->sql['set'] = "SET ";

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->sql['set'] .= "{$key} = ? ";
                if (next($data)) {
                    $this->sql['set'] .= ", ";
                }
                $this->values[] = $value;
            }
        }

        return $this;
    }
    /**
     * Insert into table
     *
     * @param [type] $table
     * @return void
     */
    public function insert($table)
    {
        $this->reset();
        $this->sql['insert'] = "INSERT INTO {$table} ";
        return $this;
    }
    /**
     * Sql query builder
     *
     * @return void
     */
    public function sql()
    {
        $sql = '';
        if (!empty($this->sql)) {
            foreach ($this->sql as $key => $value) {
                if ($key == 'where') {
                    $sql .= ' WHERE ';
                    foreach ($value as $where) {
                        $sql .= $where;
                        if (count($value) > 1 and next($value)) {
                            $sql .= ' AND ';
                        }
                    }
                } else {
                    $sql .= $value;
                }
            }
        }
        return $sql;
    }

    /**
     * Reset function
     *
     * @return void
     */
    protected function reset()
    {
        if (is_array($this->sql) && is_array($this->values)) {
            $this->sql = [];
            $this->values = [];
        }
    }
    /**
     * Delete from database
     *
     * @param string $fields
     * @return void
     */
    public function delete($fields = "")
    {
        $this->reset();
        $this->sql['delete'] = "DELETE {$fields} ";
        return $this;
    }
}
