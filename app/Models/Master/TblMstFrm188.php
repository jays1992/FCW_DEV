<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm188 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_DEDUCTION_HEAD";

    protected $primaryKey = "DEDUCTION_HEADID";

    public $timestamps = false;


}//class
