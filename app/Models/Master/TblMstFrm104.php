<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm104 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_QIJ";

    protected $primaryKey = "UDFQIJID";

    public $timestamps = false;


}//class