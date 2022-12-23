<li class="nav-item dropdown no-arrow mx-1 clickpopups">
  <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">        
    <i class="fas fa-bell fa-fw"></i>         
    <span class="badge badge-danger badge-counter" id="total_notify_count">{{isset($data_array)?count($data_array).'+':''}}</span>
  </a>
</li>

<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in navfonts" aria-labelledby="alertsDropdown">
  <h6 class="dropdown-header">Notifications</h6>

  @if(isset($data_array) && !empty($data_array))
  @foreach($data_array as $key=>$val)
  <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)" onclick="read_notification('{{$val->TABLE_NAME}}','{{$val->COLUMN_NAME}}','{{$val->DOC_ID}}','{{$key}}')" >
    <div class="mr-3">
      <div class="icon-circle bg-primary" id="notify_{{$key}}" >
        <i class="fas fa-file-alt text-white"></i>
      </div>
    </div>
    <div>
      <div class="small text-gray-500">{{date('d-M-Y',strtotime($val->DOC_DATE))}}</div>
      <span class="font-weight-bold">A new {{$val->FORM_NAME}} document no {{$val->DOC_NO}} is ready to view!</span>
    </div>
  </a>
  @endforeach
  @else
  <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)" >
    <div class="mr-3">
      <div class="icon-circle bg-info">
        <i class="fas fa-file-alt text-white"></i>
      </div>
    </div>
    <div>
      <div class="small text-gray-500">{{date('d-M-Y')}}</div>
      <span class="font-weight-bold">No any new notification record!</span>
    </div>
  </a>
  @endif

</div>


