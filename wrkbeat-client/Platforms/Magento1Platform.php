<?php


class Magento1 extends Platform
{

    function __construct()
    {
        $rootPath = $this->GetPlatformRootPath();
        $xmlFile = simplexml_load_file($rootPath . '/app/etc/local.xml');
        $config = $xmlFile->global->resources->default_setup->connection;

        $this->_host = $config->host;
        $this->_database = $config->dbname;
        $this->_username = $config->username;
        $this->_password = $config->password;
    }
}
