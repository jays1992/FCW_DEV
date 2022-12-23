<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm103 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_GRJ";

    protected $primaryKey = "UDFGRJID";

    public $timestamps = false;


}//class