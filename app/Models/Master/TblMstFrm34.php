<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm34 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_TRANSPORTER";

    protected $primaryKey = "TRANSPORTERID";

    public $timestamps = false;


}//class
