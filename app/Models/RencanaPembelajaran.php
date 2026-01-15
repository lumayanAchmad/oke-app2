<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaPembelajaran extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function dataPegawai()
    {
        return $this->belongsTo(DataPegawai::class);
    }

    public function dataPendidikan()
    {
        return $this->belongsTo(DataPendidikan::class)->withDefault();
    }

    public function dataPelatihan()
    {
        return $this->belongsTo(DataPelatihan::class)->withDefault();
    }

    public function bentukJalur()
    {
        return $this->belongsTo(BentukJalur::class)->withDefault();
    }

    public function jenisPendidikan()
    {
        return $this->belongsTo(JenisPendidikan::class)->withDefault();
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    public function kelompokCanValidating()
    {
        return $this->hasOne(kelompokCanValidating::class, 'rencana_pembelajaran_id');
    }

    public function unitKerjaCanVerifying()
    {
        return $this->hasOne(unitKerjaCanVerifying::class, 'rencana_pembelajaran_id');
    }

    public function universitasCanApproving()
    {
        return $this->hasOne(universitasCanApproving::class, 'rencana_pembelajaran_id');
    }

    public function suratRekomendasi()
    {
        return $this->hasOne(SuratRekomendasi::class, 'rencana_pembelajaran_id');
    }
}
