<?php

namespace UnsentDev\Unsent;

/**
 * Client for /contactBooks endpoints.
 */
class Contacts
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Contacts resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List contacts in a contact book.
     *
     * @param string $bookId Contact book ID
     * @param array $options Optional parameters (emails, page, limit, ids)
     * @return array [data, error]
     */
    public function list(string $bookId, array $options = []): array
    {
        $query = http_build_query($options);
        $path = "/contactBooks/{$bookId}/contacts" . ($query ? '?' . $query : '');
        return $this->unsent->get($path);
    }

    /**
     * Create a contact in a contact book.
     *
     * @param string $bookId Contact book ID
     * @param array $payload Contact data (use CreateContactRequest structure from Types.php)
     *   - 'email': string Email address (required)
     *   - 'name': string|null Contact name
     *   - 'metadata': array|null Custom metadata fields
     * @return array [data, error] - Returns contact data on success
     * 
     * @see \UnsentDev\Unsent\Model\CreateContactRequest For request structure
     */
    public function create(string $bookId, array $payload): array
    {
        return $this->unsent->post("/contactBooks/{$bookId}/contacts", $payload);
    }

    /**
     * Get contact details.
     *
     * @param string $bookId Contact book ID
     * @param string $contactId Contact ID
     * @return array [data, error]
     */
    public function get(string $bookId, string $contactId): array
    {
        return $this->unsent->get("/contactBooks/{$bookId}/contacts/{$contactId}");
    }

    /**
     * Update a contact (partial update).
     *
     * @param string $bookId Contact book ID
     * @param string $contactId Contact ID
     * @param array $payload Update data (use UpdateContactRequest structure from Types.php)
     *   - 'email': string|null Email address
     *   - 'name': string|null Contact name
     *   - 'metadata': array|null Custom metadata fields
     * @return array [data, error] - Returns updated contact data
     * 
     * @see \UnsentDev\Unsent\Model\UpdateContactRequest For request structure
     */
    public function update(string $bookId, string $contactId, array $payload): array
    {
        return $this->unsent->patch("/contactBooks/{$bookId}/contacts/{$contactId}", $payload);
    }

    /**
     * Upsert a contact (create if doesn't exist, update if exists).
     *
     * @param string $bookId Contact book ID
     * @param string $contactId Contact ID (will be created with this ID if doesn't exist)
     * @param array $payload Contact data (use CreateContactRequest structure from Types.php)
     *   - 'email': string Email address (required)
     *   - 'name': string|null Contact name
     *   - 'metadata': array|null Custom metadata fields
     * @return array [data, error] - Returns contact data (created or updated)
     * 
     * @see \UnsentDev\Unsent\Model\CreateContactRequest For request structure
     */
    public function upsert(string $bookId, string $contactId, array $payload): array
    {
        return $this->unsent->put("/contactBooks/{$bookId}/contacts/{$contactId}", $payload);
    }

    /**
     * Delete a contact.
     *
     * @param string $bookId Contact book ID
     * @param string $contactId Contact ID
     * @return array [data, error]
     */
    public function delete(string $bookId, string $contactId): array
    {
        return $this->unsent->delete("/contactBooks/{$bookId}/contacts/{$contactId}");
    }
}
