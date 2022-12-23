<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm8 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_BUSINESSUNIT";

    protected $primaryKey = "BUID";

    public $timestamps = false;


}//class
