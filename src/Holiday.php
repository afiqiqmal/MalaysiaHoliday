<?php

namespace afiqiqmal\MalaysiaHoliday;
use Goutte\Client;

class Holiday
{
	private $base_url;
	private $default_path;
	private $regional_path;

	private $client;
	private $result;

	private $month;
	private $groupByMonth = false;

	public function __construct()
	{
      	$this->base_url = "http://www.officeholidays.com/countries/malaysia";
      	$this->regional_path = "/regional.php";

      	$this->client = new Client();
   	}
   	//Holiday holiday = new Holiday;
	public static function init()
	{
		return new self;
	}

	public function getAllRegionHoliday($year = 2017)
	{	
		$this->result = $this->baseRequest(null,$year);
		return $this;
	}

	public function getRegionHoliday($region,$year = 2017)
	{
		$this->result = $this->baseRequest($region,$year);
		return $this;
	}

	public function groupByMonth(){
		$this->groupByMonth = true;
		return $this;
	}

	public function filterByMonth($month){
		$this->month = $month;
		return $this;
	}

	public function get(){
		if($this->result['status'])
		{
			if ($this->month != null && $this->checkMonth($this->month)) {
				foreach ($this->result['data'] as $key => $holiday){
					if(date('F',strtotime($holiday['date'])) == $this->month){
						$temp[] = $holiday;
					}
				}

				$this->result['data'] = $temp;

				header('Content-Type: application/json');
				return json_encode($this->result);
			}
			else if($this->groupByMonth)
			{
				foreach ($this->result['data'] as $key => $holiday) 
				{
					$temp[date('F',strtotime($holiday['date']))][] = $holiday;
				}

				$this->result['data'] = $temp;

				header('Content-Type: application/json');	
				return json_encode($this->result);
			}
			else{

				header('Content-Type: application/json');	
				return json_encode($this->result);
			}
		}
		else{
			return [
					'status' => false,
					'message' => "Error occured with the results"
				];
		}
	}

	private function baseRequest($region,$year)
	{
		return $this->queryWeb($region,$year);	
	}

	private function queryWeb($regional,$year)
	{
		$request_url = null;
		$currentYear = ($year==null)?date('Y'):$year;
		if ($regional == null) {
			$request_url = $this->base_url."/".$year.".php";
		}
		else 
		{
			if ($this->checkRegional($regional)) 
			{
				$request_url = $this->base_url.$this->regional_path."?list_year=".$currentYear."&list_region=".$regional;	
			}
			else
			{
				return [
					'status' => false,
					'message' => $regional." is not include in the regional state"
				];
			}
		}

		if ($request_url !=null) 
		{
			$crawler = $this->client->request('GET', $request_url);
			$result = $crawler->filter('.list-table tr')->each(function ($node) use ($currentYear){
			    if($node->children()->nodeName() == 'td'){
					$temp['day'] = trim($node->children()->eq(0)->text());
					$date_str = strtok(trim($node->children()->eq(1)->extract('_text','class')[0]),"\n")." ".$currentYear;
					$temp['date'] = date_format(date_create_from_format('F d Y',$date_str),'d-m-Y');
					$temp['month'] = date('F',strtotime($temp['date']));
					$temp['name'] = trim($node->children()->eq(2)->text());
					$temp['description'] = trim($node->children()->eq(3)->text());
					switch ($node->extract('class')[0]) {
							case 'govt_holiday':
								$temp['type'] = "Government/Public Sector Holiday";
								break;
							case 'publicholiday':
								$temp['type'] = "Not a Public Holiday";
								break;
							case 'holiday':
								$temp['type'] = "National Holiday";
								break;
							case 'regional':
								$temp['type'] = "Regional Holiday";
								break;
							default:
								$temp['type'] = "Unknown";
								break;
					}
					
					return $temp;
				}
			});

			$arrays = array_values(array_filter($result));

			return [
				'status' => true,
				'regional' => ($regional == null)?"all":$regional,
				'year'=> $currentYear,
				'data' => $arrays,
				'sources' => $request_url,
				'developer' => [
					"name"=> "Hafiq",
				    "email"=> "hafiqiqmal93@gmail.com",
				    "github"=> "https://github.com/afiqiqmal"
				]
			];
		}
		else
		{
			return [
					'status' => false,
					'message' => "Wrong Url Format"
				];
		}
	}

	private function checkRegional($regional)
	{
		$arrays = array('Johor','Kedah','Kelantan','Kuala Lumpur','Labuan','Malacca','Negeri Sembilan','Pahang','Penang','Perak','Perlis','Putrajaya','Sarawak','Selangor','Terengganu');
		if (in_array(strtolower($regional), array_map('strtolower', $arrays))) {
			return true;
		}
		return false;
	}

	private function checkMonth($month)
	{
		$arrays = array('January','February','March','April','May','June','July','August','September','October','November','December');
		if (in_array(strtolower($month), array_map('strtolower', $arrays))) {
			return true;
		}
		return false;
	}
}














