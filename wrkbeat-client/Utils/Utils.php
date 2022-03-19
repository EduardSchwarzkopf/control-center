<?php


class Utils
{

    static public function GetDomain(): string
    {
        $host = $_SERVER['HTTP_HOST'];
        preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
        return $matches[0];
    }

    public static function RandomString(int $length = 64): string
    { // 64 = 32 Chars
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }

    public static function GetDiskUsage(): string
    {
        return self::GetUsage();
    }

    public static function GetInodesUsage(): string
    {
        return self::GetUsage(2);
    }

    private static function GetUsage(int $type = 1): string
    {

        if ($type == 1) {
            $command = 'df -h ./'; // diskusage
        } elseif ($type == 2) {
            $command = 'df -i /';  // inodes
        }

        $output = exec($command);
        $percentPos = strpos($output, "%");

        $usage = substr($output, $percentPos - 2, 2);

        return $usage;
    }
}
