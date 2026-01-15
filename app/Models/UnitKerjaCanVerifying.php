<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerjaCanVerifying extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dataPegawai()
    {
        return $this->belongsTo(DataPegawai::class);
    }

    public function rencanaPembelajaran()
    {
        return $this->belongsTo(RencanaPembelajaran::class);
    }

    public function catatanVerifikasi()
    {
        return $this->hasMany(CatatanVerifikasi::class, 'unit_kerja_can_verifying_id');
    }
}
