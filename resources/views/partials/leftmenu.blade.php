<div class="menuleft jquery-accordion-menu"  id="jquery-accordion-menu" >
    <div class="jquery-accordion-menu-header" id="form"></div>	
		<ul class="nav__list" id="vertical-menu"> 
			<li><a href="{{route('home')}}"><i class="fa fa-home"></i> Dashboard</a></li>
			<li><a href="#"><i class="fa fa-user"></i> Transactions</a>
				<ul class="submenu">
					@foreach($menu_data as $key=>$val)
					<li>
					@if(strtolower($val['heading'])==='master')
					<a class="collapse-item" href="{{route('master',[$val['formid'],'index' ])}}"><i class="fa fa-angle-double-right"></i>
 {{$val['formname']}}</a>
					@elseif(strtolower($val['heading'])==='udf')
						<a class="collapse-item" href="{{route('master',[$val['formid'],'index' ])}}"><i class="fa fa-angle-double-right"></i>
 {{$val['formname']}}</a>
					@elseif(strtolower($val['heading'])==='transactions')
					<a class="collapse-item" href="{{route('transaction',[$val['formid'],'index' ])}}"><i class="fa fa-angle-double-right"></i>
 {{$val['formname']}}</a>											
					@endif
					</li>
					@endforeach
				</ul>
			</li>
			<li><a href="#"><i class="fa fa-user"></i> Reports</a>
				<ul class="submenu">
					@foreach($menu_data as $key=>$val)
					<li>
					@if(strtolower($val['heading'])==='reports')
						<a class="collapse-item" href="{{route('report',[$val['formid'],'index' ])}}"><i class="fa fa-angle-double-right"></i>
 {{$val['formname']}}</a>											
					@endif
					</li>
					@endforeach
				</ul>
			</li>
		</ul>
		