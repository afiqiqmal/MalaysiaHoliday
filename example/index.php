<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ .'/../vendor/autoload.php';

use afiqiqmal\MalaysiaHoliday\Holiday;

$holiday = new Holiday;

$result = $holiday->getRegionHoliday(['Selangor', 'Malacca'])->get();

//print_r($result);
header('Content-Type: application/json');
echo json_encode($result['data']['Selangor']);
