<?php

namespace App\Policies;

use App\Models\AiProviderSetting;
use App\Models\User;

class AiProviderSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, AiProviderSetting $aiProviderSetting): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, AiProviderSetting $aiProviderSetting): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, AiProviderSetting $aiProviderSetting): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, AiProviderSetting $aiProviderSetting): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, AiProviderSetting $aiProviderSetting): bool
    {
        return $user->isSuperAdmin();
    }
}
