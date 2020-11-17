<?php


namespace App\Model;


use Base\Model;

class FileUpload extends Model
{



    public static function fileUoloader($filename, $destinataion)
    {
        if (file_exists($_FILES['file']['tmp_name'])){
         return  move_uploaded_file($filename, $destinataion. $_FILES['file']['name']);

        }else{
            return false;
        }

    }

}