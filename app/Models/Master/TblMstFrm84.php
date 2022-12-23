<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm84 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_GRNRGP";

    protected $primaryKey = "GRNRGPID";

    public $timestamps = false;


}//class