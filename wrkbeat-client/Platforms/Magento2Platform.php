<?php


class Magento2 extends BasePlatform
{

    function __construct()
    {
        $env = include_once('../../app/etc/env.php');
        $credentials = $env["db"]['connection']['default'];
        $this->_host = $credentials["host"];
        $this->_database = $credentials["dbname"];
        $this->_username = $credentials["username"];
        $this->_password = $credentials["password"];
    }
}
