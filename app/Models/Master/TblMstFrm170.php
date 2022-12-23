<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm170 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_POSTLEVEL";

    protected $primaryKey = "PLID";

    public $timestamps = false;


}//class
