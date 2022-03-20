<?php

abstract class CheckPlatormBase implements CheckPlatformInterface
{
    static public function Run(Platform $platform): bool
    {

        $dbCeck = $platform->CheckDatabaseConnection();

        return $dbCeck;
    }
}
