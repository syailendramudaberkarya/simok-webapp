<?php

namespace App\Models;

use Database\Factories\AnggotaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
    'jenis_kelamin', 'agama', 'status_perkawinan', 'pekerjaan', 'kewarganegaraan', 'golongan_darah', 'alamat', 'rt_rw', 'kelurahan', 'kecamatan',
    'idpropinsi', 'idkabupaten', 'idkecamatan', 'idkelurahan',
    'no_telepon', 'foto_ktp_path', 'foto_wajah_path', 'nomor_anggota',
    'status', 'alasan_tolak', 'approved_at', 'approved_by', 'tingkatan',
])]
class Anggota extends Model
{
    /** @use HasFactory<AnggotaFactory> */
    use HasFactory, \App\Traits\HasHierarchicalScope;

    /**
     * The table associated with the model.
     */
    protected $table = 'anggota';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'approved_at' => 'datetime',
            'nik' => 'encrypted',
        ];
    }

    /**
     * Get the user that owns the anggota profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved this anggota.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function propinsi(): BelongsTo
    {
        return $this->belongsTo(Propinsi::class, 'idpropinsi');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'idkabupaten');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'idkecamatan');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'idkelurahan');
    }

    /**
     * Get the kartu anggota for this member.
     */
    public function kartuAnggota(): HasMany
    {
        return $this->hasMany(KartuAnggota::class);
    }

    /**
     * Check if the anggota is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    /**
     * Check if the anggota is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'menunggu';
    }

    /**
     * Check if the anggota is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Signed URL handlers for sensitive photos (Valid for 30 minutes)
     */
    public function getFotoWajahSignedUrlAttribute()
    {
        if (!$this->foto_wajah_path) return null;
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'file.private', now()->addMinutes(30), ['path' => $this->foto_wajah_path]
        );
    }

    public function getFotoKtpSignedUrlAttribute()
    {
        if (!$this->foto_ktp_path) return null;
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'file.private', now()->addMinutes(30), ['path' => $this->foto_ktp_path]
        );
    }

    /**
     * Get the expiration date.
     * Prefers the date from the latest card, falls back to +5 years from approval.
     */
    public function getExpiredAtAttribute(): ?\Illuminate\Support\Carbon
    {
        $latestCard = $this->kartuAnggota()->latest()->first();
        
        if ($latestCard && $latestCard->berlaku_hingga) {
            return \Illuminate\Support\Carbon::parse($latestCard->berlaku_hingga);
        }

        return $this->approved_at?->copy()->addYears(5);
    }
}
