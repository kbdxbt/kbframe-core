<?php

namespace Modules\Core\Tests\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Core\Tests\Models\Member;
use Modules\Core\Tests\Requests\MemberRequest;
use Modules\Core\Tests\Resources\MemberCollection;
use Modules\Core\Tests\Resources\MemberResource;

class MemberController extends BaseController
{
    public function index(): JsonResponse|JsonResource
    {
        return $this->success(MemberCollection::make(Member::query()->get()));
    }

    public function create(MemberRequest $request): JsonResponse|JsonResource
    {
        $member = Member::query()->create([
            'name' => $request->input('name'),
            'email' => fake()->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        return $this->success(MemberResource::make($member));
    }
}
