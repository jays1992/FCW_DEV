<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm190 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_HOLIDAY_TYPE";

    protected $primaryKey = "HOLIDAYTYPEID";

    public $timestamps = false;


}//class
