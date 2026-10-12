<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table         = 'admin_users';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['email', 'name', 'password_hash', 'last_login_at'];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', mb_strtolower(trim($email)))->first();
    }
}
