<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm10 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDF_DSP";

    protected $primaryKey = "UDF_DSPID";

    public $timestamps = false;


}//class