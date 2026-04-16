<?php

namespace App\DataTransferObjects;

class KtpData
{
    /**
     * @param  array<string, string>  $raw
     */
    public function __construct(
        public readonly ?string $nik = null,
        public readonly ?string $nama = null,
        public readonly ?string $tempatLahir = null,
        public readonly ?string $tanggalLahir = null,
        public readonly ?string $jenisKelamin = null,
        public readonly ?string $agama = null,
        public readonly ?string $alamat = null,
        public readonly ?string $rtRw = null,
        public readonly ?string $kelurahan = null,
        public readonly ?string $kecamatan = null,
        public readonly array $raw = [],
    ) {}

    public function isEmpty(): bool
    {
        return $this->nik === null
            && $this->nama === null
            && $this->tempatLahir === null
            && $this->tanggalLahir === null;
    }

    /**
     * Count how many fields were successfully extracted.
     */
    public function extractedFieldCount(): int
    {
        $count = 0;

        foreach (['nik', 'nama', 'tempatLahir', 'tanggalLahir', 'jenisKelamin', 'agama', 'alamat', 'rtRw', 'kelurahan', 'kecamatan'] as $field) {
            if ($this->{$field} !== null) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Build from a raw parsed array (e.g. from KtpTextParser).
     *
     * @param  array<string, string>  $data
     */
    public static function fromParsedArray(array $data): self
    {
        return new self(
            nik: $data['nik'] ?? null,
            nama: $data['nama'] ?? null,
            tempatLahir: $data['tempat_lahir'] ?? null,
            tanggalLahir: $data['tanggal_lahir'] ?? null,
            jenisKelamin: $data['jenis_kelamin'] ?? null,
            agama: $data['agama'] ?? null,
            alamat: $data['alamat'] ?? null,
            rtRw: $data['rt_rw'] ?? null,
            kelurahan: $data['kelurahan'] ?? null,
            kecamatan: $data['kecamatan'] ?? null,
            raw: $data,
        );
    }
}
