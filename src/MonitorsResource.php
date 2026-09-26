<?php

namespace ExitProbe\Laravel;

class MonitorsResource
{
    public function __construct(protected ExitProbeClient $client) {}

    /** GET /monitors — list monitors, optionally filtered by project_id, protocol, status, search, range. */
    public function list(array $query = []): array
    {
        return $this->client->request('GET', '/monitors', $query);
    }

    /** POST /monitors — create a monitor. See config/exitprobe.php's README for the accepted field set. */
    public function create(array $input): array
    {
        return $this->client->request('POST', '/monitors', body: $input);
    }

    /** POST /projects/{projectId}/monitors — create a monitor inside a specific project. */
    public function createForProject(int $projectId, array $input): array
    {
        return $this->client->request('POST', "/projects/{$projectId}/monitors", body: $input);
    }

    /** GET /monitors/{id} */
    public function get(int $id): array
    {
        return $this->client->request('GET', "/monitors/{$id}");
    }

    /** PUT /monitors/{id} */
    public function update(int $id, array $input): array
    {
        return $this->client->request('PUT', "/monitors/{$id}", body: $input);
    }

    /** DELETE /monitors/{id} — soft-deletes; see restore(). */
    public function delete(int $id): array
    {
        return $this->client->request('DELETE', "/monitors/{$id}");
    }

    /** POST /monitors/{id}/restore */
    public function restore(int $id): array
    {
        return $this->client->request('POST', "/monitors/{$id}/restore");
    }

    /** POST /monitors/{id}/check-now — triggers an on-demand check across the monitor's probes. */
    public function checkNow(int $id): array
    {
        return $this->client->request('POST', "/monitors/{$id}/check-now");
    }

    /** GET /monitors/{id}/check-status?ids=1,2,3 — poll checks dispatched by checkNow(). */
    public function checkStatus(int $id, array $checkIds): array
    {
        return $this->client->request('GET', "/monitors/{$id}/check-status", ['ids' => implode(',', $checkIds)]);
    }

    /** GET /monitors/{id}/checks — paginated raw check history. */
    public function checks(int $id, array $query = []): array
    {
        return $this->client->request('GET', "/monitors/{$id}/checks", $query);
    }

    /** DELETE /monitors/{id}/checks — clears stored check history for this monitor. */
    public function clearChecks(int $id): array
    {
        return $this->client->request('DELETE', "/monitors/{$id}/checks");
    }

    /** GET /monitors/{id}/uptime */
    public function uptime(int $id, array $query = []): array
    {
        return $this->client->request('GET', "/monitors/{$id}/uptime", $query);
    }

    /** GET /monitors/{id}/uptime/regions — per-probe-region uptime breakdown. */
    public function uptimeRegions(int $id, array $query = []): array
    {
        return $this->client->request('GET', "/monitors/{$id}/uptime/regions", $query);
    }

    /** GET /monitors/{id}/analytics — full charting/analytics payload for the monitor detail page. */
    public function analytics(int $id, array $query = []): array
    {
        return $this->client->request('GET', "/monitors/{$id}/analytics", $query);
    }

    /** GET /monitors/{id}/status — lightweight status probe, cheap to poll from third-party integrations. */
    public function status(int $id): array
    {
        return $this->client->request('GET', "/monitors/{$id}/status");
    }
}
