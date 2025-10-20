<?php

namespace App\Contracts;

interface SaaSykitCompatible
{
    // Tenant Management
    public function getCurrentTenant(): ?\App\Models\Tenant;
    public function userBelongsToTenant(\App\Models\User $user, \App\Models\Tenant $tenant): bool;
    
    // Subscription Management
    public function getActiveSubscription(\App\Models\Tenant $tenant): ?\App\Models\Subscription;
    public function canCreateSubscription(\App\Models\Tenant $tenant): bool;
    
    // Team Management
    public function inviteUser(\App\Models\Tenant $tenant, string $email, string $role): \App\Models\Invitation;
    public function removeUser(\App\Models\Tenant $tenant, \App\Models\User $user): bool;
    
    // Events
    public function fireUserJoinedTenant(\App\Models\User $user, \App\Models\Tenant $tenant): void;
    public function fireUserLeftTenant(\App\Models\User $user, \App\Models\Tenant $tenant): void;
}