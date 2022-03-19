<?php


class Magento1 extends BasePlatform
{

    function __construct()
    {
        $xmlFile = simplexml_load_file("../../app/etc/local.xml");
        $config = $xmlFile->global->resources->default_setup->connection;

        $this->_host = $config->host;
        $this->_database = $config->dbname;
        $this->_username = $config->username;
        $this->_password = $config->password;
    }
}
