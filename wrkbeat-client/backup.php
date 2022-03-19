<?php
$config = include('./wrkbeat-config.php');

if ($config["backupCreate"]) {
    require('./SQLDump.php');
    require('./AddToLogFile.php');
    require('./CleanupFiles.php');
    $start = date("Y-m-d H:i:s");
    $time_start = microtime(true);
    $logText = "Backup started: $start\n";

    $folder = "./backups/";
    if ($config["backupFolder"]) {
        $folder = $config["backupFolder"];
    }
    $logText .= "backup folder: $folder\n";

    $dumpFile = SQLDump($config["environment"], $folder);
    $logText .= "dump: $dumpFile \n";

    $result = file_exists($dumpFile);
    $resultText = $result ? "success" : "failed";
    $logText .= "result: $resultText \n";


    if ($result) {

        // Cleanup
        $logText .= "\n--- Cleanup started:\n";
        $sqlFiles = glob($folder . "*.sql.gz",GLOB_BRACE);
        $files_to_keep = $config["backupKeepAmount"] ? $config["backupKeepAmount"] : 7;

        $removedFiles = CleanupFiles($sqlFiles, $files_to_keep);
        foreach ($removedFiles as $file) {
            $logText .= " -- $file\n";
        }
        $logText .= "\n--- Cleanup finished: removed " . count($removedFiles) . " files\n\n";

    }

    // Email Notification
    $backupNotification = $config["backupNotification"];
    $logText .= "Notification Setting:" . var_export($backupNotification, true) . "\n";
    if ($backupNotification) {

        $domain = $argv[1];
        $to = $config["backupNotificationReceiver"];
        $headers = "From: backup@$domain";

        $subject = "Backup $resultText - " . $_SERVER['HTTP_HOST'];
        $txt = "\nZeitstempel: " . date("Y-m-d H:i");

        $mailResult = mail($to,$subject,$txt,$headers);

        $mailResultText = $mailResult ? "success" : "failed";
        $logText .= "Sending email: $mailResultText\n";

    }

    $end = date("Y-m-d H:i:s");
    $time_end = microtime(true);
    $time = round($time_end - $time_start, 2);
    $logText .= "Backup finished: $end (duration: $time seconds)\n";

    AddToLogFile($logText, "backup");

}