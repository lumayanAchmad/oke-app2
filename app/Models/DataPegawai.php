<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPegawai extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get all of the for the Pegawai
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rencanaPembelajaran()
    {
        return $this->hasMany(RencanaPembelajaran::class);
    }

    public function ketua()
    {
        return $this->hasOne(Kelompok::class, 'id_ketua'); // Ketua kelompok
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo(PendidikanTerakhir::class);
    }

    public function unitKerjaCanVerifying()
    {
        return $this->hasMany(unitKerjaCanVerifying::class);
    }

    public function universitasCanApproving()
    {
        return $this->hasMany(universitasCanApproving::class);
    }

}
