<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{

    protected $table         = 'activity_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'user_id',
        'activity',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public function logActivity(?int $userId, string $activity, ?string $description = null): bool
    {
        $request = service('request');

        return $this->insert([
            'user_id'     => $userId,
            'activity'    => $activity,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $request->getUserAgent()->getAgentString(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]) !== false;
    }
}