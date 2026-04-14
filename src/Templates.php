<?php
// @manual

namespace UnsentDev\Unsent;

/**
 * Client for /templates endpoints.
 */
class Templates
{
    /**
     * @var Unsent Unsent client instance
     */
    private $unsent;

    /**
     * Initialize the Templates resource client.
     *
     * @param Unsent $unsent Unsent client instance
     */
    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    /**
     * List all templates.
     *
     * @return array [data, error]
     */
    public function list(): array
    {
        return $this->unsent->get('/templates');
    }

    /**
     * Create an email template.
     *
     * @param array $payload Template data (use CreateTemplateRequest structure from Types.php)
     *   - 'name': string Template name (required)
     *   - 'subject': string|null Email subject line
     *   - 'html': string|null HTML content with template variables
     *   - 'text': string|null Plain text content
     * @return array [data, error] - Returns template data on success
     * 
     * @see \UnsentDev\Unsent\Model\CreateTemplateRequest For request structure
     */
    public function create(array $payload): array
    {
        return $this->unsent->post('/templates', $payload);
    }

    /**
     * Get template details.
     *
     * @param string $id Template ID
     * @return array [data, error]
     */
    public function get(string $id): array
    {
        return $this->unsent->get("/templates/{$id}");
    }

    /**
     * Update an email template.
     *
     * @param string $id Template ID
     * @param array $payload Update data (use UpdateTemplateRequest structure from Types.php)
     *   - 'name': string|null Template name
     *   - 'subject': string|null Email subject line
     *   - 'html': string|null HTML content
     *   - 'text': string|null Plain text content
     * @return array [data, error] - Returns updated template data
     * 
     * @see \UnsentDev\Unsent\Model\UpdateTemplateRequest For request structure
     */
    public function update(string $id, array $payload): array
    {
        return $this->unsent->patch("/templates/{$id}", $payload);
    }

    /**
     * Delete a template.
     *
     * @param string $id Template ID
     * @return array [data, error]
     */
    public function delete(string $id): array
    {
        return $this->unsent->delete("/templates/{$id}");
    }
}
