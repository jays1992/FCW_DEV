<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm16 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_PRIORITY";

    protected $primaryKey = "PRIORITYID";

    public $timestamps = false;


}//class
