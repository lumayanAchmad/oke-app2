<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratRekomendasi extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function rencanaPembelajaran()
    {
        return $this->belongsTo(RencanaPembelajaran::class, 'rencana_pembelajaran_id');
    }
}
