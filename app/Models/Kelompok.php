<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelompok extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ketua()
    {
        return $this->belongsTo(DataPegawai::class, 'id_ketua');
    }

    public function anggota()
    {
        return $this->hasMany(DataPegawai::class, 'kelompok_id');
    }

    public function kelompokCanValidating()
    {
        return $this->hasMany(KelompokCanValidating::class);
    }

}
