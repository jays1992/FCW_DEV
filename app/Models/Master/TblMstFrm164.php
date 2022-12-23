<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstFrm164 extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_NATUAREOF_ASSESSEE";

    protected $primaryKey = "NOAID";

    public $timestamps = false;


}//class
