<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm172 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_GRADE";

    protected $primaryKey = "GRADEID";

    public $timestamps = false;


}//class
