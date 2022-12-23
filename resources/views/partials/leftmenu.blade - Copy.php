<div class="overflow-container">
			<nav class="nav" role="navigation">
			<ul class="nav__list">
			  <!-- first level begin-->
			  @forelse($menu_data as $key1=>$val1)
			  <li>
				<input id="group-{{ $loop->iteration }}" type="checkbox" hidden />
				<label for="group-{{ $loop->iteration }}"><span class="fa fa-angle-right"></span> {{$key1}}</label>
					<!-- second begin  -->
					<ul class="group-list">				 
						@foreach($val1 as $key2=>$val2)
						<li>
						<input id="sub-group-{{ $loop->parent->iteration }}{{ $loop->iteration }}" type="checkbox" hidden />
						<label for="sub-group-{{ $loop->parent->iteration }}{{ $loop->iteration }}"><span class="fa fa-angle-right"></span>  {{$key2}} </label>
							<!-- third begin  -->
							<ul class="sub-group-list">
								@foreach($val2 as $key3=>$val3)
									<li>
										@if(strtolower($val3['heading'])==='master')
											<a href="{{route('master',[$val3['formid'],'index' ])}}">{{$val3['formname']}}</a>

										@elseif(strtolower($val3['heading'])==='transactions')
											@php
												$formid = (int)$val3['formid'];
												if(strpos(strtolower($val3['formname']), "amendment") !== false ){
													$formid--;
												}
											@endphp
											<a href="{{route('transaction',[$formid,'index' ])}}">{{$val3['formname']}}</a>								
										@else
											<a href="#">{{$val3['formname']}}</a>
										@endif
									</li>
								@endforeach
							</ul>
							<!-- third end  -->
						</li>		  
						@endforeach
					</ul>
					<!-- second end  -->
			  </li>	
			  @empty
			  	<li> No Record Found.</li>
			  @endforelse
			  <!-- first level  end-->				
			
			</ul>