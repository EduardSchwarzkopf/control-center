<?php

class CheckWordpress extends CheckPlatormBase
{
    static public function Run(Platform $platform): bool
    {

        $parentCheck = parent::Run($platform);

        // TODO:
        // - Sending E-Mail
        // - Password Reset E-Mail

        $result = $parentCheck;

        return $result;
    }
}