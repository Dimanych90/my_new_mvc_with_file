<?php


namespace Base;


abstract class Controller
{


    protected $view;
    protected $user;

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }


    public function setView(View $view)
    {
        $this->view = $view;
        return $this;
    }

    public function redirect($url)
    {
        header("Location:" . $url);
        exit();
    }
}