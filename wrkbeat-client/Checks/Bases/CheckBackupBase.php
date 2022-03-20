<?php

abstract class CheckBackupBase implements CheckFilesInterface
{
    static public function Run(int $threshold, string $pattern = null): bool
    {

        if ($pattern == null) {
            return false;
        }

        $backupfolder = dirname(dirname(__FILE__)) . '/backups';

        if (is_dir($backupfolder) == false) {
            mkdir($backupfolder, 0755, true);
        }

        $backupPattern = $backupfolder . '/' . $pattern;
        $modificationDateFile = FileUtils::GetRecentFileModificationDate($backupPattern);

        $hours = FileUtils::GetAgeHours($modificationDateFile);
        $result =  $hours <= $threshold;

        return $result;
    }
}
