<?php

namespace API\Illuminate\Database;

use API\Config\Config;
use API\Eloquent\Singleton;
use PDO;
use PDOException;

class Database
{
    use Singleton;
    /**
     * PDO protected
     *
     * @var [type]
     */
    protected $pdo = null;
    /**
     * Connection check out
     *
     * @var boolean
     */
    protected $isConnected = false;
    /**
     * Middleware database connection
     *
     * @var array
     */
    protected $middleware = [];
    /**
     * Connect ot the database
     *
     * @return void
     */
    protected function connect()
    {
        $this->middleware = Config::group("Middleware/database");

        try {

            $this->pdo = new PDO($this->middleware["driver"] . ":host=" . $this->middleware["host"] . ";dbname=" . $this->middleware["db_name"],
                $this->middleware["db_user"],
                $this->middleware["db_password"],
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->middleware["charset"]]);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->isConnected = true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
    /**
     * CloseConnection
     *
     * @return void
     */
    protected function closeConnection()
    {
        if ($this->pdo !== null) {
            $this->pdo = null;
        }
    }
    /**
     * Execute for query
     *
     * @param [type] $sql
     * @param array $values
     * @return void
     */
    public function execute(string $sql, array $values = []): bool
    {
        $sql = $this->filterQuery($sql);

        $sth = $this->pdo->prepare($sql);

        return $sth->execute($values);
    }
    /**
     * Filter for query
     *
     * @param [type] $sql
     * @return void
     */
    protected function filterQuery(string $sql)
    {
        return trim(str_replace("\r", " ", $sql));
    }
    /**
     * Query for database fetching
     *
     * @param [type] $sql
     * @param [type] $values
     * @param [type] $mode
     * @return void
     */

    public function query(string $sql, array $values = [], bool $mode = true): array
    {
        $sql = $this->filterQuery($sql);

        $sth = $this->pdo->prepare($sql);

        $sth->execute($values);

        $result = $sth->fetchAll($mode ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ);

        return !!$result ? $result : [];
    }
    /**
     * getLastInsertId
     *
     * @return void
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertid();
    }

}
