<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;

class FormPolicy
{
    /** Ro'yxatda forma ko'rinishi (barcha formalarni ko'rish yoki egasi) */
    public function view(User $user, Form $form): bool
    {
        return $user->ownsForm($form) || $user->canViewAllForms();
    }

    public function update(User $user, Form $form): bool
    {
        return $user->ownsForm($form) || $user->canEditAnyForm();
    }

    public function delete(User $user, Form $form): bool
    {
        return $user->ownsForm($form) || $user->canDeleteAnyForm();
    }

    public function viewResponses(User $user, Form $form): bool
    {
        return $user->ownsForm($form) || $user->canViewAnyResponses();
    }

    public function exportResponses(User $user, Form $form): bool
    {
        return $user->ownsForm($form) || $user->canExportAnyResponses();
    }

    public function deleteResponses(User $user, Form $form): bool
    {
        return $user->ownsForm($form) || $user->canDeleteAnyResponses();
    }
}
