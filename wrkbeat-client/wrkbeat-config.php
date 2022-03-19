<?php

return array (
    //Globals
    "responseCode" => 404,
    "phpPath" => null,                                          // Abweichender PHP Pfad
    "environment" => 1,                                         // 0= None, 1= Wordpress, 2= Magento2, 3= Magento1
    "timeTolerance" => 28,                                      // File age in hours
    "warningLevel" => 80,                                       // Warning Level
    "showDetailedOutput"=> false,                               // False in Productive
    "testmailReceiver" => "exception@wrkbeat.com",              // Receiver of Testmails

    // Checks
    "checkDiskUsage" => true,                                   // DiskSpace Check
    "checkInodes" => true,                                      // Inodes Check

    // Backup
    "backupCreate" => true,                                     // Backup Process
    "backupFolder" => "./backups/",                             // Backup Folder
    "backupNotification" => false,                              // Notification for Backups?
    "backupNotificationReceiver" => "exception@wrkbeat.com",      // Receiver of Backup-Notifications
    "backupKeepAmount" => 30,                                   // Amount of Backups to Keep - Default 30
);

