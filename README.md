# Malaysia Holidays :airplane:
Parsing Malaysian Public Holidays

[![Tests](https://github.com/afiqiqmal/MalaysiaHoliday/actions/workflows/php.yml/badge.svg)](https://github.com/afiqiqmal/MalaysiaHoliday/actions/workflows/php.yml)
[![Coverage](https://img.shields.io/codecov/c/github/afiqiqmal/MalaysiaHoliday.svg)](https://codecov.io/gh/afiqiqmal/MalaysiaHoliday)
[![Packagist](https://img.shields.io/packagist/dt/afiqiqmal/MalaysiaHoliday.svg)](https://packagist.org/packages/afiqiqmal/MalaysiaHoliday)
[![Packagist](https://img.shields.io/packagist/v/afiqiqmal/MalaysiaHoliday.svg)](https://packagist.org/packages/afiqiqmal/MalaysiaHoliday)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/paypalme/mhi9388?locale.x=en_US)


![](https://banners.beyondco.de/Malaysia%20Holiday.png?theme=dark&packageName=afiqiqmal%2Fmalaysiaholiday&pattern=cage&style=style_1&description=Parsing+Malaysia+Public+Holiday&md=1&fontSize=100px&images=globe)


### Holidays based on country
If you want the list of holidays based on countries, you may refer here https://github.com/afiqiqmal/country-holiday


### How to Use :sparkles:

Declare
```php
$holiday = new MalaysiaHoliday;
MalaysiaHoliday::make();
app(MalaysiaHoliday::class); // if bound with laravel refer here - https://laravel.com/docs/13.x/container#contextual-binding
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

### School Holidays :school:

Malaysia school holidays (Kumpulan A & Kumpulan B), including term and festive holidays

```php
MalaysiaSchoolHoliday::make()->get(); // all groups, current year
MalaysiaSchoolHoliday::make()->fromState("Selangor")->get();
MalaysiaSchoolHoliday::make()->fromState(["Selangor", "Kedah"])->get();
MalaysiaSchoolHoliday::make()->ofYear(2026)->get();
```

> Source only publishes the current academic year. Requesting another year returns `status: false` with a message.

Sample
<pre>
{
   "status":true,
   "year":2026,
   "data":[
      {
         "regional":"Selangor",
         "group":"Kumpulan B",
         "collection":[
            {
               "name":"Term 1 Holidays",
               "start_date":"2026-03-21",
               "end_date":"2026-03-29",
               "start_day":"Saturday",
               "end_day":"Sunday",
               "total_days":9,
               "is_holiday":true,
               "type":"Term Holiday",
               "states":["Johor", "Kuala Lumpur", "..."]
            }
         ]
      }
   ],
   "error_messages":[]
}
</pre>

`type` is one of `School Session` (first day of school), `Term Holiday` or `Festive Holiday`.

### Requirements
- PHP 8.2 - 8.5
- Symfony 7.4 / 8.x components
- Framework agnostic — works with Laravel 11, 12 and 13

### To install

run

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

School holidays scraped from - https://publicholidays.com.my/school-holidays/

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

<br>

### Donate to the project :tea: 

<a href="https://www.paypal.com/paypalme/mhi9388?locale.x=en_US"><img src="https://i.imgur.com/Y2gqr2j.png" height="40"></a> 



