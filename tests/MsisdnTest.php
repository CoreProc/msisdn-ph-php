<?php

class MsisdnTest extends PHPUnit_Framework_TestCase
{

    protected $validMobileNumbers = [
        '09171231234',
        '0917-123-1234',
        '63917-123-1234',
        '+63-917-123-1234',
        '+63.917.123.1234 ',
        '+639171231234',
        ' +639171231234  ',
    ];

    public function testValidNumbers()
    {
        require __DIR__ . '/../vendor/autoload.php';

        foreach ($this->validMobileNumbers as $mobileNumber) {
            $this->assertTrue(Coreproc\MsisdnPh\Msisdn::validate($mobileNumber), 'Mobile number "' . $mobileNumber . '" should be valid.');
        }
    }

    public function isInIsolation()
    {
        //
    }

}