<?php


abstract class BasePlatform
{

    private string $_host = '';
    private string $_database = '';
    private string $_username = '';
    private string $_password = '';

    // DatabaseConnection
    // Database Backup
    // Database Backup Check
    // E-Mail Check

    public function SQLDump(string $folder): bool
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
}
