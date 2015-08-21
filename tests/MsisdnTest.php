<?php

class MsisdnTest extends PHPUnit_Framework_TestCase
{

    public function __construct()
    {
        require __DIR__ . '/../vendor/autoload.php';
        parent::__construct();
    }

    protected $validMobileNumbers = array(
        '09171231234',
        '0917-123-1234',
        '63917-123-1234',
        '+63-917-123-1234',
        '+63.917.123.1234 ',
        '+639171231234',
        ' +639171231234  ',
    );

    protected $invalidMobileNumbers = array(
        '0918123123',
        '+6391812312345',
        ''
    );

    public function testValidNumbers()
    {
        foreach ($this->validMobileNumbers as $mobileNumber) {
            $this->assertTrue(Coreproc\MsisdnPh\Msisdn::validate($mobileNumber), 'Mobile number "' . $mobileNumber . '" should be valid.');
        }
    }

    public function testInvalidNumbers()
    {
        foreach ($this->invalidMobileNumbers as $mobileNumber) {
            $this->assertFalse(Coreproc\MsisdnPh\Msisdn::validate($mobileNumber), 'Mobile number "' . $mobileNumber . '" should be invalid.');
        }
    }

    public function testExceptionInConstructor()
    {
        foreach ($this->invalidMobileNumbers as $mobileNumber) {
            $exception = null;

            try {
                new \Coreproc\MsisdnPh\Msisdn($mobileNumber);
            } catch (Exception $e) {
                $exception = $e;
            }

            $this->assertInstanceOf('Exception', $exception, 'We expect an exception thrown by the constructor.');
        }
    }

    public function testFormattedNumbers()
    {
        $mobileNumber = '+63917123-1234';

        $msisdn = new \Coreproc\MsisdnPh\Msisdn($mobileNumber);

        $this->assertEquals('09171231234', $msisdn->get());

        $this->assertEquals('+639171231234', $msisdn->get(true));

        $this->assertEquals('0917-123-1234', $msisdn->get(false, '-'));

        $this->assertEquals('+63 917 123 1234', $msisdn->get(true, ' '));
    }

    public function testPrefix()
    {
        $mobileNumber = '09171231234';

        $msisdn = new \Coreproc\MsisdnPh\Msisdn($mobileNumber);

        $this->assertEquals('917', $msisdn->getPrefix());
    }

    public function testOperator()
    {
        $globeMsisdn = new \Coreproc\MsisdnPh\Msisdn('09171231234');

        $smartMsisdn = new \Coreproc\MsisdnPh\Msisdn('09191231234');

        $sunMsisdn = new \Coreproc\MsisdnPh\Msisdn('09321231234');

        $unknownMsisdn = new \Coreproc\MsisdnPh\Msisdn('09511231234');

        $this->assertEquals('GLOBE', $globeMsisdn->getOperator());

        $this->assertEquals('SMART', $smartMsisdn->getOperator());

        $this->assertEquals('SUN', $sunMsisdn->getOperator());

        $this->assertEquals('UNKNOWN', $unknownMsisdn->getOperator());
    }

}