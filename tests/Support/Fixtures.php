<?php

namespace Tests\Support;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Serves recorded HTML pages so tests do not depend on the live websites.
 *
 * Missing pages fail the test. To record them, run the tests with
 * RECORD_FIXTURES=1; to refresh a page, delete its file and record again.
 */
final class Fixtures
{
    private const DIR = __DIR__.'/../fixtures';

    public static function client(): HttpClientInterface
    {
        return new MockHttpClient(static function (string $method, string $url): MockResponse {
            $file = self::path($url);

            if (! is_file($file)) {
                if (! getenv('RECORD_FIXTURES')) {
                    throw new \RuntimeException("Missing fixture for {$url}. Run the tests with RECORD_FIXTURES=1 to record it.");
                }
                self::record($method, $url, $file);
            }

            $fixture = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

            return new MockResponse($fixture['body'], [
                'http_code' => $fixture['status'],
                'response_headers' => ['content-type: text/html; charset=UTF-8'],
            ]);
        });
    }

    private static function path(string $url): string
    {
        $parts = parse_url($url);
        $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', ($parts['host'] ?? '').($parts['path'] ?? '')), '-');

        // The hash keeps URLs that differ only by case apart on case-insensitive filesystems.
        return self::DIR.'/'.$slug.'-'.substr(md5($url), 0, 8).'.json';
    }

    private static function record(string $method, string $url, string $file): void
    {
        $client = HttpClient::create(['timeout' => 30, 'headers' => ['User-Agent' => 'Mozilla/5.0']]);

        for ($attempt = 1; ; $attempt++) {
            try {
                $response = $client->request($method, $url);
                $status = $response->getStatusCode();
                // Never record transient server errors as fixtures.
                if ($status < 500) {
                    $body = $response->getContent(false);
                    break;
                }
                $error = "HTTP {$status}";
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            if ($attempt >= 5) {
                throw new \RuntimeException("Could not record fixture for {$url} after {$attempt} attempts: {$error}");
            }
            sleep($attempt * 2);
        }

        file_put_contents($file, json_encode(
            ['url' => $url, 'status' => $status, 'body' => $body],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        )."\n");
    }
}
