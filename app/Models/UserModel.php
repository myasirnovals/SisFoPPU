<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'full_name',
        'is_active',
        'last_login',
    ];

    public function findForAuthentication(string $identity): ?array
    {
        $builder = $this->db->table('users u');
        $builder->select([
            'u.id',
            'u.username',
            'u.email',
            'u.password',
            'u.full_name',
            'u.is_active',
            'u.last_login',
            'u.created_at',
            'u.updated_at',
            'u.deleted_at',
            'GROUP_CONCAT(DISTINCT r.name ORDER BY ur.id ASC SEPARATOR ",") AS role_names',
            'GROUP_CONCAT(DISTINCT r.label ORDER BY ur.id ASC SEPARATOR "||") AS role_labels',
        ]);
        $builder->join('user_roles ur', 'ur.user_id = u.id', 'left');
        $builder->join('roles r', 'r.id = ur.role_id', 'left');
        $builder->groupStart();
        $builder->where('u.username', $identity);
        $builder->orWhere('u.email', $identity);
        $builder->groupEnd();
        $builder->where('u.deleted_at', null);
        $builder->groupBy('u.id');

        $user = $builder->get()->getRowArray();

        if ($user === null) {
            return null;
        }

        $roles = array_values(array_filter(array_map('trim', explode(',', (string) ($user['role_names'] ?? '')))));
        $roleLabels = array_values(array_filter(array_map('trim', explode('||', (string) ($user['role_labels'] ?? '')))));

        $user['roles'] = $roles;
        $user['role_labels'] = $roleLabels;
        $user['role'] = $roles[0] ?? null;
        $user['role_label'] = $roleLabels[0] ?? null;

        unset($user['role_names']);

        return $user;
    }

    public function touchLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]) !== false;
    }
}
