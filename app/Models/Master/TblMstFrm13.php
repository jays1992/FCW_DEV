<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm13 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDF_PACKINGSLIP";

    protected $primaryKey = "UDF_PSID";

    public $timestamps = false;


}//class