<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'name',
        'username',
        'email',
        'password_hash',
        'status',
        'last_login_at',
    ];

    protected $useTimestamps = true;

    public function findByLogin(string $login)
    {
        return $this->groupStart()
            ->where('email', $login)
            ->orWhere('username', $login)
            ->groupEnd()
            ->where('deleted_at', null)
            ->first();
    }

    public function getUserRoles(int $userId): array
    {
        return $this->db->table('user_roles')
            ->select('roles.id, roles.name, roles.slug')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();
    }

    public function getUserRoleSlugs(int $userId): array
    {
        $roles = $this->getUserRoles($userId);

        return array_column($roles, 'slug');
    }
}