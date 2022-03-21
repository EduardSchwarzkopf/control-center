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

    public static function CheckSendingMail(string $emailTo): bool
    {
        $timestamp = "mail.timestamp";

        if (file_exists($timestamp)) {
            $to = $emailTo;
            $subject = "Control-Center PHP Mailtest";
            $txt = date("Y-m-d H:i");
            $headers = "From: monitoring@" . self::GetDomain();

            $modificationDate = FileUtils::GetModificationDate($timestamp);
            $modificationCheck = FileUtils::GetAgeHours($modificationDate) > 1;

            if ($modificationCheck == false) {
                $out = mail($to, $subject, $txt, $headers);
            } else {
                return true;
            }

            if ($out == false) {
                return false;
            }

            touch($timestamp);
            return true;
        }

        $time = time() - 3600;
        touch($timestamp, $time);
        self::CheckSendingMail($emailTo);
        return true;
    }

    public static function GetDiskUsage(): int
    {
        return self::GetUsage();
    }

    public static function GetInodesUsage(): int
    {
        return self::GetUsage(2);
    }

    private static function GetUsage(int $type = 1): int
    {

        if ($type == 1) {
            $command = 'df -h ./'; // diskusage
        } elseif ($type == 2) {
            $command = 'df -i /';  // inodes
        }

        $output = exec($command);

        preg_match('/ (\d+)%/', $output, $matches);

        $usage = 0.0;

        if (is_array($matches) && count($matches) > 0 && is_numeric($matches[1])) {
            $usage = floatval($matches[1]);
        }

        return $usage;
    }
}
