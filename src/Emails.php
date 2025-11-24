<?php

namespace Souravsspace\Unsent;

/**
 * Client for /emails endpoints.
 */
class Emails
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Emails resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * Send an email (alias for create).
     *
     * @param array $payload Email data
     * @param array $options Optional parameters (e.g. ['idempotencyKey' => '...'])
     * @return array [data, error]
     */
    public function send(array $payload, array $options = []): array
    {
        return $this->create($payload, $options);
    }

    /**
     * Create and send an email.
     *
     * @param array $payload Email data
     * @param array $options Optional parameters (e.g. ['idempotencyKey' => '...'])
     * @return array [data, error]
     */
    public function create(array $payload, array $options = []): array
    {
        $body = $payload;

        // Normalize 'from' field if 'from_' is used
        if (isset($body['from_']) && !isset($body['from'])) {
            $body['from'] = $body['from_'];
            unset($body['from_']);
        }

        // Convert scheduledAt to ISO 8601 if it's a DateTime object
        if (isset($body['scheduledAt']) && $body['scheduledAt'] instanceof \DateTime) {
            $body['scheduledAt'] = $body['scheduledAt']->format(\DateTime::ATOM);
        }

        $headers = [];
        if (isset($options['idempotencyKey'])) {
            $headers['Idempotency-Key'] = $options['idempotencyKey'];
        }

        return $this->unsent->post('/emails', $body, $headers);
    }

    /**
     * Send multiple emails in batch.
     *
     * @param array $emails Array of email payloads
     * @param array $options Optional parameters (e.g. ['idempotencyKey' => '...'])
     * @return array [data, error]
     */
    public function batch(array $emails, array $options = []): array
    {
        $items = [];
        
        foreach ($emails as $email) {
            $item = $email;
            
            // Normalize 'from' field
            if (isset($item['from_']) && !isset($item['from'])) {
                $item['from'] = $item['from_'];
                unset($item['from_']);
            }
            
            // Convert scheduledAt to ISO 8601
            if (isset($item['scheduledAt']) && $item['scheduledAt'] instanceof \DateTime) {
                $item['scheduledAt'] = $item['scheduledAt']->format(\DateTime::ATOM);
            }
            
            $items[] = $item;
        }

        $headers = [];
        if (isset($options['idempotencyKey'])) {
            $headers['Idempotency-Key'] = $options['idempotencyKey'];
        }

        return $this->unsent->post('/emails/batch', $items, $headers);
    }

    /**
     * Get email details.
     *
     * @param string $emailId Email ID
     * @return array [data, error]
     */
    public function get(string $emailId): array
    {
        return $this->unsent->get("/emails/{$emailId}");
    }

    /**
     * Update a scheduled email.
     *
     * @param string $emailId Email ID
     * @param array $payload Update data
     * @return array [data, error]
     */
    public function update(string $emailId, array $payload): array
    {
        $body = $payload;

        // Convert scheduledAt to ISO 8601
        if (isset($body['scheduledAt']) && $body['scheduledAt'] instanceof \DateTime) {
            $body['scheduledAt'] = $body['scheduledAt']->format(\DateTime::ATOM);
        }

        return $this->unsent->patch("/emails/{$emailId}", $body);
    }

    /**
     * Cancel a scheduled email.
     *
     * @param string $emailId Email ID
     * @return array [data, error]
     */
    public function cancel(string $emailId): array
    {
        return $this->unsent->post("/emails/{$emailId}/cancel", []);
    }
}
