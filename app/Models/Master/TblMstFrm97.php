<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm97 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_UDFFOR_SPI";

    protected $primaryKey = "UDFSPIID";

    public $timestamps = false;


}//class