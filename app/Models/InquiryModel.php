<?php

namespace App\Models;

use CodeIgniter\Model;

class InquiryModel extends Model
{
    public const TYPES = [
        'kontakt' => 'Kontakt',
        'foretag' => 'Företagsevent',
        'grupp'   => 'Gruppbokning',
        'tavling' => 'Egen tävling',
    ];

    public const STATUSES = ['new' => 'Ny', 'handled' => 'Hanterad'];

    protected $table         = 'inquiries';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'type', 'name', 'email', 'phone', 'organisation', 'participants', 'preferred_date',
        'package_interest', 'wants_food', 'message', 'status', 'mailed_at', 'mail_error',
    ];

    /** Raderar förfrågningar äldre än lagringstiden i integritetspolicyn. */
    public function purgeExpired(int $months): int
    {
        $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$months} months")))->delete();

        return $this->db->affectedRows();
    }
}
