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
        'name',
        'username',
        'email',
        'password_hash',
        'status',
        'last_login_at',
    ];

    public function findForAuthentication(string $identity): ?array
    {
        $builder = $this->db->table('users u');
        $builder->select([
            'u.id',
            'u.name',
            'u.name AS full_name',
            'u.username',
            'u.email',
            'u.password_hash',
            'u.status',
            'u.last_login_at',
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
        $user['full_name'] = (string) ($user['full_name'] ?? $user['name'] ?? $user['username'] ?? '');

        unset($user['role_names']);

        return $user;
    }

    public function findByLogin(string $identity): ?array
    {
        return $this->findForAuthentication($identity);
    }

    public function getUserRoleSlugs(int $userId): array
    {
        $builder = $this->db->table('user_roles ur');
        $builder->select('r.name');
        $builder->join('roles r', 'r.id = ur.role_id', 'inner');
        $builder->where('ur.user_id', $userId);
        $builder->orderBy('ur.id', 'ASC');

        $rows = $builder->get()->getResultArray();

        return array_values(array_filter(array_map(static fn (array $row): string => (string) ($row['name'] ?? ''), $rows)));
    }

    public function touchLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login_at' => date('Y-m-d H:i:s')]) !== false;
    }
}
