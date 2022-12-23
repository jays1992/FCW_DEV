<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm87 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_SRN";

    protected $primaryKey = "UDFSRNID";

    public $timestamps = false;


}//class