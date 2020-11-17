<?php


namespace Base;

use Base\Db;
use App\Controller\User;
use App\Model\User as ModUser;


class Model
{
    protected $data;
    protected $id;
    protected static $table;
    protected $idField = 'id';


    public function __construct(array $data = [])
    {
        $this->data = $data;
    }


    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        if (!$this->id && isset($this->data['id'])) {
            $this->setId($this->data['id']);
        }
        return $this->id;
    }

    public function save()
    {

        $db = Db::instance();
        $fields = implode(',', array_keys($this->data));
//       var_dump($this->data);die();
        $keyValues = array_map(function ($values) {
            return ':' . $values;
        }, array_keys($this->data));
        $param = implode(',', $keyValues);
        $data = array_combine($keyValues,
            array_values($this->data));
        $table = static::$table;
        $insert = "INSERT INTO $table ($fields) VALUES ($param)";
        $result = $db->exec($insert, __METHOD__, $data);
        $this->setId($db->lastInsertId());
        return $result;
    }

    public function userAuth()
    {
        $db = Db::instance();
        $fields = implode(',', array_keys($this->data));
//       var_dump($this->data);die();
        $keyValues = array_map(function ($values) {
            return ':' . $values;
        }, array_keys($this->data));
        $param = implode(',', $keyValues);
        $data = array_combine($keyValues,
            array_values($this->data));
        $table = static::$table;
        $select = "SELECT * FROM $table WHERE name = :name AND
 password = :password  AND birthdate = :birthdate";
        $fetch = $db->fetchOne($select, __METHOD__, $data);
        if ($fetch) {
            $this->setId($fetch[0]['id']);
            return $fetch;
        } else {
            header("location:" . 'register');
        }

        return $fetch;
    }

    public function getById(int $id)
    {
        $table = static::$table;
        $select = "SELECT * FROM $table WHERE id = $id";
        $data = \Base\Db::instance()->fetchOne($select, __METHOD__);
        if (!$data) {
            return null;
        }

        $this->data = $data[0];


        return $data[0];
    }


    public static function getList(int $limit, int $offset)
    {
        $db = Db::instance();
        $table = static::$table;
        $select = "SELECT * FROM $table LIMIT $limit OFFSET $offset";
        $data = $db->fetchAll($select, __METHOD__, []);

        if (!$data) {
            return [];
        }
        $result = [];
        foreach ($data as $datum) {
            $model = new static();
            $model->setId($datum['id']);
            $model->data = $datum;
            $result[] = $model;
        }

        return $result;
    }


    protected function get($filter)
    {
        return $this->data[$filter] ?? null;

    }

    protected function set($filter, $value)
    {
        $this->data[$filter] = $value;
//        var_dump($value);
        return $this;
    }
}