<?php

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
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
    #[DataProvider('yearProvider')]
    public function testGetEachStateSchoolHoliday($year)
    {
        $response = MalaysiaSchoolHoliday::make()->fromState(MalaysiaHoliday::$region_array, $year)->get();

        $this->assertTrue($response['status']);
        $this->assertSame($year, $response['year']);
        $this->assertSame([], $response['error_messages']);
        foreach (MalaysiaHoliday::$region_array as $key => $state) {
            $data = $response['data'][$key];
            $this->assertSame($state, $data['regional']);
            $this->assertContains($data['group'], ['Kumpulan A', 'Kumpulan B'], $state);
            $this->assertNotEmpty($data['collection'], $state);
            foreach ($data['collection'] as $holiday) {
                $this->assertContains($state, $holiday['states']);
                $this->assertSame($year, (int)substr($holiday['start_date'], 0, 4));
            }
        }
    }

    public static function yearProvider(): array
    {
        return [[2025], [2026], [2027]];
    }

    /**
     * To test Malay exclusion note, e.g. "Kecuali Negeri Sarawak"
     */
    public function testFallbackExcludedState()
    {
        $response = MalaysiaSchoolHoliday::make()->fromState(['Sarawak', 'Selangor'], 2027)->get();

        $this->assertTrue($response['status']);
        $this->assertStringContainsString('calendarmalaysia.com', $response['source']);

        $sarawak = array_column($response['data'][0]['collection'], 'start_date');
        $selangor = array_column($response['data'][1]['collection'], 'start_date');
        $this->assertContains('2027-10-28', $sarawak);
        $this->assertNotContains('2027-10-29', $sarawak);
        $this->assertContains('2027-10-29', $selangor);
        $this->assertNotContains('2027-10-28', $selangor);
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
