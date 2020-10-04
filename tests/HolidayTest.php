<?php

namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use afiqiqmal\MalaysiaHoliday\MalaysiaHoliday;

/**
 * RequestTest.php
 * to test function in Request class
 */
class RequestTest extends TestCase
{
    /**
     * To test getting all region holiday in Malaysia
     */
    public function testGetAllRegionHoliday()
    {
        $holiday = new MalaysiaHoliday;
        $response = $holiday->fromAllState()->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data']['regional'] == 'Malaysia');
    }

    /**
     * To test getting specific region holiday
     */
    public function testGetSpecificRegionHoliday()
    {
        $holiday = new MalaysiaHoliday;
        $response = $holiday->fromState('Selangor')->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');
    }

    /**
     * To test getting multiple regions holiday
     */
    public function testGetMultipleRegionsHoliday()
    {
        $holiday = new MalaysiaHoliday;
        $response = $holiday->fromState(['Selangor', 'Malacca'])->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');
        $this->assertTrue($response['data'][1]['regional'] == 'Malacca');
    }

    public function testGetAllRegionsHoliday()
    {
        $holiday = new MalaysiaHoliday;
        $response = $holiday->fromState(MalaysiaHoliday::$region_array)->get();

        $this->assertTrue($response['status']);
        foreach (MalaysiaHoliday::$region_array as $key => $regional) {
            $this->assertTrue($response['data'][$key]['regional'] == $regional);
        }
    }

    /**
     * To test getting multiple regions holiday
     */
    public function testErrorMessage()
    {
        $holiday = new MalaysiaHoliday;
        $response = $holiday->fromState(['Selangor', 'Malaccaa'])->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');


        $this->assertFalse($response['data'][1]['regional'] == 'Malacca');
        $this->assertTrue($response['data'][1]['collection'] == []);

        $this->assertCount(1, $response['error_messages']);
        $this->assertTrue($response['error_messages'][0] == 'Malaccaa is not include in the regional state');
    }
}