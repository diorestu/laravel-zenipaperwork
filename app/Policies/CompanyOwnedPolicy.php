<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompanyOwnedPolicy
{
    public function view(User $user, Model $model): bool
    {
        return $user->isSuperAdmin() || $user->company_id === $model->company_id;
    }

    public function update(User $user, Model $model): bool
    {
        return $this->view($user, $model) && $user->canManageCompany();
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->update($user, $model);
    }
}
