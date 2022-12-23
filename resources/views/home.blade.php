
@extends('layouts.app')
@section('content')

<link href="css/sb-admin-3.min.css" rel="stylesheet">
<style type="text/css">
  .bgdesignsn {
    background: url(../img/body-bg.jpg) no-repeat;}
</style>

<div class="container-fluid "> 
  <div class="col-md-6">
   <div class="row">
      <div class="col-md-12">
       <div id="myCarousel" class="carousel slide" data-ride="carousel" style="margin-bottom:20px">
    <!-- Wrapper for slides -->
    <div class="carousel-inner">



    @if(!empty($slider_data))
          @foreach($slider_data as $key=>$row)
            <div class="item  {{ $key==0 ? 'active':''}}">
              <img src="{{ asset('http://bsquareappfordemo.com:8888/docs/company1/BannerImage/'.$row->UPLOADBANNER) }}" alt="Express Car wash" style="width:100%;">
            </div>
          @endforeach
          @else
          <div class="item active">
          <img src="{{ asset('img/ultra-crystal.jpg') }}" alt="Express Car wash" style="width:100%;">
          </div>
        @endif






    </div>

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
    </div>
    <!-- <img src="../img/ultra-crystal.jpg" class="lft_pic"> -->
   </div>
    </div>
	  <div class="col-md-6">
  <div class="row">

  @if(isset($menu_data) && !empty($menu_data))
    @foreach($menu_data as $key=>$val)
    @php
    if(strtolower($val->heading)==='master'){
      $heading="master";
    }
    else if(strtolower($val->heading)==='report'){
      $heading="report";
    }
    else if(strtolower($val->heading)==='udf'){
      $heading="master";
    }
    else if(strtolower($val->heading)==='transactions'){
      $heading="transaction";							
    }
    @endphp
    <div class="col-xl-4 col-md-6 mb-2">
    <a href="{{route($heading,[$val->formid,'index' ])}}">
      <div class="{{$val->COLOR}}">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col innerdesd">
              <img style="width:50px !important;" src="{{$val->ICON_IMAGE}}" alt="">
              
            </div>
           
          </div>
        </div>
      </div>
	  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{$val->formname}}</div>
      </a>
    </div>
    @endforeach
    @endif

      </div>

  </div>
</div>
@endsection
        