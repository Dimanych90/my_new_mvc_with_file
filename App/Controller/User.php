<?php


namespace App\Controller;


use Base\Controller;
use Base\Model;
use Base\Session;

class User extends Controller
{
    protected $view;

    public function loginedAction()
    {
        $file = new \App\Model\File();
        $user = new \App\Model\User();
        $user->setName($_POST['name']);
        $user->setPassword(sha1('sshk.k.' . $_POST['password']));
//        $user->setPhotoId(0);
        $user->setBirthdate('1990-15-06');
        $user->userAuth();
        if (!empty($user->userAuth())) {
            Session::instance()->save($user->getId());
            $this->redirect('../file/upload');
//            header("location:" . '../file/upload');
//            $this->view->render("file/upload.phtml", ["title"=>"Download files"]);
        } else {
            $this->view->render("user/404.phtml", ["title" => "ERROR 404"]);
        }
    }


    public function loginAction()
    {
        $this->view->render("user/login.phtml", ["title" => "Authorize"]);
    }

    public function registerAction()
    {
        if ($_POST['name']) {
            $user = new \App\Model\User();
            $file = new \App\Model\File();
            $user->setName($_POST['name']);
            $user->setPassword(sha1('sshk.k.' . $_POST['password']));
            $user->setBirthdate('1990-15-06');
//            $user->setPhotoId(0);
            $user->userAuth();
            if (empty($user->userAuth())) {
                $user->save();
            }
            $this->redirect('login');
        } else {
            $this->view->render("user/register.phtml", ["title" => "Registration form"]);

        }

    }

    public function userlistAction()
    {
        $limit = $_GET['limit'] ?? 10;
        $offset = $_GET['offset'] ?? 0;

        /** @var \App\Model\User[] $users */
        $users = \App\Model\User::getList($limit, $offset);

        $this->view->render("user/list.phtml", ["users" => $users]);

    }


}