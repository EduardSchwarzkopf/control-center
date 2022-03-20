<?php

/* Init */
require('./GetDatabaseCredentials.php');
require('./CleanupFiles.php');
require('./AddToLogFile.php');

$configs = include('wrkbeat-config.php');

define("ENVIRONMENT", $configs["environment"]);
define("TIMETOLERANCE", $configs["timeTolerance"]);
define("WARNINGLEVEL", $configs["warningLevel"]);
define("SHOWOUTPUT", $configs["showDetailedOutput"]);
define("MAILRECEIVER", $configs["testmailReceiver"]);
define("DOMAIN", require('./BaseDomain.php'));
$RESPONSECODE = $configs["responseCode"];
$checkDiskUsage = $configs["checkDiskUsage"];
$checkInodesUsage = $configs["checkInodes"];

define("VERSION", "10.1");

$checkResults = [];

if (ENVIRONMENT !== 0) {
    $backupfolder = $configs["backupFolder"] ? $configs["backupFolder"] : "./backups";
    $sqlFiles = "$backupfolder/*.sql.gz";
    $checkResults[] = BackupCheck($sqlFiles);


    $modDate = GetRecentFileModificationDate($sqlFiles);
    if (GetAgeHours($modDate) >= 24) {
        $backupPath = dirname(__FILE__) . "/backup.php";
        $phpPath = $configs["phpPath"] ? $configs["phpPath"] : "php";
        exec("$phpPath $backupPath '" . DOMAIN . "' >/dev/null 2>&1 &");
    }


    CreateHeadline("SQL Connection");
    $sqlConnection = SQLConnectionCheck();
    $checkResults[] = $sqlConnection;

    if (defined('ENVIRONMENT')) {

        if (ENVIRONMENT == 2) {
            CreateHeadline("IndexLogFile");
            $indexLogs = indexLogsCheck();

            CreateHeadline("Files Backup");
            $backupfolder = dirname(dirname(__FILE__)) . "/var/backups";
            $checkResults[] = BackupCheck(dirname(dirname(__FILE__)) . "/var/backups/*_filesystem_code.tgz");
        } elseif (ENVIRONMENT == 1 && $sqlConnection) {
            CreateHeadline("Wordpress Backup");
            if (defined("WPBACKUPHASH")) {
                $wpBackupFilePath = dirname(dirname(__FILE__)) . "/wp-content/uploads/backwpup-" . WPBACKUPHASH . "-backups/*.tar.gz";
                $checkResults[] = BackupCheck($wpBackupFilePath);
            } else {
                AddToOutput("BackWPup Plugin not found", SHOWOUTPUT);
            }
        }
    }
}

if ($checkDiskUsage) {
    $checkResults[] = diskUsage();
}

if ($checkInodesUsage) {
    $checkResults[] = InodesCheck();
}

$checkResults[] = CheckSendingMail();


}

//Add Response Code to Oupput:
CreateHeadline("Response Code");
AddToOutput("Response: " . http_response_code(), true);

//Versionsnummer
AddToOutput("Version: " . VERSION, true);


//Echo Everything
echo $statusOutput;

AddtoLogFile($statusOutput, "monitor");





// function diskUsage()
// {

//     // Disk Usage mit folgendem Befehl ermitteln:
//     $diskUsage = GetUsage();
//     $result = true;
//     $status = "OK";

//     if ($diskUsage > WARNINGLEVEL) {
//         AddToOutput("Disk Usage: " . $diskUsage . "%", true);
//         $status = "WARN!";
//         $result = false;
//     }

//     AddToOutput("Disk Status: $status", true);
//     AddToOutput("Disk Usage: $diskUsage%", true);
//     AddToOutput("Warning at: " . WARNINGLEVEL . "%", true);

//     return $result;
// }
