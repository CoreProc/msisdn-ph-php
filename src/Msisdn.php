<?php

namespace Coreproc\MsisdnPh;

use Exception;

class Msisdn
{

    private $msisdn;

    private $smartPrefixes = null;

    private $globePrefixes = null;

    private $sunPrefixes = null;

    private $prefix = null;

    private $operator = null;

    protected $countryPrefix = '+63';

    public function __construct($msisdn)
    {
        if (Msisdn::validate($msisdn) === false) {
            throw new Exception(
                'The supplied MSISDN is not valid. ' .
                'You can use the `Msisdn::validate()` method ' .
                'to validate the MSISDN being passed.',
                400
            );
        }

        $this->msisdn = Msisdn::clean($msisdn);
    }

    /**
     * Returns a formatted mobile number
     *
     * @param bool|false $countryCode
     * @param string $separator
     * @return mixed|string
     */
    function get($countryCode = false, $separator = '')
    {
        if ($countryCode == false) {
            $formattedNumber = '0' . $this->msisdn;

            if ( ! empty($separator)) {
                $formattedNumber = substr_replace($formattedNumber, $separator, 4, 0);
                $formattedNumber = substr_replace($formattedNumber, $separator, 8, 0);
            }

            return $formattedNumber;
        } else {
            $formattedNumber = $this->countryPrefix . $this->msisdn;

            if ( ! empty($separator)) {
                $formattedNumber = substr_replace($formattedNumber, $separator, strlen($this->countryPrefix), 0);
                $formattedNumber = substr_replace($formattedNumber, $separator, 7, 0);
                $formattedNumber = substr_replace($formattedNumber, $separator, 11, 0);
            }

            return $formattedNumber;
        }
    }

    /**
     * Returns the prefix of the MSISDN number.
     *
     * @return string The prefix of the MSISDN number
     */
    public function getPrefix()
    {
        if ($this->prefix == null) {
            $this->prefix = substr($this->msisdn, 0, 3);
        }

        return $this->prefix;
    }

    /**
     * Determines the operator of this number
     *
     * @return string The operator of this number
     */
    public function getOperator()
    {
        $this->setPrefixes();

        if ( ! empty($this->operator)) {
            return $this->operator;
        }

        if (in_array($this->getPrefix(), $this->smartPrefixes)) {
            $this->operator = 'SMART';
        } else if (in_array($this->getPrefix(), $this->globePrefixes)) {
            $this->operator = 'GLOBE';
        } else if (in_array($this->getPrefix(), $this->sunPrefixes)) {
            $this->operator = 'SUN';
        } else {
            $this->operator = 'UNKNOWN';
        }

        return $this->operator;
    }

    private function setPrefixes()
    {
        if (empty($this->smartPrefixes)) {
            $this->smartPrefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/smart.json'));
        }

        if (empty($this->globePrefixes)) {
            $this->globePrefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/globe.json'));
        }

        if (empty($this->sunPrefixes)) {
            $this->sunPrefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/sun.json'));
        }
    }

    /**
     * Validate a given mobile number
     *
     * @param string $mobileNumber
     * @return bool
     */
    public static function validate($mobileNumber)
    {
        $mobileNumber = Msisdn::clean($mobileNumber);

        return ! empty($mobileNumber) &&
        strlen($mobileNumber) === 10 &&
        is_numeric($mobileNumber);
    }

    /**
     * Cleans the string
     *
     * @param string $msisdn
     * @return string The clean MSISDN
     */
    private static function clean($msisdn)
    {
        $msisdn = preg_replace("/[^0-9]/", "", $msisdn);

        // We remove the 0 or 63 from the number
        if (substr($msisdn, 0, 1) == '0') {
            $msisdn = substr($msisdn, 1, strlen($msisdn));
        } else if (substr($msisdn, 0, 2) == '63') {
            $msisdn = substr($msisdn, 2, strlen($msisdn));
        }

        return $msisdn;
    }

    /**
     * Sets the country prefix - this defaults to +63
     *
     * @param $countryPrefix
     */
    public function setCountryPrefix($countryPrefix)
    {
        $this->countryPrefix = $countryPrefix;
    }

}
