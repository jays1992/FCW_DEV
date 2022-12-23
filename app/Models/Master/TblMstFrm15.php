<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm15 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_ENQUIRYMEDIA";

    protected $primaryKey = "EMID";

    public $timestamps = false;


}//class
