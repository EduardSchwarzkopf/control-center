<?php

class Monitor
{

    public function StartChecks()
    {

        $postList = $_POST;

        $checkList = [
            'diskusage' => 80,
            'inodes' => 80,
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
                    # code...
                    break;

                case 'magento1':
                    # code...
                    break;

                case 'magento2':
                    # code...
                    break;
            }

            $a = 1;

            // Save to Monitor Output
        }
    }

    public function JSONResponse(): string
    {
        return '';
    }
}
