<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm99 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_PB";

    protected $primaryKey = "UDFPBID";

    public $timestamps = false;


}//class