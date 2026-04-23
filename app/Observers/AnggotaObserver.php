<?php

namespace App\Observers;

use App\Models\Anggota;
use App\Services\SatuBarisanApiService;
use Illuminate\Support\Facades\Log;

class AnggotaObserver
{
    protected SatuBarisanApiService $apiService;

    public function __construct(SatuBarisanApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    protected function mapData(Anggota $anggota): array
    {
        $statusMap = [
            'menunggu' => 'Menunggu',
            'disetujui' => 'Aktif',
            'ditolak' => 'Pasif',
        ];

        $agamaMap = [
            'Islam' => 1,
            'Kristen' => 2,
            'Katolik' => 3,
            'Hindu' => 4,
            'Buddha' => 5,
            'Konghucu' => 6,
        ];

        $statusKawinMap = [
            'Belum Kawin' => 1,
            'Kawin' => 2,
            'Cerai Hidup' => 3,
            'Cerai Mati' => 4,
        ];

        $golDarahMap = [
            'A' => 1,
            'B' => 2,
            'AB' => 3,
            'O' => 4,
        ];

        return [
            'nik' => $anggota->nik,
            'noanggota' => $anggota->nomor_anggota,
            'nama' => $anggota->nama_lengkap,
            'tempatlahir' => $anggota->tempat_lahir,
            'tgllahir' => $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('Y-m-d') : null,
            'alamat' => $anggota->alamat,
            'email' => $anggota->user ? $anggota->user->email : null,
            'telepon' => $anggota->no_telepon,
            'idjeniskelamin' => $anggota->jenis_kelamin === 'Laki-laki' ? 1 : ($anggota->jenis_kelamin === 'Perempuan' ? 2 : null),
            'idagama' => $agamaMap[$anggota->agama] ?? null,
            'idstatuskawin' => $statusKawinMap[$anggota->status_perkawinan] ?? null,
            'idgoldarah' => $golDarahMap[strtoupper($anggota->golongan_darah ?? '')] ?? null,
            'idkewarganegaraan' => $anggota->kewarganegaraan === 'WNI' ? 1 : ($anggota->kewarganegaraan === 'WNA' ? 2 : null),
            'idpropinsi' => $anggota->idpropinsi,
            'idkabupaten' => $anggota->idkabupaten,
            'idkecamatan' => $anggota->idkecamatan,
            'idkelurahan' => $anggota->idkelurahan,
            'status' => $statusMap[$anggota->status] ?? 'Menunggu',
        ];
    }

    public function created(Anggota $anggota): void
    {
        try {
            $data = $this->mapData($anggota);
            $this->apiService->createAnggota($data);
        } catch (\Exception $e) {
            Log::error('Error calling SatuBarisan create API: '.$e->getMessage());
        }
    }

    public function updated(Anggota $anggota): void
    {
        try {
            $data = $this->mapData($anggota);
            // Use NIK as the primary key for the API
            $key = $anggota->nik ?? $anggota->nomor_anggota;
            if ($key) {
                $this->apiService->updateAnggota($key, $data);
            }
        } catch (\Exception $e) {
            Log::error('Error calling SatuBarisan update API: '.$e->getMessage());
        }
    }

    public function deleted(Anggota $anggota): void
    {
        try {
            $key = $anggota->nik ?? $anggota->nomor_anggota;
            if ($key) {
                $this->apiService->deleteAnggota($key);
            }
        } catch (\Exception $e) {
            Log::error('Error calling SatuBarisan delete API: '.$e->getMessage());
        }
    }
}
