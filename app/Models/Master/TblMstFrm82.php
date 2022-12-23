<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm82 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_NRGP";

    protected $primaryKey = "UDFNRGPID";

    public $timestamps = false;


}//class