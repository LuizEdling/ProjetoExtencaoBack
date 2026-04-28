<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalCatalogEntry extends Model
{
    protected $table = 'animal_catalog_entries';

    protected $fillable = [
        'kind',
        'name',
    ];
}
