<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm96 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_SSI";

    protected $primaryKey = "UDFSSIID";

    public $timestamps = false;


}//class