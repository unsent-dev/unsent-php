<?php

namespace UnsentDev\Unsent;

/**
 * Client for /api-keys endpoints.
 */
class ApiKeys
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the ApiKeys resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all API keys.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/api-keys');
    }

    /**
     * Create a new API key.
     *
     * @param array $payload API key data (use CreateApiKeyRequest structure from Types.php)
     *   - 'name': string API key name (required)
     *   - 'permission': string Permission level - FULL, SENDING, or READ_ONLY (required)
     * @return array [data, error] - Returns API key data with token on success
     * 
     * @see \UnsentDev\Unsent\Model\CreateApiKeyRequest For request structure
     * @see \UnsentDev\Unsent\Model\CreateApiKey200Response For response (includes token)
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/api-keys', $payload);
    }


    /**
     * Delete an API key.
     *
     * @param string $id API key ID
     * @return array [data, error]
     */
    public function delete(string $id): array
    {
        return $this->unsent->delete("/api-keys/{$id}");
    }
}
