<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstCompany extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_COMPANY";

    protected $primaryKey = "CYID";

    protected $appends = ['STATUS'];

  

}
