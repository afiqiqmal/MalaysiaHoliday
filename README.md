# Malaysia Holiday Date List
Parsing Malaysia Public Holiday.

[![Build Status](https://travis-ci.org/xmhafiz/MalaysiaHoliday.svg?branch=master)](https://travis-ci.org/afiqiqmal/MalaysiaHoliday)
[![Coverage](https://img.shields.io/codecov/c/github/afiqiqmal/MalaysiaHoliday.svg)](https://codecov.io/gh/afiqiqmal/MalaysiaHoliday)
[![Packagist](https://img.shields.io/packagist/dt/afiqiqmal/MalaysiaHoliday.svg)](https://packagist.org/packages/afiqiqmal/MalaysiaHoliday)
[![Packagist](https://img.shields.io/packagist/v/afiqiqmal/MalaysiaHoliday.svg)](https://packagist.org/packages/afiqiqmal/MalaysiaHoliday)


### Usage

Holidays in current years

```php
$holiday = new Holiday;
$holiday->getAllRegionHoliday()->get();
```

Holidays in specific years

```php
$holiday = new Holiday;
$holiday->getAllRegionHoliday(2017)->get();
$holiday->getAllRegionHoliday([2017, 2019])->get();
```

Holidays by regional

```php
$holiday = new Holiday;
$holiday->getRegionHoliday("Selangor")->get();
$holiday->getRegionHoliday(["Selangor","Malacca"])->get();
```

Holidays by regional in 2017

```php
$holiday = new Holiday;
$holiday->getRegionHoliday("Selangor","2017")->get();
$holiday->getRegionHoliday("Selangor", [2017, 2019])->get();
$holiday->getRegionHoliday(["Selangor","Malacca"], [2017, 2019])->get();
```


Grouping and Filter result

```php
$holiday = new Holiday;
$holiday->getAllRegionHoliday()->groupByMonth()->get();
$holiday->getAllRegionHoliday()->filterByMonth("January")->get();  //date('F')
```

### Requirement
- PHP 7.0+ (because 5.6 too old 😝)

### install

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

### Source
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
