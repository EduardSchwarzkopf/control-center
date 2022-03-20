<?php


class FileUtils
{

    static public function CleanupFiles(array $files, int $files_to_keep = 30): array
    {
        $filesList = self::SortFilesByDate($files);

        $files = [];
        for ($i = 0; count($filesList) > $files_to_keep; $i++) {
            array_push($files, $filesList[$i]);
            unlink($filesList[$i]);
            unset($filesList[$i]);
        }

        return $files;
    }

    public static function GetRecentFile(array $files)
    {
        $files = array_combine($files, array_map('filemtime', $files));
        arsort($files);
        $recentFile = key($files);
        return $recentFile;
    }

    public static function GetRecentFileModificationDate(string $filePattern)
    {
        $files = glob($filePattern);
        $backupFile = self::GetRecentFile($files);
        $date = self::GetModificationDate($backupFile);
        return $date;
    }

    public static function SortFilesByDate(array $files): array
    {
        usort($files, function ($x, $y) {
            return filemtime($y) < filemtime($x);
        });

        return $files;
    }

    public static function GetModificationDate(string $filePath): string
    {
        return date("Y-m-d H:i", filemtime($filePath));
    }


    public static function GetAgeHours(string $date): string
    {
        $dateNow = date("Y-m-d H:i");
        $seconds = strtotime($dateNow) - strtotime($date);
        return $seconds / 60 /  60;
    }
}
