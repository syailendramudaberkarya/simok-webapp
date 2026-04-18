<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasHierarchicalScope
{
    /**
     * Scope a query to only include records within the user's organizational level and region.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScoped(Builder $query, User $user)
    {
        // If not an admin, they technically shouldn't be here, but let's be safe.
        if (!$user->isAdmin()) {
            return $query->whereRaw('1=0');
        }

        // DPN level has full access
        if ($user->tingkatan === 'DPN') {
            return $query;
        }

        $kantor = $user->kantor;

        // If an admin doesn't have an associated office, their scope is empty (or we can fallback to DPN behavior, but strict is safer)
        if (!$kantor) {
            return $query->whereRaw('1=0');
        }

        return match ($user->tingkatan) {
            'DPD' => $query->where('idpropinsi', $kantor->idpropinsi),
            'DPC' => $query->where('idkabupaten', $kantor->idkabupaten),
            'PR'  => $query->where('idkecamatan', $kantor->idkecamatan),
            'PAR' => $query->where('idkelurahan', $kantor->idkelurahan),
            default => $query->whereRaw('1=0'),
        };
    }
}
