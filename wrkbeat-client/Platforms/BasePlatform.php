<?php


abstract class BasePlatform
{

    private string $_host = '';
    private string $_database = '';
    private string $_username = '';
    private string $_password = '';

    // Database Backup
    // Database Backup Check

    protected function GetPlatformRootPath(): string
    {
        return dirname(__FILE__, 3);
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

            $conn = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);

            if ($conn->connect_error || $conn->error) {
                $result =  false;
            }

            $conn->close();
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
}
