<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportFile extends Model
{
    use HasFactory;

    protected $table="import_files";

    protected $tagName = 'Import Files';

    protected $fillable = [
        'file_name',
        'type',
        'status',
        'created_by'
    ];
}
