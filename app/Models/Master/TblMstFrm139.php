<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm139 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_TAXTYPE";

    protected $primaryKey = "TAXID";

    public $timestamps = false;


}//class
