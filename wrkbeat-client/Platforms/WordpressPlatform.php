<?php


class WordpressPlatform extends BasePlatform
{

    function __construct()
    {

        include_once('../../wp-config.php');

        $this->_host = DB_HOST;
        $this->_dbname = DB_NAME;
        $this->_username = DB_USER;
        $this->_password = DB_PASSWORD;
    }
}
