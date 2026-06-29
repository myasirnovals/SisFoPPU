<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeScaleModel extends Model
{
    protected $table            = 'grade_scales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'scale_code',
        'grade_letter',
        'min_score',
        'max_score',
        'grade_point',
        'predicate',
        'description',
        'is_passing',
        'is_default',
        'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'grade_letter' => 'required|max_length[10]',
        'min_score'    => 'required|decimal|less_than_equal_to[100]',
        'max_score'    => 'required|decimal|less_than_equal_to[100]',
        'is_passing'   => 'required|in_list[0,1]',
    ];
    protected $validationMessages = [
        'grade_letter' => [
            'required' => 'Huruf mutu wajib diisi.',
        ],
        'min_score' => [
            'required' => 'Batas bawah wajib diisi.',
        ],
        'max_score' => [
            'required' => 'Batas atas wajib diisi.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate     = [];
    protected $afterUpdate      = [];
    protected $beforeFind       = [];
    protected $afterFind        = [];
    protected $beforeDelete     = [];
    protected $afterDelete      = [];
}
