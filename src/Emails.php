<?php
// @manual

namespace UnsentDev\Unsent;

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
     * @param array $payload Email data (use SendEmailRequest structure from Types.php)
     * @param array $options Optional parameters (e.g. ['idempotencyKey' => '...'])
     * @return array [data, error] - Returns email data on success or error details
     * 
     * @see \UnsentDev\Unsent\Model\SendEmailRequest For request structure
     */
    public function send(array $payload, array $options = []): array
    {
        return $this->create($payload, $options);
    }


    /**
     * Create and send an email.
     *
     * @param array $payload Email data (use SendEmailRequest structure from Types.php)
     *   - 'to': array|string Email recipient(s) (SendEmailRequestTo structure: ['email' => '...', 'name' => '...'])
     *   - 'from': string|null Sender email address
     *   - 'subject': string Email subject
     *   - 'html': string|null HTML email body
     *   - 'text': string|null Plain text email body
     *   - 'replyTo': string|null Reply-to email address
     *   - 'cc': array|null CC recipients
     *   - 'bcc': array|null BCC recipients
     *   - 'attachments': array|null Email attachments
     *   - 'headers': array|null Custom email headers
     *   - 'tags': array|null Email tags for categorization
     *   - 'scheduledAt': string|\DateTime|null Schedule email for later (ISO 8601 format or DateTime object)
     *   - 'templateId': string|null Template ID to use
     *   - 'variables': array|null Template variables
     * @param array $options Optional parameters (e.g. ['idempotencyKey' => '...'])
     * @return array [data, error] - Returns email data on success or error details
     * 
     * @see \UnsentDev\Unsent\Model\SendEmailRequest For complete request structure
     * @see \UnsentDev\Unsent\Model\SendEmailRequestTo For recipient structure
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
     * List emails.
     *
     * @param array $options Optional parameters (page, limit, startDate, endDate, domainId)
     * @return array [data, error]
     */
    public function list(array $options = []): array
    {
        $params = [];
        
        if (isset($options['page'])) {
            $params['page'] = (string) $options['page'];
        }
        if (isset($options['limit'])) {
            $params['limit'] = (string) $options['limit'];
        }
        if (isset($options['startDate'])) {
            $params['startDate'] = $options['startDate'];
        }
        if (isset($options['endDate'])) {
            $params['endDate'] = $options['endDate'];
        }
        
        // Handle domainId as string or array
        if (isset($options['domainId'])) {
            if (is_array($options['domainId'])) {
                $params['domainId'] = $options['domainId'];
            } else {
                $params['domainId'] = [$options['domainId']];
            }
        }
        
        $query = http_build_query($params);
        $path = '/emails' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Get email complaints.
     *
     * @param array $options Optional parameters (page, limit)
     * @return array [data, error]
     */
    public function getComplaints(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/emails/complaints' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Get email bounces.
     *
     * @param array $options Optional parameters (page, limit)
     * @return array [data, error]
     */
    public function getBounces(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/emails/bounces' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Get email unsubscribes.
     *
     * @param array $options Optional parameters (page, limit)
     * @return array [data, error]
     */
    public function getUnsubscribes(array $options = []): array
    {
        $query = http_build_query($options);
        $path = '/emails/unsubscribes' . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
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

    /**
     * Get events for a specific email.
     *
     * @param string $emailId Email ID
     * @param array $options Optional parameters (page, limit)
     * @return array [data, error]
     */
    public function getEvents(string $emailId, array $options = []): array
    {
        $params = [];
        
        if (isset($options['page'])) {
            $params['page'] = (string) $options['page'];
        }
        if (isset($options['limit'])) {
            $params['limit'] = (string) $options['limit'];
        }
        
        $query = http_build_query($params);
        $path = "/emails/{$emailId}/events" . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }
}
