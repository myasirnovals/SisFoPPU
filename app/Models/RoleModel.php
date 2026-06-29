<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'code',
        'description',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;

    /**
     * Get role by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }

    /**
     * Get role ID by code
     */
    public function getIdByCode(string $code): ?int
    {
        $role = $this->getByCode($code);
        return $role ? (int)$role['id'] : null;
    }
}
