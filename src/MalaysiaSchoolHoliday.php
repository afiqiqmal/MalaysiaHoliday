<?php

namespace Holiday;

use Holiday\Exception\RegionException;
use Symfony\Component\BrowserKit\HttpBrowser as Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MalaysiaSchoolHoliday
{
    private string $base_url = "https://publicholidays.com.my/school-holidays";
    private string $fallback_url = "https://calendarmalaysia.com/school-holidays-";
    private Client $client;

    private int|null $year = null;
    private string|array|null $region = null;

    public function __construct($client = null)
    {
        $this->client = new Client($client);
    }

    public static function make(?HttpClientInterface $client = null): MalaysiaSchoolHoliday
    {
        return new self($client);
    }

    public function fromAllState($year = null): static
    {
        $this->region = null;
        $this->year = $year;

        return $this;
    }

    public function fromState($region, $year = null): static
    {
        $this->region = $region;
        $this->year = $year;

        return $this;
    }

    public function ofYear($year): static
    {
        $this->year = $year;

        return $this;
    }

    public function get(): array
    {
        $year = (int)($this->year ?? date('Y'));

        $groups = null;
        $source = null;
        $failed = 0;

        // publicholidays.com.my only shows the current academic year, so fall back to
        // calendarmalaysia.com which has a page per year
        foreach (['crawl' => $this->base_url."/", 'crawlFallback' => $this->fallback_url.$year."/"] as $method => $url) {
            try {
                $result = $this->{$method}($year);
                if ($this->publishedYear($result) === $year) {
                    $groups = $result;
                    $source = $url;
                    break;
                }
            } catch (\Throwable) {
                $failed++;
            }
        }

        if ($groups === null) {
            return [
                'status' => false,
                'message' => $failed === 2
                    ? "Error occurred with the results"
                    : "School holidays for {$year} are not available",
            ];
        }

        $final = [];
        $error_messages = [];

        if ($this->region === null) {
            $final = $groups;
        } else {
            foreach ((array)$this->region as $region) {
                try {
                    $state = $this->checkRegional($region);
                    $group = $this->findGroup($groups, $state);

                    $final[] = [
                        'regional' => $state,
                        'group' => $group['group'] ?? null,
                        'collection' => array_values(array_filter(
                            $group['collection'] ?? [],
                            fn ($holiday) => in_array($state, $holiday['states'])
                        )),
                    ];
                } catch (RegionException $regionException) {
                    $error_messages[] = $regionException->getMessage();
                    $final[] = [
                        'regional' => $region,
                        'group' => null,
                        'collection' => [],
                    ];
                }
            }
        }

        return [
            'status' => true,
            'year' => $year,
            'source' => $source,
            'data' => $final,
            'error_messages' => $error_messages,
            'developer' => [
                "name" => "Hafiq",
                "email" => "hafiqiqmal93@gmail.com",
                "github" => "https://github.com/afiqiqmal"
            ]
        ];
    }

    private function crawl(int $year): array
    {
        $crawler = $this->client->request('GET', $this->base_url."/");

        if ($this->client->getResponse()->getStatusCode() !== 200) {
            throw new \RuntimeException("Unable to fetch school holidays");
        }

        // each group: <h2 id="kumpulan-x">, <p>(states)</p>, <table class="phgtable">
        $groups = $crawler->filter('h2[id^="kumpulan-"]')->each(function (Crawler $heading) {
            $states = [];
            $collection = [];

            foreach ($heading->nextAll() as $sibling) {
                if ($sibling->nodeName === 'h2') {
                    break;
                }

                if ($sibling->nodeName === 'p' && empty($states)) {
                    $states = $this->parseStates($sibling->textContent);
                }

                if ($sibling->nodeName === 'table') {
                    $collection = (new Crawler($sibling))->filter('tbody tr')->each(
                        fn (Crawler $row) => $this->parseRow($row, $states)
                    );
                    break;
                }
            }

            return [
                'group' => trim($heading->text()),
                'states' => $states,
                'collection' => array_values(array_filter($collection)),
            ];
        });

        // festive holidays are listed in a separate table with a "States" column
        $crawler->filter('table.tablepress tbody tr')->each(function (Crawler $row) use (&$groups) {
            if ($row->filter('td')->count() < 4) {
                return;
            }

            $states = $this->resolveStates($row->filter('td')->eq(3)->text(), $groups);

            foreach ($groups as &$group) {
                $matched = array_values(array_intersect($group['states'], $states));
                if (!empty($matched) && $holiday = $this->parseRow($row, $matched, 'Festive Holiday')) {
                    $group['collection'][] = $holiday;
                }
            }
        });

        foreach ($groups as &$group) {
            usort($group['collection'], fn ($a, $b) => strcmp($a['start_date'], $b['start_date']));
        }

        return $groups;
    }

    private function crawlFallback(int $year): array
    {
        $crawler = $this->client->request('GET', $this->fallback_url.$year."/");

        if ($this->client->getResponse()->getStatusCode() !== 200) {
            throw new \RuntimeException("Unable to fetch school holidays");
        }

        // each group: <h3>Kumpulan X ...</h3>, then (states) paragraph and table further down the page
        $headings = $crawler->filter('h3')->reduce(
            fn (Crawler $heading) => (bool)preg_match('/^Kumpulan [A-Z]\b/', trim($heading->text()))
        );

        $groups = $headings->each(function (Crawler $heading) {
            $node = $heading->getNode(0);
            $xpath = new \DOMXPath($node->ownerDocument);
            $paragraph = $xpath->query('following::p[1]', $node)->item(0);
            $table = $xpath->query('following::table[1]', $node)->item(0);

            preg_match('/^Kumpulan [A-Z]\b/', trim($heading->text()), $match);
            $states = $paragraph ? $this->parseStates($paragraph->textContent) : [];

            $collection = $table ? (new Crawler($table))->filter('tbody tr')->each(
                fn (Crawler $row) => $this->parseFallbackRow($row, $match[0], $states)
            ) : [];

            return [
                'group' => $match[0],
                'states' => $states,
                'collection' => array_values(array_filter($collection)),
            ];
        });

        foreach ($groups as &$group) {
            usort($group['collection'], fn ($a, $b) => strcmp($a['start_date'], $b['start_date']));
        }

        return $groups;
    }

    /**
     * Finishes column holds an end date, nothing (single day) or a note such as "Kedah Only"
     */
    private function parseFallbackRow(Crawler $row, string $group, array $states): ?array
    {
        $cells = $row->filter('td');
        if ($cells->count() < 3) {
            return null;
        }

        $start = $this->parseDate($cells->eq(1)->text());
        if (!$start) {
            return null;
        }

        $finishes = trim($cells->eq(2)->text());
        $end = $this->parseDate($finishes) ?? $start;

        if ($finishes !== '' && $end === $start) {
            $states = array_values(array_intersect(
                $states,
                $this->resolveStates($finishes, [['group' => $group, 'states' => $states]], $states)
            ));
        }

        $name = trim($cells->eq(0)->text());

        return [
            'name' => $name,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_day' => $start->format('l'),
            'end_day' => $end->format('l'),
            'total_days' => $start->diff($end)->days + 1,
            'is_holiday' => true,
            'type' => stripos($name, 'PERAYAAN') !== false ? 'Festive Holiday' : 'Term Holiday',
            'states' => $states,
        ];
    }

    private function parseRow(Crawler $row, array $states, string $type = 'Term Holiday'): ?array
    {
        $cells = $row->filter('td');
        if ($cells->count() < 3) {
            return null;
        }

        $start = $this->parseDate($cells->eq(1)->text());
        if (!$start) {
            return null;
        }

        $end = $this->parseDate($cells->eq(2)->text());
        $name = trim($cells->eq(0)->text());
        $is_holiday = $end !== null;

        return [
            'name' => $name,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end?->format('Y-m-d'),
            'start_day' => $start->format('l'),
            'end_day' => $end?->format('l'),
            'total_days' => $is_holiday ? $start->diff($end)->days + 1 : 0,
            'is_holiday' => $is_holiday,
            'type' => $is_holiday ? $type : 'School Session',
            'states' => $states,
        ];
    }

    private function parseDate(string $text): ?\DateTimeImmutable
    {
        if (!preg_match('/\d{1,2} [A-Za-z]{3,9} \d{4}/', $text, $match)) {
            return null;
        }

        return \DateTimeImmutable::createFromFormat('!j M Y', $match[0])
            ?: \DateTimeImmutable::createFromFormat('!j F Y', $match[0])
            ?: null;
    }

    private function parseStates(string $text): array
    {
        $states = [];
        foreach (explode(',', trim($text, " ()\n\r\t")) as $name) {
            try {
                $states[] = $this->checkRegional(trim($name));
            } catch (RegionException) {
                // ignore unknown names
            }
        }

        return $states;
    }

    /**
     * Resolve text such as "Kumpulan A", "Sarawak", "All States in Kumpulan B except Sarawak"
     * or "Kecuali Negeri Sarawak". $default is used when nothing is named before "except"
     */
    private function resolveStates(string $text, array $groups, array $default = []): array
    {
        $parts = preg_split('/\b(except|kecuali)\b/i', $text, 2);

        return array_values(array_diff(
            $this->statesIn($parts[0], $groups) ?: $default,
            $this->statesIn($parts[1] ?? '', $groups)
        ));
    }

    private function statesIn(string $text, array $groups): array
    {
        $states = [];

        foreach ($groups as $group) {
            if (stripos($text, $group['group']) !== false) {
                $states = array_merge($states, $group['states']);
            }
        }

        $names = array_merge(MalaysiaHoliday::$region_array, array_keys(MalaysiaHoliday::$related_region));
        foreach ($names as $name) {
            if (preg_match('/\b'.preg_quote($name, '/').'\b/i', $text)) {
                $states[] = $this->checkRegional($name);
            }
        }

        return array_values(array_unique($states));
    }

    private function publishedYear(array $groups): ?int
    {
        foreach ($groups as $group) {
            foreach ($group['collection'] as $holiday) {
                return (int)substr($holiday['start_date'], 0, 4);
            }
        }

        return null;
    }

    private function findGroup(array $groups, string $state): ?array
    {
        foreach ($groups as $group) {
            if (in_array($state, $group['states'])) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @throws RegionException
     */
    private function checkRegional($regional): string
    {
        foreach (MalaysiaHoliday::$related_region as $index => $state) {
            if (strtolower($index) == strtolower($regional)) {
                $regional = $state;
                break;
            }
        }

        foreach (MalaysiaHoliday::$region_array as $state) {
            if (strtolower($state) == strtolower($regional)) {
                return $state;
            }
        }

        throw new RegionException($regional . " is not include in the regional state");
    }
}
