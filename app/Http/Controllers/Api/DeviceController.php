<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\PaginateRequest;
use App\Http\Requests\Device\RegisterDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class DeviceController extends Controller
{
    /** Display a listing of the resource.
     * @response AnonymousResourceCollection<LengthAwarePaginator<DeviceResource>>
     */
    public function index(PaginateRequest $request): ResourceCollection
    {
        /** @var int|null */
        $per_page = $request->per_page;

        return DeviceResource::collection(Device::where('user_id', $request->user()?->id)->paginate($per_page ?? 10));
    }

    /** Display the specified resource. */
    public function show(Device $device): DeviceResource
    {
        return new DeviceResource($device);
    }

    /** Store a newly created resource in storage. */
    public function store(RegisterDeviceRequest $request): DeviceResource
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()?->id;
        $validated['ip_address'] = $request->ip();

        Device::upsert(
            $validated,
            ['identifier'],
            ['user_id', 'fcm_token', 'ip_address', 'platform'],
        );

        $device = Device::where('identifier', $validated['identifier'])->first();

        return new DeviceResource($device);
    }

    /** Remove the specified resource from storage. */
    public function destroy(Device $device): DeviceResource
    {
        $device->delete();

        return new DeviceResource($device);
    }
}
