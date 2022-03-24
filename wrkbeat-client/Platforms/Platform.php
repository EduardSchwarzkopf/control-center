<?php

abstract class Platform implements ResponseInterface
{

    protected string $_host = '';
    protected string $_database = '';
    protected string $_username = '';
    protected string $_password = '';
    protected string $_platformRoot = '';
    protected $_platformConfig = '';

    protected string $_db_server_info = '';
    protected string $_db_dump_path = '';
    protected int $_db_file_size = 0;
    protected string $_db_human_file_size = '';

    protected string $_backup_dump_path = '';
    protected int $_backup_file_size = 0;
    protected string $_backup_human_file_size = '';

    protected array $hideFields = [
        'hideFields', '_host', '_database', '_username', '_password', '_platformRoot', '_platformConfig'
    ];


    function __construct($configFilePath)
    {
        $this->_platformRoot = dirname(__DIR__, 2);

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

    public function GetHiddenFields()
    {
        return $this->GetHiddenFields();
    }

    public function GetBackupDumpPath(): string
    {
        return $this->_backup_dump_path;
    }

    public function GetBackupFileSize(): int
    {
        return $this->_backup_file_size;
    }

    public function GetBackupHumanFileSize(): string
    {
        return $this->_backup_human_file_size;
    }

    public function GetSQLDumpPath(): string
    {
        return $this->_db_dump_path;
    }

    public function GetDatabaseFileSize(): int
    {
        return $this->_db_file_size;
    }

    public function GetDatabaseHumanFileSize(): string
    {
        return $this->_db_human_file_size;
    }

    public function GetDatabaseInfo(): string
    {
        return $this->_db_server_info;
    }

    public function CreateSQLDump(): bool
    {
        $sqlCheck = $this->CheckDatabaseConnection();

        if ($sqlCheck == false) {
            return false;
        }

        $host = $this->_host;
        $database = $this->_database;
        $username = $this->_username;
        $password = $this->_password;

        $randomString = Utils::RandomString();

        $fileName = date('Y-m-d_H-i-s') . '_' . $database . '_' . $randomString . '.sql.gz';

        $dumpFilePath = dirname(__DIR__) . '/backups/' . $fileName;
        $cmd = "mysqldump --user=$username  --password=$password  --host=$host  --routines --skip-triggers --lock-tables=false --default-character-set=utf8  $database --single-transaction=TRUE | gzip > $dumpFilePath";
        exec($cmd);

        $result = file_exists($dumpFilePath);

        if ($result) {

            $this->_db_dump_path = str_replace($this->_platformRoot, '', $dumpFilePath);
            $this->_db_file_size = filesize($dumpFilePath);
            $this->_db_human_file_size = FileUtils::HumanFileSize($this->_db_file_size);
        }

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

            $this->_db_server_info = $conn->server_info;

            $conn->close();
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function CreateFilesBackup(?array $exludePatternList): bool
    {
        $result = true;


        $exlude = '';
        $platformPath = $this->_platformRoot;
        foreach ($exludePatternList as $excludePattern) {
            $exlude .= "--exclude=$platformPath/$excludePattern ";
        }

        $now = date('Y-m-d_H-i-s');
        $randomString = Utils::RandomString();

        $file = $now . '_files_backup_' . $randomString . '.tgz';

        $clientPath = dirname(__DIR__);
        $backupPath = $clientPath . '/backups/' . $file;
        $cmd = "tar zcv --exclude=$clientPath $exlude -f $backupPath $platformPath ";
        $exec = exec($cmd);

        $result = file_exists($backupPath);
        if ($result) {

            $this->_backup_dump_path = str_replace($this->_platformRoot, '', $backupPath);
            $this->_backup_file_size = filesize($backupPath);
            $this->_backup_human_file_size = FileUtils::HumanFileSize($this->_backup_file_size);
        }


        return $result;
    }
}
