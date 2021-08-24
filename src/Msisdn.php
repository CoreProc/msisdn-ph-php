<?php

namespace Coreproc\MsisdnPh;

use Coreproc\MsisdnPh\Exceptions\InvalidMsisdnException;

class Msisdn
{
    private $msisdn;

    private $smartPrefixes = null;

    private $globePrefixes = null;

    private $sunPrefixes = null;

    private $ditoPrefixes = null;

    private $gomoPrefixes = null;

    private $prefix = null;

    private $operator = null;

    protected $countryPrefix = '+63';

    /**
     * Msisdn constructor.
     *
     * @param $msisdn
     * @throws InvalidMsisdnException
     */
    public function __construct($msisdn)
    {
        if (Msisdn::validate($msisdn) === false) {
            throw new InvalidMsisdnException(
                'The supplied MSISDN is not valid. ' .
                'You can use the `Msisdn::validate()` method ' .
                'to validate the MSISDN being passed.',
                400
            );
        }

        $this->msisdn = self::clean($msisdn);
    }

    /**
     * Returns a formatted mobile number
     *
     * @param bool|false $hasCountryCode
     * @param string $separator
     * @return mixed|string
     */
    function get($hasCountryCode = false, $separator = '')
    {
        if (! $hasCountryCode) {
            $formattedNumber = '0' . $this->msisdn;

            if (! empty($separator)) {
                $formattedNumber = substr_replace($formattedNumber, $separator, 4, 0);
                $formattedNumber = substr_replace($formattedNumber, $separator, 8, 0);
            }
        } else {
            $formattedNumber = $this->countryPrefix . $this->msisdn;

            if (! empty($separator)) {
                $formattedNumber = substr_replace($formattedNumber, $separator, strlen($this->countryPrefix), 0);
                $formattedNumber = substr_replace($formattedNumber, $separator, 7, 0);
                $formattedNumber = substr_replace($formattedNumber, $separator, 11, 0);
            }
        }

        return $formattedNumber;
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

        if (! empty($this->operator)) {
            return $this->operator;
        }

        foreach ($this->globePrefixes as $globePrefix) {
            $prefix = substr($this->msisdn, 0, strlen($globePrefix));

            if (in_array($prefix, $this->globePrefixes)) {
                $this->operator = 'GLOBE';

                return $this->operator;
            }
        }

        foreach ($this->smartPrefixes as $smartPrefix) {
            $prefix = substr($this->msisdn, 0, strlen($smartPrefix));

            if (in_array($prefix, $this->smartPrefixes)) {
                $this->operator = 'SMART';

                return $this->operator;
            }
        }

        foreach ($this->sunPrefixes as $sunPrefix) {
            $prefix = substr($this->msisdn, 0, strlen($sunPrefix));

            if (in_array($prefix, $this->sunPrefixes)) {
                $this->operator = 'SUN';

                return $this->operator;
            }
        }

        foreach ($this->ditoPrefixes as $ditoPrefix) {
            $prefix = substr($this->msisdn, 0, strlen($ditoPrefix));

            if (in_array($prefix, $this->ditoPrefixes)) {
                $this->operator = 'DITO';

                return $this->operator;
            }
        }

        foreach ($this->gomoPrefixes as $gomoPrefix) {
            $prefix = substr($this->msisdn, 0, strlen($gomoPrefix));

            if (in_array($prefix, $this->gomoPrefixes)) {
                $this->operator = 'GOMO';

                return $this->operator;
            }
        }

        $this->operator = 'UNKNOWN';

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

        if (empty($this->ditoPrefixes)) {
            $this->ditoPrefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/dito.json'));
        }

        if (empty($this->gomoPrefixes)) {
            $this->gomoPrefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/gomo.json'));
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
        if (substr($msisdn, 0, 1) === '0') {
            $msisdn = substr($msisdn, 1, strlen($msisdn));
        } else {
            if (substr($msisdn, 0, 2) === '63') {
                $msisdn = substr($msisdn, 2, strlen($msisdn));
            }
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
