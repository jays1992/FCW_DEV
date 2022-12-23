<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm2 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_NATUREOFGROUP";

    protected $primaryKey = "NOGID";

    public $timestamps = false;


}//class
