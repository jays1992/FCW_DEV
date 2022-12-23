<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm18 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_INDUSTRYVERTICAL";

    protected $primaryKey = "INDSVID";

    public $timestamps = false;


}//class
