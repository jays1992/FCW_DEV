<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm90 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFORSE";

    protected $primaryKey = "UDFSEID";

    public $timestamps = false;


}//class