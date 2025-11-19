<?php

namespace Souravsspace\Unsent;

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
     * Create a contact.
     *
     * @param string $bookId Contact book ID
     * @param array $payload Contact data
     * @return array [data, error]
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
     * Update a contact.
     *
     * @param string $bookId Contact book ID
     * @param string $contactId Contact ID
     * @param array $payload Update data
     * @return array [data, error]
     */
    public function update(string $bookId, string $contactId, array $payload): array
    {
        return $this->unsent->patch("/contactBooks/{$bookId}/contacts/{$contactId}", $payload);
    }

    /**
     * Upsert a contact (create if doesn't exist, update if exists).
     *
     * @param string $bookId Contact book ID
     * @param string $contactId Contact ID
     * @param array $payload Contact data
     * @return array [data, error]
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
