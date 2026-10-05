<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Holiday\MalaysiaHoliday;
use Holiday\MalaysiaSchoolHoliday;

class SchoolHolidayTest extends TestCase
{
    /**
     * To test getting school holidays for all groups
     */
    public function testGetAllGroupSchoolHoliday()
    {
        $response = MalaysiaSchoolHoliday::make()->get();

        $this->assertTrue($response['status']);
        $this->assertSame(['Kumpulan A', 'Kumpulan B'], array_column($response['data'], 'group'));
        $this->assertContains('Kedah', $response['data'][0]['states']);
        $this->assertContains('Selangor', $response['data'][1]['states']);
        $this->assertNotEmpty($response['data'][0]['collection']);
    }

    /**
     * To test getting school holidays by state, including alias and invalid state
     */
    public function testGetSchoolHolidayByState()
    {
        $response = MalaysiaSchoolHoliday::make()->fromState(['Selangor', 'KL', 'Malaccaa'])->get();

        $this->assertTrue($response['status']);
        $this->assertSame('Selangor', $response['data'][0]['regional']);
        $this->assertSame('Kumpulan B', $response['data'][0]['group']);
        $this->assertSame('Kuala Lumpur', $response['data'][1]['regional']);
        $this->assertSame([], $response['data'][2]['collection']);
        $this->assertSame(['Malaccaa is not include in the regional state'], $response['error_messages']);

        foreach ($response['data'][0]['collection'] as $holiday) {
            $this->assertContains('Selangor', $holiday['states']);
        }
    }

    /**
     * To test every state belongs to a group and has school holidays
     */
    public function testGetEachStateSchoolHoliday()
    {
        $response = MalaysiaSchoolHoliday::make()->fromState(MalaysiaHoliday::$region_array)->get();

        $this->assertTrue($response['status']);
        $this->assertSame([], $response['error_messages']);
        foreach (MalaysiaHoliday::$region_array as $key => $state) {
            $data = $response['data'][$key];
            $this->assertSame($state, $data['regional']);
            $this->assertContains($data['group'], ['Kumpulan A', 'Kumpulan B'], $state);
            $this->assertNotEmpty($data['collection'], $state);
            foreach ($data['collection'] as $holiday) {
                $this->assertContains($state, $holiday['states']);
            }
        }
    }

    /**
     * To test state excluded from a festive holiday (e.g. Deepavali not for Sarawak)
     */
    public function testFestiveHolidayExcludedState()
    {
        $response = MalaysiaSchoolHoliday::make()->fromState('Sarawak')->get();

        $this->assertTrue($response['status']);
        foreach ($response['data'][0]['collection'] as $holiday) {
            $this->assertContains('Sarawak', $holiday['states']);
        }
    }

    /**
     * To test unpublished year returns error
     */
    public function testUnpublishedYear()
    {
        $response = MalaysiaSchoolHoliday::make()->ofYear(2099)->get();

        $this->assertFalse($response['status']);
        $this->assertArrayHasKey('message', $response);
    }
}
