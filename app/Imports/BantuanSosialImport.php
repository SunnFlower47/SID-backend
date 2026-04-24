<?php

namespace App\Imports;

use App\Models\BantuanSosial;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class BantuanSosialImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return new BantuanSosial([
            'program' => $row['program'],
            'jenis' => $row['jenis_bantuan'],
            'deskripsi' => $row['deskripsi'],
            'periode' => $row['periode'],
            'tanggal_mulai' => $row['tanggal_mulai'] ? Carbon::createFromFormat('d/m/Y', $row['tanggal_mulai']) : null,
            'tanggal_selesai' => $row['tanggal_selesai'] ? Carbon::createFromFormat('d/m/Y', $row['tanggal_selesai']) : null,
            'status' => $row['status'] ?? 'aktif',
            'created_by' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'program' => 'required|string|max:255',
            'jenis_bantuan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'periode' => 'nullable|string|max:50',
            'tanggal_mulai' => 'nullable|date_format:d/m/Y',
            'tanggal_selesai' => 'nullable|date_format:d/m/Y|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:aktif,nonaktif',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
