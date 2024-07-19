<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recouvrement extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasTimestamps;
    use HasFactory;
    protected $fillable = [
        "ligne",
        "idClient",
        "libelle",
        "email",
        "telephone",
        "num_facture",
        "credit",
        "debit",
        "id_agent"
    ];

     // Assurez-vous que le modèle utilise bien les timestamps
     public $timestamps = true;
}
