<?php

namespace App\Services;

use App\Contracts\SaaSykitCompatible;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Subscription;
use Illuminate\Support\Facades\Event;

class SaaSykitPluginService implements SaaSykitCompatible
{
    public function getCurrentTenant(): ?Tenant
    {
        return app('current_tenant');
    }

    public function userBelongsToTenant(User $user, Tenant $tenant): bool
    {
        return $tenant->hasUser($user);
    }

    public function getActiveSubscription(Tenant $tenant): ?Subscription
    {
        return $tenant->subscription;
    }

    public function canCreateSubscription(Tenant $tenant): bool
    {
        return $tenant->is_active;
    }

    public function inviteUser(Tenant $tenant, string $email, string $role = 'member'): Invitation
    {
        if (!$tenant->canAddSeat()) {
            throw new \Exception('Tenant has reached maximum seat limit');
        }

        $invitation = Invitation::create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'role' => $role,
            'token' => \Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        $this->fireUserInvited($invitation);
        
        return $invitation;
    }

    public function removeUser(Tenant $tenant, User $user): bool
    {
        if (!$tenant->hasUser($user)) {
            return false;
        }

        $tenant->users()->detach($user->id);
        $this->fireUserLeftTenant($user, $tenant);
        
        return true;
    }

    public function fireUserJoinedTenant(User $user, Tenant $tenant): void
    {
        Event::dispatch('saasykit.user.joined_tenant', [$user, $tenant]);
    }

    public function fireUserLeftTenant(User $user, Tenant $tenant): void
    {
        Event::dispatch('saasykit.user.left_tenant', [$user, $tenant]);
    }

    public function fireUserInvited(Invitation $invitation): void
    {
        Event::dispatch('saasykit.user.invited', [$invitation]);
    }
}