<?php


abstract class Platform
{

    protected string $_host = '';
    protected string $_database = '';
    protected string $_username = '';
    protected string $_password = '';
    protected string $_platformRoot = '';
    protected $_platformConfig = '';

    public string $db_server_info = '';


    function __construct($configFilePath)
    {
        $this->_platformRoot = dirname(dirname(dirname(__FILE__)));

        $configPath = $this->_platformRoot . $configFilePath;
        $this->_platformConfig = $this->LoadPlatformConfigFile($configPath);
    }

    private function LoadPlatformConfigFile(string $configFilePath)
    {
        $platformConfig = include_once($configFilePath);

        if ($platformConfig == false) {
            $platformConfig = simplexml_load_file($this->_platformRoot . '/app/etc/local.xml');

            if ($platformConfig == false) {
                throw new Exception($configFilePath . ' not found');
            }
        }

        return $platformConfig;
    }

    public function CreateSQLDump(string $folder): bool
    {
        $host = $this->_host;
        $database = $this->_database;
        $username = $this->_username;
        $password = $this->_password;

        $randomString = Utils::RandomString();

        $fileName = $database . "_" . date("Y-m-d_H-i-s") . "_" . $randomString . ".sql.gz";

        $dumpfile = $folder . $fileName;
        $cmd = "mysqldump --user=$username  --password=$password  --host=$host  --routines --skip-triggers --lock-tables=false --default-character-set=utf8  $database --single-transaction=TRUE  | gzip > $dumpfile";
        exec($cmd);


        $result = file_exists($dumpfile);
        return $result;
    }

    public function CheckDatabaseConnection(): bool
    {
        try {

            $result = true;
            $conn = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);

            if ($conn->connect_error || $conn->error) {
                $result = false;
            }

            $this->db_server_info = $conn->server_info;

            $conn->close();
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
}
