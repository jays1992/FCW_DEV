<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm128 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_ACCOUNTSUBGROUP";

    protected $primaryKey = "ASGID";

    public $timestamps = false;


}//class
