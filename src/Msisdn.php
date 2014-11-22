<?php

/**
 * MsisdnPH - An MSISDN identification and cleaner library for Philippine telco
 * subscribers
 *
 * @author Chris Bautista <chrisbjr@gmail.com>
 * @copyright 2014 Chris Bautista
 * @link https://github.com/chrisbjr/msisdn-ph-library-php
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 * @package chrisbjr\msisdn-ph
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Coreproc\MsisdnPh;

class Msisdn
{

    private $msisdn = null;
    private $prefix = null;
    private $operator = null;
    private $smart_prefixes = null;
    private $globe_prefixes = null;
    private $sun_prefixes = null;

    /**
     * Usage example:
     * <br />
     * require_once 'Msisdn.php';<br />
     * $msisdn = new Msisdn();<br />
     * $msisdn->set('09281234567');<br />
     * echo $msisdn->getOperator();<br />
     * echo $msisdn->get(true);<br />
     *
     * <br />
     *
     * For CodeIgniter, place this in the 'application/libraries' folder then
     * use the following code:<br />
     *
     * $this->load->library('msisdn');<br />
     * $this->msisdn->set('09281234567);<br />
     * echo $this->msisdn->getOperator();<br />
     * echo $this->msisdn->get(true);<br />
     */
    function __construct($msisdn = null)
    {
        // Get the prefixes
        $this->smart_prefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/smart.json'));
        $this->globe_prefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/globe.json'));
        $this->sun_prefixes = json_decode(file_get_contents(__DIR__ . '/prefixes/sun.json'));

        if ($msisdn != null) {
            $this->set($msisdn);
        }
    }

    /**
     * Set the MSISDN
     *
     * @param string $msisdn
     * @return boolean Returns whether the given MSISDN is valid or not
     */
    function set($msisdn)
    {
        // Clean the number
        $msisdn = $this->clean($msisdn);

        // Determine if the number is valid
        if ($this->isValid($msisdn) == false) {
            return false;
        }

        $this->msisdn = $msisdn;

        // We reset the variables so we can use this instance over and over with
        // a bit of speed
        $this->prefix = null;
        $this->operator = null;

        return true;
    }

    /**
     * Returns the MSISDN number. Set the first parameter to true if you want to
     * return the MSISDN number with the country code.
     *
     * @param boolean $country_code Set to true if you want to return the MSISDN
     * with the country code. Defaults to false.
     * @return string The MSISDN number.
     */
    function get($country_code = false)
    {
        if ($this->msisdn == null) {
            return null;
        }

        if ($country_code == false) {
            return '0' . $this->msisdn;
        } else {
            return '63' . $this->msisdn;
        }
    }

    /**
     * Determines the operator of this number
     *
     * @return string The operator of this number
     */
    function getOperator()
    {
        if ($this->msisdn == null) {
            return null;
        }

        if ($this->operator == null) {
            if (in_array($this->getPrefix(), $this->smart_prefixes)) {
                $this->operator = 'SMART';
            } else if (in_array($this->getPrefix(), $this->globe_prefixes)) {
                $this->operator = 'GLOBE';
            } else if (in_array($this->getPrefix(), $this->sun_prefixes)) {
                $this->operator = 'SUN';
            }
        }

        return $this->operator;
    }

    /**
     * Returns the prefix of the MSISDN number.
     *
     * @return string The prefix of the MSISDN number
     */
    function getPrefix()
    {
        if ($this->prefix == null) {
            if ($this->msisdn != null) {
                $this->prefix = substr($this->msisdn, 0, 3);
            }
        }

        return $this->prefix;
    }

    /**
     * Cleans the MSISDN number
     *
     * @param string $msisdn
     * @return string The clean MSISDN
     */
    function clean($msisdn)
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
     * Determines if the number is valid or not. This function assumes that the
     * number given is already clean.
     *
     * @return boolean
     */
    function isValid($msisdn = null)
    {
        if (empty($msisdn)) {
            $msisdn = $this->msisdn;
        } else {
            $msisdn = $this->clean($msisdn);
        }

        if (empty($msisdn)) {
            return false;
        }

        if (strlen($msisdn) != 10 || is_numeric($msisdn) == false) {
            return false;
        }

        return true;
    }

}
