<?php


namespace Base;


use App\Controller\Index;

class Db
{

    /** @var \PDO */
    private $_pdo;
    /** @var array $_fetch */
    private $_fetch = [];

    /** @var array */
    private $_log = [];

    const DB_NEWUSER = 1;
    const DB_POSTS = 2;
    const DB_USERS = 3;
    const DB_FILES = 4;
    /** @var array */
    private static $_tableNumberMap =
        ["newusers" => self::DB_NEWUSER,
            "posts" => self::DB_POSTS,
            "users" => self::DB_USERS,
            "files" => self::DB_FILES];

    const QUERY_TYPE_SELECT = 1;
    const QUERY_TYPE_INSERT = 2;
    const QUERY_TYPE_UPDATE = 3;
    const QUERY_TYPE_DELETE = 4;

    private static $_instance;

    private $_dbNames;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    private function getDbConnection(int $db_num)
    {
        $this->_pdo = [];
        if (!isset($this->_pdo[$db_num])) {


            try {
                $db_type = $_ENV['DB_TYPE'] ?? '';
                $db_host = $_ENV['DB_HOST'] ?? '';
                $db_name = $_ENV['DB_NAME'] ?? '';
                $db_user = $_ENV['DB_USER'] ?? '';
                $db_password = $_ENV['DB_PASSWORD'] ?? '';
                $t = microtime(1);
                $this->_pdo = new \PDO("$db_type:host=$db_host;dbname=$db_name", "$db_user", "$db_password");
                $this->_log = [
                    microtime(1) - $t,
                    $db_name,
                    'connect',
                    0
                ];
                $this->_dbNames[$db_num] = $db_name;
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        } else {
            return $this->_pdo[$db_num];
        }
    }

    private function getArrayFlip(string $table_name)
    {
        $map = array_flip(self::$_tableNumberMap);
//        var_dump($map);
        return $map[$table_name];
    }

    public function getQueryTYpe($query)
    {

        $queryType = substr($query, 0, 6);

        if ($queryType == 'select') {
            return self::QUERY_TYPE_SELECT;
        } elseif ($queryType == 'insert') {
            return self::QUERY_TYPE_INSERT;
        } elseif ($queryType == 'update') {
            return self::QUERY_TYPE_UPDATE;
        } elseif ($queryType == 'delete') {
            return self::QUERY_TYPE_DELETE;
        }
        return $queryType;

    }

    /**
     * @return mixed
     */
    public function getFetch()
    {
        return $this->_fetch;
    }

    public function getTableNameFromQuery(string $query)
    {
        $query = strtolower($query);
        $query = trim($query);

        $queryType = $this->getQueryTYpe($query);
        switch ($queryType) {
            case self::QUERY_TYPE_SELECT:
            case self::QUERY_TYPE_DELETE:
                $query = preg_replace('/\s+/', ' ', $query);
                $parts = explode(' from ', $query);
                $table = explode(' ', $parts[1])[0];
                break;
            case self::QUERY_TYPE_INSERT;
                $query = preg_replace('/\s+/', ' ', $query);
                $parts = explode(' into ', $query);
                $table = explode(' ', $parts[1])[0];
                break;
            case self::QUERY_TYPE_UPDATE:
                $query = preg_replace('/\s+/', ' ', $query);
                $parts = explode(' ', $query);
                $table = explode(' ', $parts[1])[0];
                break;
            default:
                throw new \PDOException("This table does not exist in ", $query);
        }

        return str_replace('`', '', $table);

    }

    public function fetchAll(string $query, $_method, array $param)
    {
        $table = $this->getTableNameFromQuery($query);

        if (!isset(self::$_tableNumberMap[$table])) {
            throw new \PDOException("There's no table in the " . $table);
        }
        $db_num = self::$_tableNumberMap[$table];
        $this->getDbConnection($db_num);
        $pdo = $this->_pdo->prepare($query);
        $t = microtime(1);
        $ret = $pdo->execute($param);

        if (!$ret) {
            var_dump($this->_pdo->errorInfo());
            trigger_error($this->_pdo->errorInfo()[0]);
            return -1;
        }
        $affectedRows = $pdo->rowCount();
        $this->_log = [
            microtime(1) - $t,
            $this->_dbNames[$db_num],
            $pdo->queryString,
            $_method,
            $affectedRows
        ];
        $this->_fetch = $pdo->fetchAll(\PDO::FETCH_ASSOC);

//var_dump($this->getFetch());
//die();
//        return $affectedRows;

        return $this->_fetch;

    }

    public function fetchOne(string $query, string $_method, array $param = [])
    {

        $fetchOne[] = $this->fetchAll($query, $_method, $param);

        return reset($fetchOne);


    }
//    public function fetchOne(string $query, string $_method , array $params = []): array
//    {
//        $data[] = $this->fetchAll($query, $_method, $params);
//        return $data[''] ? reset($data) : [];
//    }

    public function exec($query, $_method, $param = [])
    {
//        var_dump($query,$param); die();

        $table = $this->getTableNameFromQuery($query);

        if (!isset(self::$_tableNumberMap[$table])) {
            throw new \PDOException("There's no table in the " . $table);
        }
        $db_num = self::$_tableNumberMap[$table];
        $this->getDbConnection($db_num);
        $pdo = $this->_pdo->prepare($query);

        $t = microtime(1);
         $ret = $pdo->execute($param);

        if (!$ret) {
            $controllerUser = new Index();
            $controllerUser->redirect("/register");
//            var_dump($this->_pdo->errorInfo());
//            trigger_error($this->_pdo->errorInfo()[0]);
//            return -1;
        }

        $affectedRows = $pdo->rowCount();
        $this->_log = [
            microtime(1) - $t,
            $this->_dbNames[$db_num],
            $pdo->queryString,
            $_method,
            $affectedRows

        ];

//         $this->_fetch = $pdo->fetchAll(\PDO::FETCH_ASSOC);
//        return $this->_fetch;
        return $affectedRows;

    }

    public function lastInsertId()
    {
      return $this->_pdo->lastInsertId();
    }

    public function getLog()
    {
//        if ($asHtml) {
//            $html = "<br><br>";

        $this->_log[0] = round($this->_log[0], 4);

        if ($this->_log) {
            foreach ($this->_log as $item) {
//                    list($queryTime, $dbName, $text, $affectedRows) = $item;
//                    $item .= round($queryTime, 4) . ' <br> ' . $dbName . '<br> ' . $text . ' <br>' . $affectedRows . '<br>';
                round($this->_log[0], 4);
                echo $item;


                echo '<hr>';
//                }
            }
//                return $html;
        } else {
            return $this->_log;
        }

    }
}