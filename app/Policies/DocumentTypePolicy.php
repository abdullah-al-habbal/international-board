<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, DocumentType $documentType): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, DocumentType $documentType): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, DocumentType $documentType): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, DocumentType $documentType): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, DocumentType $documentType): bool
    {
        return $user->isAdmin();
    }
}
