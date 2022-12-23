<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm140 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_CRCONVERSION";

    protected $primaryKey = "CRCOID";

    public $timestamps = false;


}//class
