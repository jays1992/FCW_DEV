<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm166 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_WITHHOLDING";

    protected $primaryKey = "HOLDINGID";

    public $timestamps = false;


}//class