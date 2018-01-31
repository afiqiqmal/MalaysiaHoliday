# Malaysia Holiday Date List
Parsing Malaysia Public Holiday.

[![Build Status](https://travis-ci.org/xmhafiz/MalaysiaHoliday.svg?branch=master)](https://travis-ci.org/afiqiqmal/MalaysiaHoliday)
[![Coverage](https://img.shields.io/codecov/c/github/afiqiqmal/MalaysiaHoliday.svg)](https://codecov.io/gh/afiqiqmal/MalaysiaHoliday)



### Usage

Holidays in current years

```php
$holiday = new Holiday;
$holiday->getAllRegionHoliday()->get();
```

Holidays in specific years

```php
$holiday = new Holiday;
$holiday->getAllRegionHoliday("2017")->get();
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
```


Grouping and Filter result

```php
$holiday = new Holiday;
$holiday->getAllRegionHoliday()->groupByMonth()->get();
$holiday->getAllRegionHoliday()->filterByMonth("January")->get();  //date('F')
```

### Requirement
- PHP 7.1+
- Tested in 7.1

### install

`composer require afiqiqmal/malaysiaholiday`

or 

```
require{
	"afiqiqmal/malaysiaholiday": "^1.1.0"
}
```


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
