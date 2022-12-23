<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm180 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_SHIFT";

    protected $primaryKey = "SHIFTID";

    public $timestamps = false;


}//class
