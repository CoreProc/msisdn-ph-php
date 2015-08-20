MsisdnPH - PHP
=====================

Easily validate and manipulate Philippine mobile numbers.

## Basic Setup

Run the following command at the root of your project (assuming you have Composer and a composer.json file already)

```
composer require coreproc/msisdn-ph 1.0.*
```

## Usage

### Validate the mobile number

```
$mobileNumber = '09171231234';

if (Msisdn::validate($mobileNumber)) {
    echo 'Valid mobile number';
} else {
    echo 'Invalid mobile number';
}
```

The `validate` method cleans the given mobile number and validates it so even if the mobile number is malformed or has other characters within it, it will still return true as long as the number is really valid:

```
$validMobileNumber = '+639171231234';
$validMobileNumber = '+63-917-123-1234';
$validMobileNumber = '0917-123-1234';
$validMobileNumber = '0917.123.1234';
$validMobileNumber = '63 917 123 12 34 ';
```

### Instantiate an MSISDN object

You can instantiate an MSISDN object and get a standardized format of your mobile number and even get the telco attached to the mobile number using this object.

```
$mobileNumber = '09171231234';

$msisdn = new Msisdn($mobileNumber);
```

The MSISDN object will throw an `Exception` if you give it an invalid mobile number, so it's best to either catch the exception OR validate it before creating an MSISDN object.

```
$invalidMobileNumber = '0917-123-123';

try {
    $msisdn = new Msisdn($invalidMobileNumber);
} catch (Exception $e) {
    echo 'The number is invalid';
    return;
}
```

OR

```
$invalidMobileNumber = '0917-123-123';

if (Msisdn::validate($invalidMobileNumber)) {
    $msisdn = new Msisdn($invalidMobileNumber);
} else {
    echo 'Invalid mobile number';
    return;
}
```


### Return a standardized format of your mobile number

When you've instantiated an `Msisdn` object, you can return the mobile number in any format you want.

```
$mobileNumber = '09171231234';

$msisdn = new Msisdn($mobileNumber);

echo $msisdn->get(); // will return 09171231234

echo $msisdn->get(true); // will return +639171231234

echo $msisdn->get(false, '-'); // will return 0917-123-1234

echo $msisdn->get(true, '-'); // will return +63-917-123-1234

echo $msisdn->get(true, '.'); // will return +63.917.123.1234

```

### Get the telco operator of a mobile number

You can also get the telco operator of the given mobile number - this is based on the included prefixes we have gathered from the telcos.

We cannot guarantee that this list is updated so use with a grain of salt.

If you want to add to the prefixes, you can find the list inside the `src/prefixes` directory.

```
$mobileNumber = '09171231234';

$msisdn = new Msisdn($mobileNumber);

echo $msisdn->getOperator(); // will return Globe

```

### Get the prefix of a mobile number

You might just want to get the prefix of a mobile number.

```
$mobileNumber = '09171231234';

$msisdn = new Msisdn($mobileNumber);

echo $msisdn->getPrefix(); // will return 917

```