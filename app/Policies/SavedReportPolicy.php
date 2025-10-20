<?php

namespace App\Policies;

use App\Models\SavedReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SavedReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SavedReport $report): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // User can view their own reports
        if ($report->user_id === $user->id) {
            return true;
        }

        // User can view public reports
        if ($report->is_public) {
            return true;
        }

        // User can view reports in their organization
        if ($report->organization_id && $report->organization_id === $user->organization_id) {
            return true;
        }

        // User can view reports in their organizational unit or parent OUs
        if ($report->organizational_unit_id) {
            $userOu = $user->organizationalUnit;
            $reportOu = $report->organizationalUnit;
            
            if ($userOu && $reportOu) {
                // Check if user's OU is same or parent of report's OU
                return $userOu->id === $reportOu->id || 
                       $reportOu->isDescendantOf($userOu);
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SavedReport $report): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $report->user_id === $user->id;
    }

    public function delete(User $user, SavedReport $report): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $report->user_id === $user->id;
    }

    public function restore(User $user, SavedReport $report): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $report->user_id === $user->id;
    }

    public function forceDelete(User $user, SavedReport $report): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $report->user_id === $user->id;
    }
}