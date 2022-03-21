<?php

class Monitor
{
    private array $_responseList = [];
    private array $_errorList = [];

    public function StartChecks()
    {

        $checkList = $_POST;

        $checkList = [
            'diskusage' => 80,
            'inodes' => 80,
            'wordpress' => true,
        ];

        foreach ($checkList as $checkItem => $value) {


            switch ($checkItem) {
                case 'diskusage':
                    $result = CheckDiskUsage::Run($value);
                    $this->_diskusage = $result;
                    break;

                case 'inodes':
                    $result = CheckInodes::Run($value);
                    $this->_inodes = $result;
                    break;

                case 'email':
                    $result = CheckInodes::Run($value);
                    $this->_email = $result;
                    break;

                case 'wordpress':
                case 'magento1':
                case 'magento2':

                    if ($value == false) {
                        break;
                    }

                    $platformName = ucfirst($checkItem) . 'Platform';

                    try {
                        $platform = new $platformName;
                    } catch (Exception $e) {
                        $message = $e->getMessage();
                        $line = $e->getLine();
                        $file = $e->getFile();

                        $this->_errorList[$platformName] = "PLATFORM ERROR: $message in $file on line $line";
                        break;
                    }

                    $platformCheck = 'Check' . $platformName;
                    $result = $platformCheck::Run($platform);

                    $serverInfo = $platform->db_server_info;
                    $this->_responseList['db_server_info'] = $serverInfo;

                    $a = 1;
                    break;
            }

            $a = 1;
        }
    }

    public function JSONResponse(): string
    {
        return '';
    }
}
