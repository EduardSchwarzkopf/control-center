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

/* Calls */
if (SHOWOUTPUT) {
    CreateHeadline("Environment");
}

$checkResults = [];

if (ENVIRONMENT !== 0) {
    CreateHeadline("SQL Backup");
    $backupfolder = $configs["backupFolder"] ? $configs["backupFolder"] : "./backups";
    $sqlFiles = "$backupfolder/*.sql.gz";
    $checkResults[] = BackupCheck($sqlFiles);


    $modDate = GetRecentFileModificationDate($sqlFiles);
    if (GetAgeHours($modDate) >= 24) {
        $backupPath = dirname( __FILE__ ) . "/backup.php";
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
            $backupfolder = dirname( dirname(__FILE__) ) . "/var/backups";
            $checkResults[] = BackupCheck( dirname( dirname(__FILE__) ) . "/var/backups/*_filesystem_code.tgz");

        } elseif (ENVIRONMENT == 1 && $sqlConnection) {
            CreateHeadline("Wordpress Backup");
            if (defined("WPBACKUPHASH")) {
                $wpBackupFilePath = dirname( dirname(__FILE__) ) . "/wp-content/uploads/backwpup-" . WPBACKUPHASH . "-backups/*.tar.gz";
                $checkResults[] = BackupCheck($wpBackupFilePath);
            } else {
                AddToOutput("BackWPup Plugin not found", SHOWOUTPUT);
            }
        }
    }
}

if ($checkDiskUsage) {
    CreateHeadline("Disk Usage");
    $checkResults[] = diskUsage();
}

if ($checkInodesUsage) {
    CreateHeadline("Inodes Usage");
    $checkResults[] = InodesCheck();
}

CreateHeadline("E-Mail Check");
$checkResults[] = CheckSendingMail();


//Set Response Code
if (in_array(false, $checkResults) && !$backupProcess) {
    http_response_code($RESPONSECODE);
} else {
    http_response_code(200);
}

//Add Response Code to Oupput:
CreateHeadline("Response Code");
AddToOutput("Response: " . http_response_code(), true);

//Versionsnummer
AddToOutput("Version: " . VERSION, true);


//Echo Everything
echo $statusOutput;

AddtoLogFile($statusOutput, "monitor");

/* FUNCTIONS */
function AddToOutput($Output, $showOutput, $breakType = "br")
{
    if ($showOutput) {
        global $statusOutput;
        if ($breakType == "br") {
            $statusOutput .= $Output . "<$breakType>";
        } else {
            $statusOutput .= "<$breakType>" . $Output . "</$breakType>";
        }
    }
}

function InodesCheck() {
    $usage = GetUsage("inodes");
    $result = true;
    $status = "OK";
    
    if ($usage > WARNINGLEVEL) {
        AddToOutput("Inodes Usage: " . $usage . "%", true);
        $status = "WARN!";
        $result = false;
    }

    AddToOutput("Inodes Status: $status", true);
    AddToOutput("Warning at: " . WARNINGLEVEL . "%", true);

    return $result;
}

function GetUsage($exec="disk") {
    // Inodes String aus Shell erhalten

    if ($exec=="disk") {
       $output = exec('df -h ./'); 
    } elseif ($exec == "inodes") {
        $output = exec('df -i /');
    }
    // Position von prozentuallem Verbrauch erhalten
    $pos = strpos($output, "%");
    // 3 Schritte zurück gehen, um Gesamtverbrauch zu erhalten
    $usage = substr($output, $pos -2, 2);

    return $usage;
}

function CheckSendingMail()
{
    $timestamp = "mail.timestamp";

    if(file_exists($timestamp)) {
        $to = MAILRECEIVER;
        $subject = "PHP MAILCHECK";
        $txt = date("Y-m-d H:i");
        $headers = "From: monitoring@" . DOMAIN;
        
        $modificationDate = GetModificationDate($timestamp);
        $modificationCheck = CheckModifactionDate($modificationDate, 1);

        if ($modificationCheck == false) {
            $out = mail($to,$subject,$txt,$headers);

        } else {
            AddToOutput("Email Status: SKIPPED", true);
            return true;
        }

        if($out == false){
            AddToOutput("Email Status: WARN", true);
            return false;
        }
        AddToOutput("Email Status: OK", true);

        touch($timestamp);
        return true;

    } else {

        AddToOutput("Init email check", true);
        $time = time() - 3600;
        touch($timestamp, $time);
        return true;
    }

}

function SQLConnectionCheck()
{

    if (defined('ENVIRONMENT')) {

         try {
            [$host, $database, $username, $password] = GetDatabaseCredentials(ENVIRONMENT);

            $conn = new mysqli($host, $username, $password, $database);

            if ($conn->error) {
                AddToOutput("SQL Error: ". $conn->error, true);
            }

            if ($conn->connect_error) {
                $errorMsg = "Connection failed: " . $conn->connect_error;
                AddToOutput($errorMsg, true);
                return false;
            } else {
                AddToOutput("Connection Status: OK", true);
            }

            if (ENVIRONMENT == 1) {
                require('../wp-config.php');
                $wpOptions = $table_prefix . "options";
                AddToOutput($result, SHOWOUTPUT);
                $sql = 'SELECT option_value FROM ' . $wpOptions . ' WHERE option_name = "backwpup_cfg_hash";';
                $result = $conn->query($sql);
                if ($result) {
                    $resultRows = $result->fetch_row();
                    define("WPBACKUPHASH", $resultRows[0]);
                }
            }
            $conn->close();
            return true;
 
         } catch (Exception $e) {
             AddToOutput("Exception catched: " . $e->getMessage(), true);
         }
   }

    AddToOutput("No Environment defined", true);
    return false;
}

function GetRecentFileModificationDate($pathToFiles) {
    $files = glob($pathToFiles);
    $backupFile = GetRecentFile($files);
    return GetModificationDate($backupFile);
}

//Last Backup Check
function BackupCheck($pathToFiles)
{
    
    $modificationDateFile = GetRecentFileModificationDate($pathToFiles);
    $modificationCheck = CheckModifactionDate($modificationDateFile);

    if (!$modificationCheck) {
        AddToOutput("Backup status: WARN", true);
        return false;
    } else {
        AddToOutput("Backup status: OK", true);
        return true;
    };
}

function GetRecentFile($files)
{
    $files = array_combine($files, array_map('filemtime', $files));
    arsort($files);
    return key($files);
}


// Magento IndexLogs
function IndexLogsCheck()
{
    $files = dirname( dirname(__FILE__) ) . "/indexlogs/log.txt";
    $modificationDateFile = GetRecentFileModificationDate($files);
    $modificationCheck = CheckModifactionDate($modificationDateFile);

    if (!$modificationCheck) {
        AddToOutput("IndexLog status: WARN", true);
        return false;
    } else {
        AddToOutput("IndexLog status: OK", true);
        return true;
    };
}

function GetAgeHours($date) {
    $dateNow = date("Y-m-d H:i");
    $seconds = strtotime($dateNow) - strtotime($date);
    return $seconds / 60 /  60;
}


function CheckModifactionDate($mdate, $hoursTolerance = TIMETOLERANCE)
{
    $hours = GetAgeHours($mdate);

    if ($hours <= $hoursTolerance) {
        return true;
    } else {
            AddToOutput("File age: " . strval(round($hours)) . " hours", SHOWOUTPUT);
        return false;
    }
}

function GetModificationDate($file)
{
    return date("Y-m-d H:i", filemtime($file));
}


function diskUsage()
{

    // Disk Usage mit folgendem Befehl ermitteln:
    $diskUsage = GetUsage();
    $result = true;
    $status = "OK";

    if ($diskUsage > WARNINGLEVEL) {
        AddToOutput("Disk Usage: " . $diskUsage . "%", true);
        $status = "WARN!";
        $result = false;
    }

    AddToOutput("Disk Status: $status", true);
    AddToOutput("Disk Usage: $diskUsage%", true);
    AddToOutput("Warning at: " . WARNINGLEVEL . "%", true);

    return $result;
}

function CreateHeadline($title)
{
    AddToOutput("<br>******** " . $title . " ********", true, "p");
}