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
	"afiqiqmal/malaysiaholiday": "^1.0.3"
}
</pre>


### Source
Scraped from - http://www.officeholidays.com/countries/malaysia

### MIT Licence
