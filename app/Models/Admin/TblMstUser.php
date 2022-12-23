<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TblMstUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'custom';

    protected $table = "TBL_MST_USER";

    protected $primaryKey = "USERID";

    protected $hidden = [
        'PASSWORD',
    ];

    public function getAuthPassword()
    {
      return $this->PASSWORD;
    }


}
