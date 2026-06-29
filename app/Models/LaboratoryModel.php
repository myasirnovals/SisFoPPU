<?php

namespace App\Models;

use CodeIgniter\Model;

class LaboratoryModel extends Model
{
    protected $table            = 'laboratories';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'room_code',
        'room_name',
        'location',
        'capacity',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $useSoftDeletes   = true;

    /**
     * Get active laboratories
     */
    public function getActive(): array
    {
        return $this->where('status', 'aktif')
            ->where('deleted_at', null)
            ->orderBy('room_name', 'ASC')
            ->findAll();
    }
}
