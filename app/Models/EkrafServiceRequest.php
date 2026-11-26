<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EkrafServiceRequest extends Model
{
    protected $fillable = ['reference', 'secret_hash', 'category', 'applicant', 'contact', 'description', 'status'];

    protected $hidden = ['secret_hash'];

    public const CATEGORIES = [
        'pendataan' => 'Pendataan pelaku ekraf',
        'pembinaan' => 'Permohonan pembinaan',
        'kolaborasi' => 'Kemitraan dan kolaborasi',
        'program' => 'Usulan program',
        'keanggotaan' => 'Informasi keanggotaan',
        'lainnya' => 'Informasi lainnya',
    ];

    public const STATUSES = [
        'received' => 'Diterima',
        'reviewing' => 'Dalam peninjauan',
        'processing' => 'Dalam tindak lanjut',
        'completed' => 'Selesai',
        'closed' => 'Ditutup',
    ];

    public const TRANSITIONS = [
        'received' => ['reviewing', 'closed'],
        'reviewing' => ['processing', 'completed', 'closed'],
        'processing' => ['reviewing', 'completed', 'closed'],
        'completed' => [],
        'closed' => [],
    ];

    public function events(): HasMany
    {
        return $this->hasMany(EkrafServiceEvent::class);
    }
}
