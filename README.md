# AppVerk PHP Matcher

Library created for testing all kinds of JSON/XML/TXT/Scalar values against patterns.

API: 

```php
PHPMatcher::match($value = '{"foo": "bar"}', $pattern = '{"foo": "@string@"}') : bool;
PHPMatcher::backtrace() : Backtrace;
PHPMatcher::error() : ?string;
```

It was built to simplify API's functional testing.

## Installation

Require new dev dependency using composer:

```
composer require --dev "app-verk/php-matcher"
```

## Basic usage

### Direct PHPMatcher usage

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();
$match = $matcher->match("lorem ipsum dolor", "@string@");

if (!$match) {
    echo "Error: " . $matcher->error();
    echo "Backtrace: \n";
    echo (string) $matcher->backtrace();
}
```

### PHPUnit extending PHPMatcherTestCase

```php
<?php

use AppVerk\PHPMatcher\PHPUnit\PHPMatcherTestCase;

class MatcherTest extends PHPMatcherTestCase
{
    public function test_matcher_that_value_matches_pattern()
    {
        $this->assertMatchesPattern('{"name": "@string@"}', '{"name": "Norbert"}');
    }
}
```

### PHPUnit using PHPMatcherAssertions trait

```php
<?php

use AppVerk\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    use PHPMatcherAssertions;

    public function test_matcher_that_value_matches_pattern()
    {
        $this->assertMatchesPattern('{"name": "@string@"}', '{"name": "Norbert"}');
    }
}
```

### Available patterns

* ``@string@``
* ``@integer@``
* ``@number@``
* ``@double@``
* ``@boolean@``
* ``@time@``
* ``@date@``
* ``@datetime@``
* ``@timezone@`` || ``@tz``
* ``@array@``
* ``@array_previous@`` - match next array element using pattern from previous element
* ``@array_previous_repeat@`` - match all remaining array elements using pattern from previous element
* ``@...@`` - *unbounded array*, once used matcher will skip any further array elements
* ``@null@``
* ``@*@`` || ``@wildcard@``
* ``expr(expression)`` - **optional**, requires `symfony/expression-language: ^2.3|^3.0|^4.0|^5.0` to be present
* ``@uuid@``
* ``@ulid@``
* ``@json@``
* ``@string@||@integer@`` - string OR integer
* ``@enum@`` - used for PHP backend enums
* ``@tax_number@`` - matches tax identification number(only PL format)
* ``@regon@`` - matches registration number(only PL format)

### Available pattern expanders

* ``startsWith($stringBeginning, $ignoreCase = false)``
* ``endsWith($stringEnding, $ignoreCase = false)``
* ``contains($string, $ignoreCase = false)``
* ``notContains($string, $ignoreCase = false)``
* ``isDateTime()``
* ``isInDateFormat($format)`` - example `"@datetime@.isInDateFormat('Y-m-d H:i:s')`
* ``before(string $date)`` - example ``"@string@.isDateTime().before(\"2020-01-01 00:00:00\")"``
* ``after(string $date)`` - example ``"@string@.isDateTime().after(\"2020-01-01 00:00:00\")"``
* ``isTzOffset()``
* ``isTzIdentifier()``
* ``isTzAbbreviation()``
* ``isEmail()``
* ``isUrl()``
* ``isIp()``
* ``isEmpty()``
* ``isNotEmpty()``
* ``lowerThan($boundry)``
* ``greaterThan($boundry)``
* ``inArray($value)`` - example ``"@array@.inArray(\"ROLE_USER\")"`` 
* ``hasProperty($propertyName)`` - example ``"@json@.hasProperty(\"property_name\")"``
* ``oneOf(...$expanders)`` - example ``"@string@.oneOf(contains('foo'), contains('bar'), contains('baz'))"``
* ``matchRegex($regex)`` - example ``"@string@.matchRegex('/^lorem.+/')"``
* ``optional()`` - work's only with ``ArrayMatcher``, ``JsonMatcher`` and ``XmlMatcher``
* ``count()`` - work's only with ``ArrayMatcher`` - example ``"@array@.count(5)"``
* ``repeat($pattern, $isStrict = true)`` - example ``'@array@.repeat({"name": "foe"})'`` or ``"@array@.repeat('@string@')"``
* ``match($pattern)`` - example ``{"image":"@json@.match({\"url\":\"@string@.isUrl()\"})"}``
* ``isOneOf(class)`` - work's only with ``EnumMatcher``, as argument it accepts class name with namespace e.g. ``@enum@.isOneOf('App\Domain\OrderStatus::class')`` 

## Example usage

### Scalar matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(1, 1);
$matcher->match('string', 'string');
```

### String matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('Norbert', '@string@');
$matcher->match("lorem ipsum dolor", "@string@.startsWith('lorem').contains('ipsum').endsWith('dolor')");

```

### Time matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('00:00:00', '@time@');
$matcher->match('00:01:00.000000', '@time@');
$matcher->match('00:01:00', '@time@.after("00:00:00")');
$matcher->match('00:00:00', '@time@.before("01:00:00")');

```

### Date matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('2014-08-19', '@date@');
$matcher->match('2020-01-11', '@date@');
$matcher->match('2014-08-19', '@date@.before("2016-08-19")');
$matcher->match('2014-08-19', '@date@.before("today").after("+ 100year")');

```

### DateTime matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('2014-08-19', '@datetime@');
$matcher->match('2020-01-11 00:00:00', '@datetime@');
$matcher->match('2014-08-19', '@datetime@.before("2016-08-19")');
$matcher->match('2014-08-19', '@datetime@.before("today").after("+ 100year")');

```

### TimeZone matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('Europe/Warsaw', '@timezone@');
$matcher->match('Europe/Warsaw', '@tz@');
$matcher->match('GMT', '@tz@');
$matcher->match('01:00', '@tz@');
$matcher->match('01:00', '@tz@.isTzOffset()');
$matcher->match('GMT', '@tz@.isTzAbbreviation()');
$matcher->match('Europe/Warsaw', '@tz@.isTzIdentifier()');
```

### Integer matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(100, '@integer@');
$matcher->match(100, '@integer@.lowerThan(200).greaterThan(10)');

```

### Number matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(100, '@number@');
$matcher->match('200', '@number@');
$matcher->match(1.25, '@number@');
$matcher->match('1.25', '@number@');
$matcher->match(0b10100111001, '@number@');
```

### Double matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(10.1, "@double@");
$matcher->match(10.1, "@double@.lowerThan(50.12).greaterThan(10)");
```

### Boolean matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(true, "@boolean@");
$matcher->match(false, "@boolean@");
```

### Wildcard matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match("@integer@", "@*@");
$matcher->match("foobar", "@*@");
$matcher->match(true, "@*@");
$matcher->match(6.66, "@*@");
$matcher->match(array("bar"), "@wildcard@");
$matcher->match(new \stdClass, "@wildcard@");
```

### Expression matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(new \DateTime('2014-04-01'), "expr(value.format('Y-m-d') == '2014-04-01'");
$matcher->match("Norbert", "expr(value === 'Norbert')");
```

### UUID matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('9f4db639-0e87-4367-9beb-d64e3f42ae18', '@uuid@');
```

### ULID matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match('01BX5ZZKBKACTAV9WEVGEMMVS0', '@ulid@');
```

### Array matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(
   array(
      'users' => array(
          array(
              'id' => 1,
              'firstName' => 'Norbert',
              'lastName' => 'Orzechowicz',
              'roles' => array('ROLE_USER'),
              'position' => 'Developer',
          ),
          array(
              'id' => 2,
              'firstName' => 'Michał',
              'lastName' => 'Dąbrowski',
              'roles' => array('ROLE_USER')
          ),
          array(
              'id' => 3,
              'firstName' => 'Johnny',
              'lastName' => 'DąbrowsBravoki',
              'roles' => array('ROLE_HANDSOME_GUY')
          )
      ),
      true,
      6.66
  ),
   array(
      'users' => array(
          array(
              'id' => '@integer@.greaterThan(0)',
              'firstName' => '@string@',
              'lastName' => 'Orzechowicz',
              'roles' => '@array@',
              'position' => '@string@.optional()'
          ),
          array(
              'id' => '@integer@',
              'firstName' => '@string@',
              'lastName' => 'Dąbrowski',
              'roles' => '@array@'
          ),
          '@...@'
      ),
      '@boolean@',
      '@double@'
  )
);
```

### Array Previous

> @array_previous@ can also be used when matching JSON's and XML's

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(
   array(
      'users' => array(
          array(
              'id' => 1,
              'firstName' => 'Norbert',
              'lastName' => 'Orzechowicz',
              'roles' => array('ROLE_USER'),
              'position' => 'Developer',
          ),
          array(
              'id' => 2,
              'firstName' => 'Michał',
              'lastName' => 'Dąbrowski',
              'roles' => array('ROLE_USER')
          ),
          array(
              'id' => 3,
              'firstName' => 'Johnny',
              'lastName' => 'DąbrowsBravoki',
              'roles' => array('ROLE_HANDSOME_GUY')
          )
      ),
      true,
      6.66
  ),
   array(
      'users' => array(
          array(
              'id' => '@integer@.greaterThan(0)',
              'firstName' => '@string@',
              'lastName' => 'Orzechowicz',
              'roles' => '@array@',
              'position' => '@string@.optional()'
          ),
          '@array_previous@',
          '@array_previous@'
      ),
      '@boolean@',
      '@double@'
  )
);
```

### Array Previous Repeat

> @array_previous_repeat@ can also be used when matching JSON's and XML's

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(
   array(
      'users' => array(
          array(
              'id' => 1,
              'firstName' => 'Norbert',
              'lastName' => 'Orzechowicz',
              'roles' => array('ROLE_USER'),
              'position' => 'Developer',
          ),
          array(
              'id' => 2,
              'firstName' => 'Michał',
              'lastName' => 'Dąbrowski',
              'roles' => array('ROLE_USER')
          ),
          array(
              'id' => 3,
              'firstName' => 'Johnny',
              'lastName' => 'DąbrowsBravoki',
              'roles' => array('ROLE_HANDSOME_GUY')
          )
      ),
      true,
      6.66
  ),
   array(
      'users' => array(
          array(
              'id' => '@integer@.greaterThan(0)',
              'firstName' => '@string@',
              'lastName' => 'Orzechowicz',
              'roles' => '@array@',
              'position' => '@string@.optional()'
          ),
          '@array_previous_repeat@'
      ),
      '@boolean@',
      '@double@'
  )
);
```

### Json matching

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(
  '{
    "users":[
      {
        "firstName": "Norbert",
        "lastName": "Orzechowicz",
        "created": "2014-01-01",
        "roles":["ROLE_USER", "ROLE_DEVELOPER"]
      }
    ]
  }',
  '{
    "users":[
      {
        "firstName": "@string@",
        "lastName": "@string@",
        "created": "@string@.isDateTime()",
        "roles": "@array@",
        "position": "@string@.optional()"
      }
    ]
  }'
);
```

### Json matching with unbounded arrays and objects

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(
  '{
    "users":[
      {
        "firstName": "Norbert",
        "lastName": "Orzechowicz",
        "created": "2014-01-01",
        "roles":["ROLE_USER", "ROLE_DEVELOPER"],
        "attributes": {
          "isAdmin": false,
          "dateOfBirth": null,
          "hasEmailVerified": true
        },
        "avatar": {
          "url": "http://avatar-image.com/avatar.png"
        }
      },
      {
        "firstName": "Michał",
        "lastName": "Dąbrowski",
        "created": "2014-01-01",
        "roles":["ROLE_USER", "ROLE_DEVELOPER", "ROLE_ADMIN"],
        "attributes": {
          "isAdmin": true,
          "dateOfBirth": null,
          "hasEmailVerified": true
        },
        "avatar": null
      }
    ]
  }',
  '{
    "users":[
      {
        "firstName": "@string@",
        "lastName": "@string@",
        "created": "@string@.isDateTime()",
        "roles": [
            "ROLE_USER",
            "@...@"
        ],
        "attributes": {
          "isAdmin": @boolean@,
          "@*@": "@*@"
        },
        "avatar": "@json@.match({\"url\":\"@string@.isUrl()\"})"
      }
      ,
      @...@
    ]
  }'
);
```

### Xml matching

**Optional** - requires `openlss/lib-array2xml: ^1.0` to be present. 

```php
<?php

use AppVerk\PHPMatcher\PHPMatcher;

$matcher = new PHPMatcher();

$matcher->match(<<<XML
<?xml version="1.0"?>
<soap:Envelope
xmlns:soap="http://www.w3.org/2001/12/soap-envelope"
soap:encodingStyle="http://www.w3.org/2001/12/soap-encoding">

<soap:Body xmlns:m="http://www.example.org/stock">
  <m:GetStockPrice>
    <m:StockName>IBM</m:StockName>
    <m:StockValue>Any Value</m:StockValue>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML
                ,
                <<<XML
<?xml version="1.0"?>
<soap:Envelope
    xmlns:soap="@string@"
            soap:encodingStyle="@string@">

<soap:Body xmlns:m="@string@">
  <m:GetStockPrice>
    <m:StockName>@string@</m:StockName>
    <m:StockValue>@string@</m:StockValue>
    <m:StockQty>@integer@.optional()</m:StockQty>
  </m:GetStockPrice>
</soap:Body>

</soap:Envelope>
XML
        );
```

Example scenario for api in behat using mongo.
---
``` cucumber
@profile, @user
Feature: Listing user toys

  As a user
  I want to list my toys

  Background:
    Given I send and accept JSON

  Scenario: Listing toys
    Given the following users exist:
      | firstName     | lastName     |
      | Chuck         | Norris       |

    And the following toys user "Chuck Norris" exist:
      | name            |
      | Barbie          |
      | GI Joe          |
      | Optimus Prime   |

    When I set valid authorization code oauth header for user "Chuck Norris"
    And I send a GET request on "/api/toys"
    Then the response status code should be 200
    And the JSON response should match:
    """
      [
        {
          "id": "@string@",
          "name": "Barbie",
          "_links: "@*@"
        },
        {
          "id": "@string@",
          "name": "GI Joe",
          "_links": "@*@"
        },
        {
          "id": "@string@",
          "name": "Optimus Prime",
          "_links": "@*@"
        }
      ]
    """
```

## PHPUnit integration

The `assertMatchesPattern()` is a handy assertion that matches values in PHPUnit tests.
To use it either include the `AppVerk\PHPMatcher\PHPUnit\PHPMatcherAssertions` trait,
or extend the `AppVerk\PHPMatcher\PHPUnit\PHPMatcherTestCase`:

```php
namespace AppVerk\PHPMatcher\Tests\PHPUnit;

use AppVerk\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use PHPUnit\Framework\TestCase;

class PHPMatcherAssertionsTest extends TestCase
{
    use PHPMatcherAssertions;

    public function test_it_asserts_if_a_value_matches_the_pattern()
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }
}
```

The `matchesPattern()` method can be used in PHPUnit stubs or mocks:

```php
$mock = $this->createMock(Foo::class);
$mock->method('bar')
    ->with($this->matchesPattern('@string@'))
    ->willReturn('foo');
```

## License

This library is distributed under the MIT license. Please see the LICENSE file.
