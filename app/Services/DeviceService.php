<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Device;
use App\Repositories\DeviceRepository;

class DeviceService
{
    public function __construct(
        private DeviceRepository $repository,
    ) {}

    public function list(array $filters = [])
    {
        return $this->repository->paginate(15, $filters);
    }

    public function getWithSyncStatus()
    {
        return $this->repository->getWithSyncStatus();
    }

    public function find(int $id): Device
    {
        return $this->repository->findOrFail($id);
    }

    public function register(array $data): Device
    {
        $data['api_token'] = Device::generateToken();

        $device = $this->repository->create($data);

        ActivityLog::log('registered', $device);

        return $device;
    }

    public function update(Device $device, array $data): Device
    {
        $device = $this->repository->update($device, $data);
        ActivityLog::log('updated', $device);
        return $device;
    }

    public function regenerateToken(Device $device): Device
    {
        $device = $this->repository->update($device, [
            'api_token' => Device::generateToken(),
        ]);

        ActivityLog::log('token_regenerated', $device);

        return $device;
    }

    public function authenticate(string $token): ?Device
    {
        return $this->repository->findByToken($token);
    }

    public function delete(Device $device): void
    {
        ActivityLog::log('deleted', $device, ['name' => $device->name]);
        $this->repository->delete($device);
    }

    public function updateStatus(Device $device, string $status): Device
    {
        return $this->repository->update($device, ['status' => $status]);
    }
}
