<?php

namespace Souravsspace\Unsent;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Unsent API client.
 * 
 * Main client for interacting with the Unsent API.
 */
class Unsent
{
    const DEFAULT_BASE_URL = 'https://api.unsent.dev';

    /**
     * @var string API key
     */
    private $key;

    /**
     * @var string Base URL for API
     */
    private $url;

    /**
     * @var array HTTP headers
     */
    private $headers;

    /**
     * @var bool Whether to raise exceptions on errors
     */
    private $raiseOnError;

    /**
     * @var Client Guzzle HTTP client
     */
    private $client;

    /**
     * @var Emails Email resource client
     */
    public $emails;

    /**
     * @var Contacts Contact resource client
     */
    public $contacts;

    /**
     * @var Campaigns Campaign resource client
     */
    public $campaigns;

    /**
     * @var Domains Domain resource client
     */
    public $domains;

    /**
     * Initialize the Unsent client.
     *
     * @param string|null $key API key (if null, reads from UNSENT_API_KEY env var)
     * @param string|null $url Optional base URL for the API
     * @param bool $raiseOnError Whether to raise exceptions on HTTP errors
     * @param Client|null $client Optional Guzzle client instance
     * @throws \InvalidArgumentException If API key is missing
     */
    public function __construct(
        ?string $key = null,
        ?string $url = null,
        bool $raiseOnError = true,
        ?Client $client = null
    ) {
        $this->key = $key ?? getenv('UNSENT_API_KEY') ?: getenv('UNSENT_API_KEY');
        
        if (!$this->key) {
            throw new \InvalidArgumentException('Missing API key. Pass it to new Unsent("us_123") or set UNSENT_API_KEY environment variable.');
        }

        $base = getenv('UNSENT_BASE_URL') ?: getenv('UNSENT_BASE_URL') ?: self::DEFAULT_BASE_URL;
        if ($url) {
            $base = $url;
        }
        $this->url = rtrim($base, '/') . '/v1';

        $this->headers = [
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
        ];

        $this->raiseOnError = $raiseOnError;
        $this->client = $client ?? new Client();

        // Initialize resource clients
        $this->emails = new Emails($this);
        $this->contacts = new Contacts($this);
        $this->campaigns = new Campaigns($this);
        $this->domains = new Domains($this);
    }

    /**
     * Perform an HTTP request and return [data, error].
     *
     * @param string $method HTTP method
     * @param string $path Request path
     * @param mixed|null $json Request body
     * @return array [data, error]
     * @throws UnsentHTTPError If raiseOnError is true and request fails
     */
    private function request(string $method, string $path, $json = null): array
    {
        $options = [
            'headers' => $this->headers,
        ];

        if ($json !== null) {
            $options['json'] = $json;
        }

        $defaultError = ['code' => 'INTERNAL_SERVER_ERROR', 'message' => 'Unknown error'];

        try {
            $response = $this->client->request($method, $this->url . $path, $options);
            $body = (string) $response->getBody();
            $data = json_decode($body, true);
            
            return [$data, null];
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            
            $error = $defaultError;
            if ($e->getResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $payload = json_decode($body, true);
                if ($payload && isset($payload['error'])) {
                    $error = $payload['error'];
                } else {
                    $error['message'] = $e->getResponse()->getReasonPhrase();
                }
            }

            if ($this->raiseOnError) {
                throw new UnsentHTTPError($statusCode, $error, $method, $path);
            }

            return [null, $error];
        } catch (\Exception $e) {
            $error = $defaultError;
            $error['message'] = $e->getMessage();

            if ($this->raiseOnError) {
                throw new UnsentHTTPError(500, $error, $method, $path);
            }

            return [null, $error];
        }
    }

    /**
     * Perform a POST request.
     *
     * @param string $path Request path
     * @param mixed $body Request body
     * @return array [data, error]
     */
    public function post(string $path, $body): array
    {
        return $this->request('POST', $path, $body);
    }

    /**
     * Perform a GET request.
     *
     * @param string $path Request path
     * @return array [data, error]
     */
    public function get(string $path): array
    {
        return $this->request('GET', $path);
    }

    /**
     * Perform a PUT request.
     *
     * @param string $path Request path
     * @param mixed $body Request body
     * @return array [data, error]
     */
    public function put(string $path, $body): array
    {
        return $this->request('PUT', $path, $body);
    }

    /**
     * Perform a PATCH request.
     *
     * @param string $path Request path
     * @param mixed $body Request body
     * @return array [data, error]
     */
    public function patch(string $path, $body): array
    {
        return $this->request('PATCH', $path, $body);
    }

    /**
     * Perform a DELETE request.
     *
     * @param string $path Request path
     * @param mixed|null $body Optional request body
     * @return array [data, error]
     */
    public function delete(string $path, $body = null): array
    {
        return $this->request('DELETE', $path, $body);
    }
}
