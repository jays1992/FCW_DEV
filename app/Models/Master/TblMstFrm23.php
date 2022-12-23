<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm23 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_COUPON_TYPE";

    protected $primaryKey = "COUPONTID";

    public $timestamps = false;


}//class
