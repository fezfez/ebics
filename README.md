# ebics

PHP library to communicate with bank through EBICS protocol.

## License
fezfez/ebics is licensed under the MIT License, see the LICENSE file for details

## Note
This library is a re work of andrew-svirin/ebics-client-php to allow multiple protocol version + unit test and E2e test

Version supported :

- 2.4
- 2.5
- 3.0

Command supported :

- INI
- HIA
- HPB
- FDL

This library work only with X509 certified communication

## Installation
```bash
composer require fezfez/ebics
```


## Initialize client

You will need to have this informations from your Bank : 

- HostID
- HostURL
- PartnerID
- UserID
- protocol version

```php
<?php

$bank    = new \Fezfez\Ebics\Bank($HOST_ID, $HOST_URL, \Fezfez\Ebics\Version::v24());
$user    = new \Fezfez\Ebics\User($PARTNER_ID, $USER_ID);
$keyring = new \Fezfez\Ebics\KeyRing('myPassword');
$x509Generator = new \Fezfez\Ebics\X509\SilarhiX509Generator();
```

**Note** : $HOST_ID, $HOST_URL, $PARTNER_ID, $USER_ID and version are decided between you and your bank.

## How to use

Before making what you want to achieve (ex: FDL call) you have to generate keys and communicate it to  with the server (INI, HIA and HPB command).

## INI command

INI command will generate a certificat of type A and send it to ebics server.
After making this request, you have to save the keyring with the new generate certificat because it will be used in call after.

```php
<?php

$keyring = (new \Fezfez\Ebics\Command\INICommand())->__invoke($bank, $user, $keyring, $x509Generator);
// save kering

```

## HIA command

HIA command will generate a certificat of type e and x and then send it to ebics server.
After making this request, you have to save the keyring with the new generate certificat because it will be used in call after.

```php
<?php

$keyring = (new \Fezfez\Ebics\Command\HIACommand())->__invoke($bank, $user, $keyring, $x509Generator);
// save kering 

```

## HPB command

HPB command will retrieve certificat of type e and x from the ebics server.
After making this request, you have to save the keyring with the new retrieved certificat because it will be used in call after.

```php
<?php

$keyring = (new \Fezfez\Ebics\Command\HPBCommand())->__invoke($bank, $user, $keyring);
// save kering

```

Once INI, HIA and HPB have been run your good to use ebics protocol.

## Saving keyring

```php
<?php

$keyring = new \Fezfez\Ebics\KeyRing('myPassword');
$keyringAsArray = $keyring->jsonSerialize(); 
$keyringAsJson  = json_encode($keyring); 

// put $keyringAsArray or $keyringAsJson in db, file etc...

```

## Wakeup keyring

```php
<?php

$keyring = \Fezfez\Ebics\KeyRing::fromArray($keyringAsArray, 'myPassword');

```

## good to know

This website provide an ebics server testing environnement : https://software.elcimai.com/efs/accueil-qualif.jsp 