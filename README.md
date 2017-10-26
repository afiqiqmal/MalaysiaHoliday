<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="description" content="Parsing Malaysia Public Holiday into JSON">
<link rel="author" href="https://www.facebook.com/zeroone93">

# Malaysia Holiday Date List
Parsing Malaysia Public Holiday.


### Usage

Holidays in current years

<pre>
Holiday::init()->getAllRegionHoliday()->get();
</pre>

Holidays in specific years

<pre>
Holiday::init()->getAllRegionHoliday("2017")->get();
</pre>

Holidays by regional

<pre>
Holiday::init()->getRegionHoliday("Selangor")->get();
Holiday::init()->getRegionHoliday(["Selangor","Malacca"])->get();
</pre>

Holidays by regional in 2017

<pre>
Holiday::init()->getRegionHoliday("Selangor","2017")->get();
</pre>


Grouping and Filter result

<pre>
Holiday::init()->getAllRegionHoliday()->groupByMonth()->get();
Holiday::init()->getAllRegionHoliday()->filterByMonth("January")->get();  //date('F')
</pre>


### install

`composer require afiqiqmal/malaysiaholiday`

or 

<pre>
require{
	"afiqiqmal/malaysiaholiday": "^1.0.4"
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
