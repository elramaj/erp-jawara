<?php
namespace App\Models;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Komplain extends Model
{
    use BelongsToCompany;

    protected $table = 'komplain';
    protected $fillable = [
    'company_id', 'no_komplain', 'proyek_id', 'jenis', 'prioritas',
    'judul', 'deskripsi', 'status', 'masih_garansi',
    'created_by', 'handled_by', 'resolved_at', 'no_servisan',
];

    protected $casts = [
        'resolved_at'   => 'datetime',
        'masih_garansi' => 'boolean',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function timeline()
    {
        return $this->hasMany(KomplainTimeline::class)->orderBy('created_at', 'desc');
    }

    public function getPrioritasLabelAttribute()
    {
        return match($this->prioritas) {
            'critical' => '🔴 Critical',
            'high'     => '🟠 High',
            'medium'   => '🟡 Medium',
            'low'      => '🟢 Low',
            default    => $this->prioritas,
        };
    }
}