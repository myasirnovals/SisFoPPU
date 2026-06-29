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
        'login_identifier',
        'identifier_type',
        'full_name',
        'email',
        'phone',
        'password_hash',
        'is_active',
        'last_login_at',
    ];

    public function findForAuthentication(string $identity): ?array
    {
        $builder = $this->db->table('users u');
        $builder->select([
            'u.id',
            'u.login_identifier',
            'u.identifier_type',
            'u.full_name',
            'u.email',
            'u.phone',
            'u.password_hash',
            'u.is_active',
            'u.last_login_at',
            'u.created_at',
            'u.updated_at',
            'u.deleted_at',
            'GROUP_CONCAT(DISTINCT r.code ORDER BY ur.id ASC SEPARATOR ",") AS role_codes',
            'GROUP_CONCAT(DISTINCT r.name ORDER BY ur.id ASC SEPARATOR "||") AS role_names',
        ]);
        $builder->join('user_roles ur', 'ur.user_id = u.id', 'left');
        $builder->join('roles r', 'r.id = ur.role_id', 'left');
        $builder->where('u.login_identifier', $identity);
        $builder->where('u.deleted_at', null);
        $builder->groupBy('u.id');

        $user = $builder->get()->getRowArray();

        if ($user === null) {
            return null;
        }

        $roleCodes = array_values(array_filter(array_map('trim', explode(',', (string) ($user['role_codes'] ?? '')))));
        $roleNames = array_values(array_filter(array_map('trim', explode('||', (string) ($user['role_names'] ?? '')))));
        $user['role_codes'] = $roleCodes;
        $user['role_names'] = $roleNames;
        $user['roles'] = $roleCodes;
        $user['role'] = $roleCodes[0] ?? null;
        $user['full_name'] = (string) ($user['full_name'] ?? $user['login_identifier'] ?? '');

        unset($user['role_names']);

        return $user;
    }

    public function findByLogin(string $identity): ?array
    {
        return $this->findForAuthentication($identity);
    }

    public function getUserRoleSlugs(string $userId): array
    {
        $builder = $this->db->table('user_roles ur');
        $builder->select('r.code');
        $builder->join('roles r', 'r.id = ur.role_id', 'inner');
        $builder->where('ur.user_id', $userId);
        $builder->orderBy('ur.id', 'ASC');

        $rows = $builder->get()->getResultArray();

        return array_values(array_filter(array_map(static fn(array $row): string => (string) ($row['code'] ?? ''), $rows)));
    }

    public function touchLastLogin(string $userId): bool
    {
        return $this->update($userId, ['last_login_at' => date('Y-m-d H:i:s')]) !== false;
    }

    public function generateUserId(): string
    {
        $lastId = $this->db->query("
            SELECT MAX(CAST(id AS UNSIGNED)) as max_id FROM {$this->table}
        ")->getRow()->max_id ?? 0;

        return str_pad((string)((int)$lastId + 1), 10, '0', STR_PAD_LEFT);
    }

    public function identifierExists(string $identifier): bool
    {
        return $this->where('login_identifier', $identifier)->where('deleted_at', null)->countAllResults() > 0;
    }
}
