<?php

namespace Souravsspace\Unsent;

/**
 * Client for /contactBooks endpoints.
 */
class ContactBooks
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the ContactBooks resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all contact books.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/contactBooks');
    }

    /**
     * Create a contact book.
     *
     * @param array $payload Contact book data (use CreateContactBookRequest structure from Types.php)
     *   - 'name': string Contact book name (required)
     *   - 'description': string|null Description
     * @return array [data, error] - Returns contact book data on success
     * 
     * @see \Souravsspace\Unsent\Model\CreateContactBookRequest For request structure
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/contactBooks', $payload);
    }

    /**
     * Get contact book details.
     *
     * @param string $id Contact book ID
     * @return array [data, error]
     */
    public function get(string $id): array
    {
        return $this->unsent->get("/contactBooks/{$id}");
    }

    /**
     * Update a contact book.
     *
     * @param string $id Contact book ID
     * @param array $payload Update data (use UpdateContactBookRequest structure from Types.php)
     *   - 'name': string|null Contact book name
     *   - 'description': string|null Description
     * @return array [data, error] - Returns updated contact book data
     * 
     * @see \Souravsspace\Unsent\Model\UpdateContactBookRequest For request structure
     */
    public function update(string $id, array $payload): array
    {
        return $this->unsent->patch("/contactBooks/{$id}", $payload);
    }

    /**
     * Delete a contact book.
     *
     * @param string $id Contact book ID
     * @return array [data, error]
     */
    public function delete(string $id): array
    {
        return $this->unsent->delete("/contactBooks/{$id}");
    }
}
