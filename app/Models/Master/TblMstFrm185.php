<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm185 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_EARNING_HEAD_TYPE";

    protected $primaryKey = "EARNING_TYPEID";

    public $timestamps = false;


}//class
