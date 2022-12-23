<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm22 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_COUPON_CAT";

    protected $primaryKey = "COUPONCID";

    public $timestamps = false;


}//class
