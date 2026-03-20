<?php

namespace UnsentDev\Unsent;

/**
 * Client for /suppressions endpoints.
 */
class Suppressions
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Suppressions resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List suppressions.
     *
     * @param array $options Optional parameters (page, limit, search, reason)
     * @return array [data, error]
     */
    public function list(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/suppressions' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Add a suppression (block an email address).
     *
     * @param array $payload Suppression data (use AddSuppressionRequest structure from Types.php)
     *   - 'email': string Email address to suppress (required)
     *   - 'reason': string Reason for suppression - HARD_BOUNCE, COMPLAINT, MANUAL, or UNSUBSCRIBE (required)
     *   - 'source': string|null Source of suppression
     * @return array [data, error] - Returns suppression data on success
     * 
     * @see \UnsentDev\Unsent\Model\AddSuppressionRequest For request structure
     * @see \UnsentDev\Unsent\Model\AddSuppression200Response For response structure
     */
    public function add(array $payload): array
    {
        return $this->unsent->post('/suppressions', $payload);
    }


    /**
     * Delete a suppression by email.
     *
     * @param string $email Email address
     * @return array [data, error]
     */
    public function delete(string $email): array
    {
        return $this->unsent->delete("/suppressions/email/{$email}");
    }
}
