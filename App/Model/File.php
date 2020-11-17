<?php


namespace App\Model;


use Base\Db;
use Base\Model;

/**
 * Class File
 * @package App\Model
 *
 * @property-read $name
 * @property-read $size
 * @property-read $upload_at
 */
class File extends Model
{
    protected $id;
    public $data;
    protected static $table = 'files';
    protected $idField = 'id';

    public function __set($name, $value)
    {
        $this->set($name, $value);
        return $this;
    }

    public function __get($name)
    {
        return $this->get($name);
    }


    public static function getFileList(int $user_id)
    {
        $db = Db::instance();
        $table = static::$table;
        $select = "SELECT * FROM $table WHERE user_id = $user_id ORDER BY id ASC LIMIT 50";
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

}