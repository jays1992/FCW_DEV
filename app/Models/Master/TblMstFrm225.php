<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm225 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_MAINTENANCE_SUB_PARAMETER";

    protected $primaryKey = "MSPID";

    public $timestamps = false;


}//class
