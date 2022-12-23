<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm53 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_VQ_MANAGEMENT";

    protected $primaryKey = "VQMID";

    public $timestamps = false;


}//class