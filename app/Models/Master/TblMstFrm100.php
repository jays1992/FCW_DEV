<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm100 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_JWO";

    protected $primaryKey = "UDFJWOID";

    public $timestamps = false;


}//class