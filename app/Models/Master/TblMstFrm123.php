<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm123 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_FYEAR";

    protected $primaryKey = "FYID";

    public $timestamps = false;


}//class
