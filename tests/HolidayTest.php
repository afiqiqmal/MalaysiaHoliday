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

        $responseObject = json_decode($response);

        $this->assertTrue($responseObject->status);
    }

    /**
     * To test getting specific region holiday
     */
    function testGetSpecificRegionHoliday()
    {
        $holiday = new Holiday;
        $response = $holiday->getRegionHoliday('Selangor')->get();

        $responseObject = json_decode($response);

        $this->assertTrue($responseObject->status);
    }

    /**
     * To test getting multiple regions holiday
     */
    function testGetMultipleRegionsHoliday()
    {
        $holiday = new Holiday;
        $response = $holiday->getRegionHoliday(['Selangor', 'Malacca'])->get();

        $responseObject = json_decode($response);
        $this->assertTrue(isset($responseObject->data->Selangor));
        $this->assertTrue(isset($responseObject->data->Malacca));
    }
}