<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm107 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_BOM";

    protected $primaryKey = "UDFBOMID";

    public $timestamps = false;


}//class