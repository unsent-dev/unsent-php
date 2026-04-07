<?php

namespace UnsentDev\Unsent;

class ProviderConnections
{
    private $unsent;

    public function __construct(Unsent $unsent)
    {
        $this->unsent = $unsent;
    }

    public function list(): array
    {
        return $this->unsent->get('/provider-connections');
    }

    public function create(array $payload): array
    {
        return $this->unsent->post('/provider-connections', $payload);
    }

    public function delete(string $id): array
    {
        return $this->unsent->delete("/provider-connections/{$id}");
    }
}
