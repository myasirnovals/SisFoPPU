<?php

namespace App\Models;

use CodeIgniter\Model;

class MataKuliahModel extends Model
{
    protected $table            = 'mata_kuliah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'kode_mk',
        'nama_mk',
        'sks',
        'semester',   // pastikan kolom ini ada di DB (lihat catatan)
        'prodi_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'kode_mk'  => 'required|max_length[20]|is_unique[mata_kuliah.kode_mk,id,{id}]',
        'nama_mk'  => 'required|max_length[100]',
        'sks'      => 'required|integer|greater_than[0]|less_than[5]',
        'semester' => 'required|in_list[Ganjil,Genap]',
    ];

    protected $validationMessages = [
        'kode_mk' => [
            'is_unique' => 'Kode mata kuliah sudah digunakan.',
        ],
        'sks' => [
            'greater_than' => 'SKS minimal 1.',
            'less_than'    => 'SKS maksimal 4.',
        ],
    ];

    protected $skipValidation = false;

    public function getForDropdown(): array
    {
        return $this->select('id, kode_mk, nama_mk, sks, prodi_id')
            ->orderBy('nama_mk', 'ASC')
            ->findAll();
    }
}
