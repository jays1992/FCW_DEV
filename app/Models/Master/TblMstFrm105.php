<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm105 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_JWI";

    protected $primaryKey = "UDFJWIID";

    public $timestamps = false;


}//class