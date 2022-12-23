<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm131 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_INVENTORY_CLASS";

    protected $primaryKey = "CLASSID";

    public $timestamps = false;


}//class
