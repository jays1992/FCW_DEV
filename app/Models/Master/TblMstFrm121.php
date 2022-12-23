<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm121 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_MONTH";

    protected $primaryKey = "MTID";

    public $timestamps = false;


}//class
