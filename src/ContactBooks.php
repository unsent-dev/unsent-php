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
     * @param array $payload Contact book data
     * @return array [data, error]
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
     * @param array $payload Update data
     * @return array [data, error]
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
