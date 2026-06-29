<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'study_program_id',
        'course_code',
        'course_name',
        'credits',
        'is_practicum',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Ambil courses aktif. Otomatis sync dari mata_kuliah jika courses kosong.
     */
    public function getActiveCourses(): array
    {
        $this->syncFromMataKuliah();
        return $this->where('status', 'aktif')
            ->where('deleted_at', null)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }

    /**
     * Sinkronisasi data dari tabel mata_kuliah ke courses
     */
    private function syncFromMataKuliah(): void
    {
        $sql = "INSERT INTO {$this->table} 
                (study_program_id, course_code, course_name, credits, is_practicum, status, created_at, updated_at)
                SELECT 
                    m.prodi_id, 
                    m.kode_mk, 
                    m.nama_mk, 
                    m.sks, 
                    1, 
                    'aktif', 
                    COALESCE(m.created_at, NOW()), 
                    COALESCE(m.updated_at, NOW())
                FROM mata_kuliah m
                LEFT JOIN {$this->table} c ON c.course_code = m.kode_mk
                WHERE c.id IS NULL";

        try {
            $this->db->query($sql);
        } catch (\Exception $e) {
            log_message('error', 'CourseModel::syncFromMataKuliah error: ' . $e->getMessage());
        }
    }
}
