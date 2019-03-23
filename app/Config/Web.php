<?php
namespace App\Config;

class Web
{
    public $name = 'Wibbo';
    public $avatar = '//cdn.wibbo.me/habbo-imaging/avatarimage?figure=';
    public $cdn = "//localhost";
    public $swf = "//localhost";
    public $asset = "//localhost";
    public $url = '//localhost';

    public function __construct()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->url = '//' . $_SERVER['HTTP_HOST'];
            $this->cdn = '//' . $_SERVER['HTTP_HOST'];
            $this->swf = '//' . $_SERVER['HTTP_HOST'];
            $this->asset = '//' . $_SERVER['HTTP_HOST'];
        }
    }
}
