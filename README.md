# Malaysia Holidays :airplane:
Parsing Malaysian Public Holidays

[![Build Status](https://travis-ci.org/xmhafiz/MalaysiaHoliday.svg?branch=master)](https://travis-ci.org/afiqiqmal/MalaysiaHoliday)
[![Coverage](https://img.shields.io/codecov/c/github/afiqiqmal/MalaysiaHoliday.svg)](https://codecov.io/gh/afiqiqmal/MalaysiaHoliday)
[![Packagist](https://img.shields.io/packagist/dt/afiqiqmal/MalaysiaHoliday.svg)](https://packagist.org/packages/afiqiqmal/MalaysiaHoliday)
[![Packagist](https://img.shields.io/packagist/v/afiqiqmal/MalaysiaHoliday.svg)](https://packagist.org/packages/afiqiqmal/MalaysiaHoliday)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/paypalme/mhi9388?locale.x=en_US)


![](https://banners.beyondco.de/Malaysia%20Holiday.png?theme=dark&packageName=afiqiqmal%2Fmalaysiaholiday&pattern=cage&style=style_1&description=Parsing+Malaysia+Public+Holiday&md=1&fontSize=100px&images=globe)


### How to Use :sparkles:

Declare
```php
$holiday = new MalaysiaHoliday;
MalaysiaHoliday::make();
app(MalaysiaHoliday::class); // if bound with laravel refer here - https://laravel.com/docs/8.x/container#contextual-binding
```


Holidays in current year

```php
$holiday = new MalaysiaHoliday; // MalaysiaHoliday::make()
$holiday->fromAllState()->get();
MalaysiaHoliday::make()->fromAllState()->get();
```

Holidays in specific years

```php
$holiday = new MalaysiaHoliday;
$holiday->fromAllState(2017)->get();
$holiday->fromAllState([2017, 2019])->get();
$holiday->fromAllState()->ofYear(2017)->get();
MalaysiaHoliday::make()->fromAllState()->ofYear(2017)->get();
```

Holidays by region

```php
$holiday = new MalaysiaHoliday;
$holiday->fromState("Selangor")->get();
$holiday->fromState(["Selangor","Malacca"])->get();
```

Holidays by region and year

```php
$holiday = new MalaysiaHoliday;
$holiday->fromState("Selangor","2017")->get();
$holiday->fromState("Selangor", [2017, 2019])->get();
$holiday->fromState(["Selangor","Malacca"], [2017, 2019])->get();
$holiday->fromState(["Selangor","Malacca"])->ofYear([2017, 2019])->get();
```


Group and filter results

```php
$holiday = new MalaysiaHoliday;
$holiday->fromAllState()->groupByMonth()->get();
$holiday->fromAllState()->filterByMonth("January")->get();  //date('F')
```

### Requirements
- PHP 7.0 and above (because 5.6 is too old 😝)

### to install

`composer require afiqiqmal/malaysiaholiday`

### Sample
<pre>
{
   "status":true,
   "data":[
      {
         "regional":"Selangor",
         "collection":[
            {
               "year":2019,
               "data":[
                  {
                     "day":"Tuesday",
                     "date":"2019-01-01",
                     "date_formatted":"01 January 2019",
                     "month":"January",
                     "name":"New Year's Day",
                     "description":"Regional Holiday",
                     "is_holiday":true,
                     "type":"Regional Holiday",
                     "type_id":4
                  },
                  {
                     "day":"Monday",
                     "date":"2019-01-21",
                     "date_formatted":"21 January 2019",
                     "month":"January",
                     "name":"Thaipusam",
                     "description":"Regional Holiday",
                     "is_holiday":true,
                     "type":"Regional Holiday",
                     "type_id":4
                  }
               ]
            }
         ]
      },
      {
         "regional":"Johor",
         "collection":[
            {
               "year":2019,
               "data":[
                  {
                     "day":"Monday",
                     "date":"2019-01-21",
                     "date_formatted":"21 January 2019",
                     "month":"January",
                     "name":"Thaipusam",
                     "description":"Regional Holiday",
                     "is_holiday":true,
                     "type":"Regional Holiday",
                     "type_id":4
                  }
               ]
            }
         ]
      }
   ],
   "developer":{
      "name":"Hafiq",
      "email":"hafiqiqmal93@gmail.com",
      "github":"https://github.com/afiqiqmal"
   }
}
</pre>

### Source :date:

Scraped from - http://www.officeholidays.com/countries/malaysia

### MIT Licence

Copyright © 2017 @afiqiqmal

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the “Software”), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.


Donate to the project :tea:

<a href="https://www.paypal.com/paypalme/mhi9388?locale.x=en_US"><img src="https://i.imgur.com/Y2gqr2j.png" height="40"></a>  

