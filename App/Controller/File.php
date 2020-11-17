<?php


namespace App\Controller;

use App\Model\FileUpload;
use Base\Controller;
use Base\Session;
class File extends Controller
{

    public function uploadAction()
    {
        $user = new \App\Model\User();
        $user->setId(Session::instance()->getUserId());

//var_dump($_FILES);die();

        if ($_FILES['file']) {
            $file = new \App\Model\File([
                "name" => $_FILES["file"]['name'],
                "user_id" => $user->getId(),
                "upload_at" => date("d:m:Y H:i"),
                "size" => $_FILES['file']['size']
            ]);
            $file->save();
            FileUpload::fileUoloader($_FILES['file']['tmp_name'],'photos/'. $user->getId() .'.' );



            $user->update($file->getId(), $user->getId());
        }

        $files = \App\Model\File::getFileList($user->getId());

        $this->view->render("file/upload.phtml",
            ['title' => 'Download file',
                'files' => $files]);
    }

//    public function listAction()
//    {
//        $user = new \App\Model\User();
//        $files = \App\Model\File::getFileList(7);
//
//        $this->view->render("file/list.phtml",["tiltle"=>"THe list of files", "files"=>$files]);
//    }
}