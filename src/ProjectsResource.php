<?php

namespace ExitProbe\Laravel;

class ProjectsResource
{
    public function __construct(protected ExitProbeClient $client) {}

    /** GET /projects */
    public function list(): array
    {
        return $this->client->request('GET', '/projects');
    }

    /** POST /projects */
    public function create(array $input): array
    {
        return $this->client->request('POST', '/projects', body: $input);
    }

    /** GET /projects/{id} */
    public function get(int $id): array
    {
        return $this->client->request('GET', "/projects/{$id}");
    }

    /** PUT /projects/{id} */
    public function update(int $id, array $input): array
    {
        return $this->client->request('PUT', "/projects/{$id}", body: $input);
    }

    /** DELETE /projects/{id} — soft-deletes; see restore(). */
    public function delete(int $id): array
    {
        return $this->client->request('DELETE', "/projects/{$id}");
    }

    /** POST /projects/{id}/restore */
    public function restore(int $id): array
    {
        return $this->client->request('POST', "/projects/{$id}/restore");
    }

    /** POST /projects/{id}/regenerate-key — rotates the project's api_key. */
    public function regenerateKey(int $id): array
    {
        return $this->client->request('POST', "/projects/{$id}/regenerate-key");
    }

    /** DELETE /projects/{id}/checks — clears stored check history for every monitor in the project. */
    public function clearChecks(int $id): array
    {
        return $this->client->request('DELETE', "/projects/{$id}/checks");
    }

    /** GET /projects/{id}/monitors — monitors scoped to this project. */
    public function monitors(int $id, array $query = []): array
    {
        return $this->client->request('GET', "/projects/{$id}/monitors", $query);
    }
}
