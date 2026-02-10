<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdjunctProfessor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'google_scholar',
        'gs_citation_number',
        'gs_h_index',
        'gs_papers_2025',
        'scopus_scholar',
        'scopus_citation_number',
        'scopus_h_index',
        'scopus_papers_2025',
        'publication_with_sohar',
        'appointment_from',
        'notes',
    ];

    protected $casts = [
        'appointment_from' => 'date',
        'gs_citation_number' => 'integer',
        'gs_h_index' => 'integer',
        'gs_papers_2025' => 'integer',
        'scopus_citation_number' => 'integer',
        'scopus_h_index' => 'integer',
        'scopus_papers_2025' => 'integer',
        'publication_with_sohar' => 'integer',
    ];
}
