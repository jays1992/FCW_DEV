<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm110 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_PMIS";

    protected $primaryKey = "UDFPMISID";

    public $timestamps = false;


}//class