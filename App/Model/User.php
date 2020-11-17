<?php


namespace App\Model;


use Base\Db;
use Base\Model;
use App\Controller\User as ContrUser;


class User extends Model
{
    protected $id;
    protected $data;
    protected static $table = 'users';
    protected $idField = 'id';


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->get('name');
    }


    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->get('birthdate');
    }


    /**
     * @param mixed $name
     */

    public function setName($name): self
    {
        $this->set('name', $name);
        return $this;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): self
    {
        $this->set('password', $password);
        return $this;
    }

    /**
     * @param mixed $birthdate
     */
    public function setBirthdate($birthdate): self
    {
        $this->set("birthdate", $birthdate);
        return $this;
    }


    public function setPhotoId($id): self
    {
        $this->set("photo_id", $id);
        return $this;
    }

    public function getAvatarUrl()
    {
        $file = new File();
        if ($this->get('photo_id') == null){
            return "../../../photos/". $this->get('id'). '.' .$file->name ;

        }else {
            $file->getById($this->get('photo_id'));

           return "../../../photos/". $this->get('id'). '.' .$file->name ;
//
        }

    }


    public function update($fileId, $userId)
    {
        $table = self::$table;
        $db = Db::instance();
        $update = "UPDATE $table SET photo_id = $fileId WHERE id = $userId";
        $db->exec($update, __METHOD__);
    }


}