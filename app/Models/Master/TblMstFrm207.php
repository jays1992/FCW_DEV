<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm207 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_COSTCENTER";

    protected $primaryKey = "CCID";

    public $timestamps = false;


}//class
