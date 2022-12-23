<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblMstCountry extends Model
{
    use HasFactory;

    protected $table = "TBL_MST_COUNTRY";

    protected $primaryKey = "CTRYID";

    public $timestamps = false;

    // protected $fillable = ['CTRYCODE','NAME','ISDCODE','LANG','CONTINENTAL','CAPITAL','STATUS' ];

    // public function getSTATUSAttribute(){
    //     return (!Empty($this->STATUS) && $this->STATUS==1)?'Approved2': 'Unapproved2';
    // }

   

}//class
