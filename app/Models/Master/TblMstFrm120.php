<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm120 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_BRANCH_GROUP";

    protected $primaryKey = "BGID";

    public $timestamps = false;


}//class
