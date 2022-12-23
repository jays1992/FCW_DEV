<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm3 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_STATE";

    protected $primaryKey = "STID";

    public $timestamps = false;


}//class
