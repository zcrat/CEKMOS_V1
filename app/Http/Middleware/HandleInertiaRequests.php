<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            'auth' => [
                'user' => [
                    'id' => $user ? $user->id : null,
                    'name' => $user ? $user->name : null,
                    'email' => $user ? $user->email : null,
                    'avatar' => $user ? $user->profile_photo_url : null,
                    'email_verified_at' => $user ? $user->email_verified_at : null,
                    'created_at' => $user ? $user->created_at : null,
                    'updated_at' => $user ? $user->updated_at : null,
                    
                ],

                'roles' => $user
                    ? ($user->hasRole('Super Admin')
                        ? ['Super Admin']
                        : $user->getRoleNames()->toArray()
                    )
                    : [],

                'permissions' => $user
                    ? ($user->hasRole('Super Admin')
                        ? ['*']
                        : $user->getAllPermissions()->pluck('name')->toArray()
                    )
                    : [],
            ],
        ];
    }
}
