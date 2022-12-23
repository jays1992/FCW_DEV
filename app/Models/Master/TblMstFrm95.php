<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm95 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_SSO";

    protected $primaryKey = "UDFSSOID";

    public $timestamps = false;


}//class