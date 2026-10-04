<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Holiday\MalaysiaHoliday;

/**
 * RequestTest.php
 * to test function in Request class
 */
class HolidayTest extends TestCase
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

    public function testFilterByMonthName()
    {
        $response = MalaysiaHoliday::make()->fromState('Selangor', 2026)->filterByMonth('August')->get();

        $this->assertTrue($response['status']);
        foreach ($response['data'][0]['collection'][0]['data'] as $holiday) {
            $this->assertSame('August', $holiday['month']);
        }
    }

    public function testAllStateGroupByMonth()
    {
        $response = MalaysiaHoliday::make()->fromAllState(2026)->groupByMonth()->get();

        $this->assertTrue($response['status']);
        $this->assertSame('Malaysia', $response['data']['regional']);
        $this->assertSame('January', $response['data']['collection'][0]['data'][0]['month']);
    }

    public function testCrawledDataIsValid()
    {
        $response = MalaysiaHoliday::make()->fromState(['Selangor', 'Melaka'], 2026)->get();

        foreach ($response['data'] as $region) {
            $holidays = $region['collection'][0]['data'];
            $this->assertGreaterThan(15, count($holidays), $region['regional']);

            foreach ($holidays as $holiday) {
                $this->assertNotSame('', $holiday['name']);
                $this->assertStringStartsWith('2026-', $holiday['date']);
                $this->assertSame(date('l', strtotime($holiday['date'])), $holiday['day']);
                $this->assertNotSame(5, $holiday['type_id'], "Unknown type: {$holiday['name']}");
            }

            $national = array_column($holidays, 'name', 'date');
            $this->assertSame('National Day', $national['2026-08-31']);
            $this->assertSame('Malaysia Day', $national['2026-09-16']);
        }
    }
}
