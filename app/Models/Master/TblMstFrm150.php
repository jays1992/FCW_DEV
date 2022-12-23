<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm150 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_USER";

    protected $primaryKey = "USERID";

    public $timestamps = false;


}//class
