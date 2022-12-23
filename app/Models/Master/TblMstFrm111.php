<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm111 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_PNM";

    protected $primaryKey = "UDFPNMID";

    public $timestamps = false;


}//class