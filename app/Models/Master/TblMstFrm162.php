<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm162 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_FUEL_TYPE";

    protected $primaryKey = "FUELID";

    public $timestamps = false;


}//class
