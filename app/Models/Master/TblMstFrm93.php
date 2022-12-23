<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm93 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_GENERALLEDGER";

    protected $primaryKey = "GLID";

    public $timestamps = false;


}//class
