<?php

namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use afiqiqmal\MalaysiaHoliday\Holiday;

/**
 * RequestTest.php
 * to test function in Request class
 */
class RequestTest extends TestCase
{
    /**
     * To test getting all region holiday in Malaysia
     */
    function testGetAllRegionHoliday()
    {
        $holiday = new Holiday;
        $response = $holiday->getAllRegionHoliday()->get();

        $this->assertTrue($response['status']);
    }

    /**
     * To test getting specific region holiday
     */
    function testGetSpecificRegionHoliday()
    {
        $holiday = new Holiday;
        $response = $holiday->getRegionHoliday('Selangor')->get();

        $this->assertTrue($response['status']);
    }

    /**
     * To test getting multiple regions holiday
     */
    function testGetMultipleRegionsHoliday()
    {
        $holiday = new Holiday;
        $response = $holiday->getRegionHoliday(['Selangor', 'Malacca'])->get();

        $this->assertTrue(isset($response['data']['Selangor']));
        $this->assertTrue(isset($response['data']['Malacca']));
    }
}