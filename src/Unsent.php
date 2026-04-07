<?php

namespace UnsentDev\Unsent;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Unsent API client.
 * 
 * Main client for interacting with the Unsent API.
 * 
 * @property-read Emails $emails Email operations client
 * @property-read Contacts $contacts Contact management client
 * @property-read Campaigns $campaigns Campaign management client
 * @property-read Domains $domains Domain management client
 * @property-read Analytics $analytics Analytics client
 * @property-read ApiKeys $apiKeys API key management client
 * @property-read ContactBooks $contactBooks Contact book management client
 * @property-read Settings $settings Team settings client
 * @property-read Suppressions $suppressions Suppression list management client
 * @property-read Templates $templates Email template management client
 * @property-read Webhooks $webhooks Webhook management client
 * @property-read System $system System operations client (health, version)
 * @property-read Events $events Event tracking client
 * @property-read Metrics $metrics Performance metrics client
 * @property-read Stats $stats Statistics client
 * @property-read Activity $activity Activity feed client
 * @property-read Teams $teams Team management client
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
     * @var Analytics Analytics resource client
     */
    public $analytics;

    /**
     * @var ApiKeys API Keys resource client
     */
    public $apiKeys;

    /**
     * @var ContactBooks Contact Books resource client
     */
    public $contactBooks;

    /**
     * @var Settings Settings resource client
     */
    public $settings;

    /**
     * @var Suppressions Suppressions resource client
     */
    public $suppressions;

    /**
     * @var Templates Templates resource client
     */
    public $templates;

    /**
     * @var Webhooks Webhooks resource client
     */
    public $webhooks;

    /**
     * @var System System resource client
     */
    public $system;

    /**
     * @var Events Events resource client
     */
    public $events;

    /**
     * @var Metrics Metrics resource client
     */
    public $metrics;

    /**
     * @var Stats Stats resource client
     */
    public $stats;

    /**
     * @var Activity Activity resource client  
     */
    public $activity;

    /**
     * @var Teams Teams resource client
     */
    public $teams;

    /**
     * @var ProviderConnections ProviderConnections resource client
     */
    public $providerConnections;

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
            throw new \InvalidArgumentException('Missing API key. Pass it to new Unsent("un_xxxx") or set UNSENT_API_KEY environment variable.');
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
        $this->analytics = new Analytics($this);
        $this->apiKeys = new ApiKeys($this);
        $this->contactBooks = new ContactBooks($this);
        $this->settings = new Settings($this);
        $this->suppressions = new Suppressions($this);
        $this->templates = new Templates($this);
        $this->webhooks = new Webhooks($this);
        $this->system = new System($this);
        $this->events = new Events($this);
        $this->metrics = new Metrics($this);
        $this->stats = new Stats($this);
        $this->activity = new Activity($this);
        $this->teams = new Teams($this);
        $this->providerConnections = new ProviderConnections($this);
    }

    /**
     * Perform an HTTP request and return [data, error].
     *
     * @param string $method HTTP method
     * @param string $path Request path
     * @param mixed|null $json Request body
     * @param array $headers Optional custom headers
     * @return array [data, error]
     * @throws UnsentHTTPError If raiseOnError is true and request fails
     */
    private function request(string $method, string $path, $json = null, array $headers = []): array
    {
        $options = [
            'headers' => array_merge($this->headers, $headers),
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
     * @param array $headers Optional custom headers
     * @return array [data, error]
     */
    public function post(string $path, $body, array $headers = []): array
    {
        return $this->request('POST', $path, $body, $headers);
    }

    /**
     * Perform a GET request.
     *
     * @param string $path Request path
     * @param array $headers Optional custom headers
     * @return array [data, error]
     */
    public function get(string $path, array $headers = []): array
    {
        return $this->request('GET', $path, null, $headers);
    }

    /**
     * Perform a PUT request.
     *
     * @param string $path Request path
     * @param mixed $body Request body
     * @param array $headers Optional custom headers
     * @return array [data, error]
     */
    public function put(string $path, $body, array $headers = []): array
    {
        return $this->request('PUT', $path, $body, $headers);
    }

    /**
     * Perform a PATCH request.
     *
     * @param string $path Request path
     * @param mixed $body Request body
     * @param array $headers Optional custom headers
     * @return array [data, error]
     */
    public function patch(string $path, $body, array $headers = []): array
    {
        return $this->request('PATCH', $path, $body, $headers);
    }

    /**
     * Perform a DELETE request.
     *
     * @param string $path Request path
     * @param mixed|null $body Optional request body
     * @param array $headers Optional custom headers
     * @return array [data, error]
     */
    public function delete(string $path, $body = null, array $headers = []): array
    {
        return $this->request('DELETE', $path, $body, $headers);
    }
}
