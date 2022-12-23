<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm135 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_QCP";

    protected $primaryKey = "QCPID";

    public $timestamps = false;


}//class
