<?php


namespace Base;


class View
{

    protected $tamlatePath;

    /**
     * @return mixed
     */
    public function setTamlatePath(string $path)
    {
         $this->tamlatePath = $path;
         return $this;
    }

    public function render(string $tpl, $data = [])
    {
        extract($data);
        include $this->tamlatePath. '/' . $tpl;
    }
}