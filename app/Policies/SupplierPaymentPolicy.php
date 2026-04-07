<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SupplierPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_supplier::payment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupplierPayment $supplierPayment): bool
    {
        return $user->can('view_supplier::payment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_supplier::payment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupplierPayment $supplierPayment): bool
    {
        return $user->can('update_supplier::payment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupplierPayment $supplierPayment): bool
    {
        return $user->can('delete_supplier::payment');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_supplier::payment');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, SupplierPayment $supplierPayment): bool
    {
        return $user->can('force_delete_supplier::payment');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_supplier::payment');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, SupplierPayment $supplierPayment): bool
    {
        return $user->can('restore_supplier::payment');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_supplier::payment');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, SupplierPayment $supplierPayment): bool
    {
        return $user->can('replicate_supplier::payment');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_supplier::payment');
    }
}
