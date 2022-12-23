<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm80 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_MIS";

    protected $primaryKey = "UDFMISID";

    public $timestamps = false;


}//class