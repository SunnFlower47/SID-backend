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
            'nama_program' => $row['program'],
            'jenis_bantuan' => $row['jenis_bantuan'],
            'deskripsi' => $row['deskripsi'],
            'periode' => $row['periode'],
            'tanggal_mulai' => !empty($row['tanggal_mulai']) ? Carbon::parse($row['tanggal_mulai']) : null,
            'tanggal_selesai' => !empty($row['tanggal_selesai']) ? Carbon::parse($row['tanggal_selesai']) : null,
            'status' => $row['status'] ?? 'aktif',
            'kriteria_penerima' => isset($row['kriteria_penerima']) ? explode(',', $row['kriteria_penerima']) : ['Umum'],
            'sumber_dana' => $row['sumber_dana'] ?? 'Dana Desa',
            'nilai_bantuan' => $row['nilai_bantuan'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'program' => 'required|string|max:255',
            'jenis_bantuan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'periode' => 'nullable|string|max:50',
            'tanggal_mulai' => 'nullable|date_format:Y-m-d',
            'tanggal_selesai' => 'nullable|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'status' => 'nullable|in:aktif,nonaktif',
            'sumber_dana' => 'nullable|string|max:255',
            'nilai_bantuan' => 'nullable|numeric|min:0',
            'kriteria_penerima' => 'nullable|string',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        if (isset($data['periode'])) {
            $data['periode'] = (string) $data['periode'];
        }
        
        foreach (['tanggal_mulai', 'tanggal_selesai'] as $field) {
            if (!empty($data[$field])) {
                if (is_numeric($data[$field])) {
                    $data[$field] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[$field])->format('Y-m-d');
                } elseif (strpos($data[$field], '/') !== false) {
                    try {
                        $data[$field] = Carbon::createFromFormat('d/m/Y', $data[$field])->format('Y-m-d');
                    } catch (\Exception $e) {
                        // ignore and let validation fail
                    }
                }
            }
        }
        
        return $data;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
