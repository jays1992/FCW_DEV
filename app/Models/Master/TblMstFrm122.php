<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm122 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_YEAR";

    protected $primaryKey = "YRID";

    public $timestamps = false;


}//class
