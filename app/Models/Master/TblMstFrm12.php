<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm12 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDF_SRETURN";

    protected $primaryKey = "UDF_SRID";

    public $timestamps = false;


}//class