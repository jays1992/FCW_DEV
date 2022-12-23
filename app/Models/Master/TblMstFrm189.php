<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm189 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_ATTENDANCE_STATUS";

    protected $primaryKey = "ATTENDANCE_STID";

    public $timestamps = false;


}//class
