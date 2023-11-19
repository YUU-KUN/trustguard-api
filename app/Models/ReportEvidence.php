<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class ReportEvidence extends Model
{
    use HasFactory, UUID;
    protected $fillable = ['report_id', 'evidence'];

    protected $appends = ['evidence_url'];

    public function getEvidenceUrlAttribute()
    {
        $evidence_path = Storage::url('evidences/');
        return url('/') . $evidence_path . $this->attributes['evidence'];
    }
}
