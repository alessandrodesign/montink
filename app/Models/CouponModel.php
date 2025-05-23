<?php

namespace App\Models;

use App\Entities\CouponEntity;
use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table         = 'coupons';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = CouponEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['code', 'discount_type', 'discount_amount', 'min_purchase', 'starts_at', 'expires_at', 'status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'code'            => 'required|min_length[3]|max_length[50]|is_unique[coupons.code,id,{id}]',
        'discount_type'   => 'required|in_list[percentage,fixed]',
        'discount_amount' => 'required|numeric|greater_than[0]',
        'min_purchase'    => 'permit_empty|numeric|greater_than_equal_to[0]',
        'starts_at'       => 'permit_empty|valid_date',
        'expires_at'      => 'permit_empty|valid_date',
        'status'          => 'required|in_list[active,inactive]',
    ];

    public function findByCode(string $code)
    {
        return $this->where('code', $code)->first();
    }

    public function getValidCoupons()
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('status', 'active')
            ->where('(starts_at IS NULL OR starts_at <= "' . $now . '")')
            ->where('(expires_at IS NULL OR expires_at >= "' . $now . '")')
            ->findAll();
    }
}