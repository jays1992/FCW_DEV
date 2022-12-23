<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm101 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_JWC";

    protected $primaryKey = "UDFJWCID";

    public $timestamps = false;


}//class