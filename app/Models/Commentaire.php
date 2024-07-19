<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'commentaires';

    protected $fillable = [
        'ligne',
        'idClient',
        'libelle',
        'email',
        'telephone',
        'num_facture',
        'credit',
        'debit',
        'message',
        'id_agent',
    ];
}
