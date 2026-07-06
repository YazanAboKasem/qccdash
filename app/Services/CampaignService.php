<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Repositories\CampaignRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class CampaignService
{
    public function __construct(
        private CampaignRepository $repository
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getWithStats()
    {
        return $this->repository->getWithStats();
    }

    public function find(int $id): Campaign
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $logo = null): Campaign
    {
        if ($logo) {
            $data['logo_path'] = $logo->store('campaigns/logos', 'public');
        }

        $campaign = $this->repository->create($data);

        ActivityLog::log('created', $campaign, ['title' => $data['title']]);

        return $campaign;
    }

    public function update(Campaign $campaign, array $data, ?UploadedFile $logo = null): Campaign
    {
        if ($logo) {
            // Delete old logo
            if ($campaign->logo_path) {
                Storage::disk('public')->delete($campaign->logo_path);
            }
            $data['logo_path'] = $logo->store('campaigns/logos', 'public');
        }

        $old = $campaign->toArray();
        $campaign = $this->repository->update($campaign, $data);

        ActivityLog::log('updated', $campaign, [
            'old' => array_intersect_key($old, $data),
            'new' => $data,
        ]);

        return $campaign;
    }

    public function delete(Campaign $campaign): void
    {
        ActivityLog::log('deleted', $campaign, ['title' => $campaign->title]);

        if ($campaign->logo_path) {
            Storage::disk('public')->delete($campaign->logo_path);
        }

        $this->repository->delete($campaign);
    }

    public function updateStatus(Campaign $campaign, string $status): Campaign
    {
        $old = $campaign->status;
        $campaign = $this->repository->update($campaign, ['status' => $status]);

        ActivityLog::log('status_changed', $campaign, [
            'old_status' => $old,
            'new_status' => $status,
        ]);

        return $campaign;
    }
}
