<?php


class Magento2 extends BasePlatform
{

    function __construct()
    {
        $rootPath = $this->GetPlatformRootPath();
        $env = include_once($rootPath . '/app/etc/env.php');
        $credentials = $env["db"]['connection']['default'];
        $this->_host = $credentials["host"];
        $this->_database = $credentials["dbname"];
        $this->_username = $credentials["username"];
        $this->_password = $credentials["password"];
    }
}
