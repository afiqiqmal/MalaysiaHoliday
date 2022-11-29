<?php

namespace afiqiqmal\MalaysiaHoliday;

use afiqiqmal\MalaysiaHoliday\exception\RegionException;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use function GuzzleHttp\Psr7\str;

class MalaysiaHoliday
{
    private $months_array = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];

    public static $region_array = [
        'Johor',
        'Kedah',
        'Kelantan',
        'Kuala Lumpur',
        'Labuan',
        'Melaka',
        'Negeri Sembilan',
        'Pahang',
        'Penang',
        'Perak',
        'Perlis',
        'Putrajaya',
        'Sarawak',
        'Selangor',
        'Terengganu'
    ];

    private $related_region = [
        'Johore' => 'Johor',
        'KL' => 'Kuala Lumpur',
        'Malacca' => 'Melaka',
        'Pulau Pinang' => 'Penang'
    ];

    private $base_url = "https://www.officeholidays.com/countries/malaysia";
    private $client;

    private $year;
    private $region;

    private $month;
    private $groupByMonth = false;

    public function __construct()
    {
        $this->client = new Client();
    }

    public static function make()
    {
        return new self;
    }

    public function fromAllState($year = null)
    {
        $this->region = null;
        $this->year = $year;
        return $this;
    }

    public function fromState($region, $year = null)
    {
        $this->region = $region;
        $this->year = $year;
        return $this;
    }

    public function ofYear($year)
    {
        $this->year = $year;
        return $this;
    }

    public function groupByMonth()
    {
        $this->groupByMonth = true;
        return $this;
    }

    public function filterByMonth($month)
    {
        $this->month = $month;
        return $this;
    }

    public function get()
    {
        $result = $this->queryWeb($this->region, $this->year);

        if ($result['status']) {
            if ($this->month != null && $this->checkMonth($this->month)) {
                foreach ($result['data'] as $key => $data) { //regional
                    foreach ($data['collection'] as $index => $collection) { //year
                        $temp = [];
                        foreach ($collection['data'] as $key2 => $holiday) { // holidays
                            $month = date('F', strtotime($holiday['date']));
                            if (strtolower($month) == $this->getMonth($this->month)) {
                                $temp[] = $holiday;
                            }
                        }

                        $result['data'][$key]['collection'][$index]['data'] = $temp;
                    }
                }

                return $result;
            } elseif ($this->groupByMonth) {
                foreach ($result['data'] as $key => $data) { //regional
                    foreach ($data['collection'] as $index => $collection) { //year
                        $temp = [];
                        foreach ($collection['data'] as $key2 => $holiday) { // holidays
                            $month = date('F', strtotime($holiday['date']));
                            $entry = array_search($month, array_column($temp, 'month'));
                            if ($entry === false) {
                                $temp[] = [
                                    'month' => $month,
                                    'data' => [$holiday],
                                ];
                            } else {
                                $temp[$entry]['data'][] = $holiday;
                            }
                        }

                        $result['data'][$key]['collection'][$index]['data'] = $temp;
                    }
                }

                return $result;
            } else {
                return $result;
            }
        } else {
            return [
                'status' => false,
                'message' => "Error occured with the results"
            ];
        }
    }

    private function queryWeb($regional, $year)
    {
        $years = ($year == null) ? [date('Y')] : $year;
        if (!is_array($years)) {
            $years = [$year];
        }

        if (!is_array($regional)) {
            $regional = [$regional];
        }

        $final = [];
        $error_messages = [];
        foreach ($regional as $region) {
            $data = [];
            try {
                foreach ($years as $selectedYear) {
                    $data[] = [
                        'year' => (int)$selectedYear,
                        'data' => $this->trigger($region, $selectedYear),
                    ];
                }

                if ($region) {
                    $final[] = [
                        'regional' => $region,
                        'collection' => $data
                    ];
                } else {
                    $final = [
                        'regional' => "Malaysia",
                        'collection' => $data
                    ];
                }
            } catch (RegionException $regionException) {
                $error_messages[] = $regionException->getMessage();
                $final[] = [
                    'regional' => $region ?? "Malaysia",
                    'collection' => []
                ];
            }
        }

        return [
            'status' => true,
            'data' => $final ?? [],
            'error_messages' => $error_messages,
            'developer' => [
                "name" => "Hafiq",
                "email" => "hafiqiqmal93@gmail.com",
                "github" => "https://github.com/afiqiqmal"
            ]
        ];
    }

    /**
     *
     * @param $region
     * @param $currentYear
     * @return array
     * @throws RegionException
     */
    private function trigger($region, $currentYear)
    {
        if ($region) {
            $request_url = $this->base_url."/".$this->checkRegional($region)."/".$currentYear;
        } else {
            $request_url = $this->base_url."/".$currentYear;
        }

        return array_values(array_filter($this->crawl($request_url, $currentYear)));
    }

    private function crawl($request_url, $currentYear)
    {
        $crawler = $this->client->request('GET', $request_url);
        return $crawler->filter('.country-table tr')->each(
            function ($node) use ($currentYear) {
                if ($node->children()->nodeName() == 'td') {
                    $temp['day'] = trim($node->children()->eq(0)->text());
                    $date_str = strtok(trim($node->children()
                            ->eq(1)
                            ->extract(['_text', 'class'])[0][0]), "\n")." ".$currentYear;
                    if ($date_str == null || empty($date_str)) {
                        return null;
                    }

                    $date = date_create_from_format('F d Y', preg_replace("/[\n\r]/", "", $date_str));

                    if (!$date) { //check another format
                        $date = date_create_from_format('Y-m-d', $node->children()->eq(1)->children()->text());

                        if (!$date) {
                            return null;
                        }
                    }

                    $temp['date'] = date_format($date, 'Y-m-d');
                    $temp['date_formatted'] = date_format($date, 'd F Y');
                    $temp['month'] = date('F', strtotime($temp['date']));
                    $temp['name'] = trim($node->children()->eq(2)->text());
                    $temp['description'] = trim($node->children()->eq(3)->text());
                    $temp['is_holiday'] = true;
                    switch (trim($node->extract(['class'])[0])) {
                        case 'govt_holiday':
                            $temp['type'] = "Government/Public Sector Holiday";
                            $temp['type_id'] = 1;
                            break;
                        case 'nap-past':
                        case 'nap':
                            $temp['type'] = "Not a Public Holiday";
                            $temp['is_holiday'] = false;
                            $temp['type_id'] = 2;
                            break;
                        case 'country-past':
                        case 'country':
                            $temp['type'] = "National Holiday";
                            $temp['type_id'] = 3;
                            break;
                        case 'region-past':
                        case 'region':
                            $temp['type'] = "Regional Holiday";
                            $temp['type_id'] = 4;
                            break;
                        default:
                            $temp['type'] = "Unknown";
                            $temp['type_id'] = 5;
                            break;
                    }

                    return $temp;
                }
            }
        );
    }

    private function checkRegional($regional)
    {
        foreach ($this->related_region as $index => $state) {
            if (strtolower($index) == strtolower($regional)) {
                $regional = $state;
                break;
            }
        }

        if (in_array(strtolower($regional), array_map('strtolower', self::$region_array))) {
            return str_replace(" ", "-", $regional);
        }

        throw new RegionException($regional . " is not include in the regional state");
    }

    private function checkMonth($month)
    {
        if (in_array(strtolower($month), array_map('strtolower', array_values($this->months_array)))) {
            return true;
        }

        return isset($this->months_array[$month]);
    }

    private function getMonth($month)
    {
        return strtolower($this->months_array[$month]) ?? strtolower($month);
    }
}














