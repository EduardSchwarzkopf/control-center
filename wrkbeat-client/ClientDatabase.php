<?php

class ClientDatabase {
    private $_environment = null;
    private $_host = null;
    private $_database = null;
    private $_username = null;
    private $_password = null;

    function __construct($environment) {
        $this->SetDatabaseAttributes();
    }


    private function SetDatabaseAttributes() {

        $dbCredentials = [];

        switch ($this->_environment) {
            case 1:
                // Wordpress
                include('../wp-config.php');
                $dbCredentials["host"] = DB_HOST;
                $dbCredentials["dbname"] = DB_NAME;
                $dbCredentials["username"] = DB_USER;
                $dbCredentials["password"] = DB_PASSWORD;
                $dbCredentials = [DB_HOST, DB_NAME, DB_USER, DB_PASSWORD];
                break;
            case 2:
                // Magento 2
                $env = include('../app/etc/env.php');
                $db = $env["db"];
                $credentials = $db['connection']['default'];
                $dbCredentials = [
                    $credentials["host"],
                    $credentials["dbname"],
                    $credentials["username"],
                    $credentials["password"],
                ];
                break;
            case 3:
                // Magento 1
                $xmlFile=simplexml_load_file("../app/etc/local.xml");
                $config = $xmlFile->global->resources->default_setup->connection;

                $dbCredentials = [
                    $config->host,
                    $config->dbname,
                    $config->username,
                    $config->password,
                ];
                break;
        }

        return $dbCredentials;
        
    }

    public function SQLDump(string $folder):bool {
        $host = $this->_host;
        $database = $this->_database;
        $username = $this->_username;
        $password = $this->_password;

        $randomString = $this->RandomString();

        $fileName = $database . "_" . date("Y-m-d_H-i-s") . "_" . $randomString . ".sql.gz";

        $dumpfile = $folder . $fileName;
        $cmd = "mysqldump --user=$username  --password=$password  --host=$host  --routines --skip-triggers --lock-tables=false --default-character-set=utf8  $database --single-transaction=TRUE  | gzip > $dumpfile";
        exec($cmd);

        
        $result = file_exists($dumpfile);
        return $result;
    }


    private function RandomString(int $length = 64):string
    { // 64 = 32 Chars
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length-($length%2))/2));
    }


}