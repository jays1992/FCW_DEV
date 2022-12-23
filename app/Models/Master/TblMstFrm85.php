<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm85 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_GRNGE";

    protected $primaryKey = "GRNGEID";

    public $timestamps = false;


}//class