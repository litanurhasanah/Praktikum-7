<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    // karena kita merubah tabelnya dari coas menjadi coa
    protected $table = 'coa';
    use HasFactory;

    // seluruh kolom dapat dimodifikasi
    protected $guarded = [];
    //kalau semuanya pakai guarded, kalau tertentu pake fillabel
}
