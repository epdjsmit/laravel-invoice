@extends('layout.master')

@section('content')
	@include('layout.errorform')
	@include('layout.info')


<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">Invoice</div>
		<div class="panel-body">
			<div class="col-lg-12">

				{!! Form::model($sales, ['route' => ['sales.update', $sales->id], 'method' => 'PATCH', 'class' => 'form-horizontal', 'autocomplete' => 'off', 'files' => true]) !!}

				@if( auth()->user()->id_group == 1 )
					<?php
					$user = App\User::all();
					foreach ($user as $key) {
						$k[$key->id] = $key->name;
					}
					?>
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-default">
								<div class="panel-heading">User</div>
								<div class="panel-body">
									<div class="col-lg-12">
										<div class="form-group {!! ( count($errors->get('id_user')) ) >0 ? 'has-error' : '' !!}">
											{!! Form::label('us', 'User :', ['class' => 'control-label col-lg-2']) !!}
											<div class="col-lg-10">
												{!! Form::select('id_user', $k, @$value, ['placeholder' => 'Choose User', 'class' => 'form-control', 'id' => 'us']) !!}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				@else
				{!! Form::hidden('id_user' , auth()->user()->id) !!}
				@endif
				
				<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Postage Slip</div>
						<div class="panel-body">
				
							<div class="form-group {!! ( count($errors->get('date')) ) >0 ? 'has-error' : '' !!}">
								{!! Form::label('da', 'Date :', ['class' => 'col-sm-5 control-label']) !!}
								<div class="col-sm-7">
									{!! Form::input('text', 'date_sale', @$value, ['class' => 'form-control date', 'placeholder' => 'Date', 'id' => 'da']) !!}
								</div>
							</div>
							<div class="serial_wrap">
<?php
$d = App\SlipNumbers::where(['id_sales' => $sales->id, 'deleted_at' => null])->get();
$vs = 9990;
$ys = 0;
if($d->count() > 0):
foreach ($d as $e):
$vs++;
?>
								<div class="form-group <?php echo ( count($errors->get('serial.*.tracking_number')) ) > 0 ? 'has-error' : '' ?> rowserial">
									<a href="{!! route('slipnumbers.destroy', $e->id) !!}" class="btn btn-danger col-lg-1">-</a>{!! Form::label('catel', 'Receipt or Postage Tracking :', ['class' => 'col-sm-4 control-label']) !!}
									<input type="hidden" name="serial[{!! $vs !!}][id]" value="{!! $e->id !!}">
									<div class="col-sm-7">
										{!! Form::input('text', 'serial['.$vs.'][tracking_number]', $e->tracking_number, ['class' => 'form-control', 'placeholder' => 'Receipt or Tracking Serial Number', 'id' => 'catel']) !!}
									</div>
								</div>
<?php
endforeach;
endif;
?>
							</div>
							<p><button class="add_serial btn btn-default" type="button">Add Postage Tracking</button></p>
							<div class="form-group <?php echo ( count( $errors->get('image.*') ) ) > 0 ? 'has-error' : '' ?>">
								{!! Form::label('img', 'Receipt or Postage Slip Image :', ['class' => 'col-sm-5 control-label']) !!}
								<div class="col-sm-7">
									{!! Form::file('image[]', ['id' => 'img', 'multiple' => 'multiple']) !!}
								</div>
							</div>

							<div class="col-lg-12">
								<div class="col-lg-4">
									<strong>Image :</strong><br />
								</div>
								<div class="col-sm-8">

								<?php
									$r = App\SlipPostage::where(['id_sales' => $sales->id, 'deleted_at' => null])->get();
									foreach ( $r as $imu ):
								?>
										<!-- Trigger the modal with a button -->
										<!-- Modal -->
										<div id="myModal" class="modal fade" role="dialog">
											<div class="modal-dialog">
										
												<!-- Modal content-->
												<div class="modal-content">
													<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title">Slip Receipt / Postage</h4>
													</div>
													<div class="modal-body">
														<img src="data:{!! $imu->mime !!};base64,{!! $imu->image !!}" class="img-responsive img-rounded">
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
													</div>
												</div>
											</div>
										</div>
										<a href="#" data-toggle="modal" data-target="#myModal">
											<img src="data:{!! $imu->mime !!};base64,{!! $imu->image !!}" class="img-responsive img-rounded">
										</a>
										<br />
										<a href="{!! route('slippostage.destroy', $imu->id) !!}" class="btn btn-danger">Delete</a>
								<?php
								endforeach;
								?>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">Customer</div>
						<div class="panel-body">
<?php
$cst = App\SalesCustomers::where(['id_sales' => $sales->id])->first();
$rcst = App\Customers::where(['id' => $cst->id_customer])->first();
echo Form::hidden('repeatcust_id', $cst->id);
?>
<!-- 							<p class="text-justify">Please fill up only 1 section. Select your <span style="color: red;">Returning Customer</span> or <span style="color: red;">New Customer</span>, but please dont fill both section.</p>
 -->							<div class="row">
								<div class="col-lg-12">
									<div class="panel panel-default">
										<div class="panel-heading">Returning Customer</div>
										<div class="panel-body">
											<div class="form-group <?php echo ( count($errors->get('repeatcust'))  > 0 ) ? 'has-error' : '' ?>">
												<label for="custsel" class="col-sm-3 control-label">Select Existing Customer : </label>
												<div class="col-sm-9">
													<select name="repeatcust" id="custsel" class="form-control">
														<option value="" data-client="" data-client_address="" data-client_poskod="" data-client_email="" data-client_phone="" >Choose Customer</option>
														<?php $rc = \App\Customers::all() ?>
														@if( $rc->count() > 0 )
														@foreach( $rc as $ec ) :
															<option value="{!! $ec->id !!}" data-client="{!! $ec->client !!}" data-client_address="{!! $ec->client_address !!}" data-client_poskod="{!! $ec->client_poskod !!}" data-client_email="{!! $ec->client_email !!}" data-client_phone="{!! $ec->client_phone !!}" {!! ( $ec->id == $cst->id_customer )? 'selected="selected"' : '' !!} >{!! $ec->client !!}</option>
														@endforeach
														@endif
													</select>
												</div>
											</div>
											<div class="form-group">
												<label for="rt" class="col-sm-3 control-label" >Customer Info :</label>
												<div class="col-sm-9">
													<span id="client"><?=$rcst->client?></span>
													<br />
													<span id="address"><?=$rcst->client_address?></span>
													<br />
													<span id="poskod"><?=$rcst->client_poskod?></span>
													<br />
													<span id="phone"><?=$rcst->client_phone?></span>
													<br />
													<span id="email"><?=$rcst->client_email?></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

<!-- 							<div class="row">
								<div class="col-lg-12">
									<div class="panel panel-default">
										<div class="panel-heading">New Customer</div>
										<div class="panel-body">
											<div class="form-group {!! ( count($errors->get('client'))  > 0 )? 'has-error' : '' !!}">
												{!! Form::label('pel', 'Customer :', ['class' => 'col-sm-3 control-label']) !!}
												<div class="col-sm-9">
													{!! Form::input('text', 'client', @$value, ['class' => 'form-control', 'placeholder' => 'Customer', 'id' => 'pel']) !!}
												</div>
											</div>
											<div class="form-group {!! ( count($errors->get('client_address')) ) >0 ? 'has-error' : '' !!}">
												{!! Form::label('apel', 'Customer Address :', ['class' => 'col-sm-3 control-label']) !!}
												<div class="col-sm-9">
													{!! Form::textarea('client_address', @$value, ['class' => 'form-control', 'placeholder' => 'Customer Address', 'id' => 'apel']) !!}
												</div>
											</div>
											<div class="form-group {!! ( count($errors->get('client_poskod')) ) >0 ? 'has-error' : '' !!}">
												{!! Form::label('pos', 'Postcode :', ['class' => 'col-sm-3 control-label']) !!}
												<div class="col-sm-9">
													{!! Form::input('text', 'client_poskod', @$value, ['class' => 'form-control', 'placeholder' => 'Postcode', 'id' => 'pos']) !!}
												</div>
											</div>
											<div class="form-group {!! ( count($errors->get('client_phone')) ) >0 ? 'has-error' : '' !!}">
												{!! Form::label('tel', 'Phone :', ['class' => 'col-sm-3 control-label']) !!}
												<div class="col-sm-9">
													{!! Form::input('text', 'client_phone', @$value, ['class' => 'form-control', 'placeholder' => 'Phone', 'id' => 'tel']) !!}
												</div>
											</div>
											<div class="form-group {!! ( count($errors->get('client_email')) ) >0 ? 'has-error' : '' !!}">
												{!! Form::label('tela', 'Email :', ['class' => 'col-sm-3 control-label']) !!}
												<div class="col-sm-9">
													{!! Form::input('text', 'client_email', @$value, ['class' => 'form-control', 'placeholder' => 'Email', 'id' => 'tela']) !!}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div> -->

						</div>
					</div>
				</div>
				</div>
				
				<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">Add Invoice Item</div>
						<div class="panel-body">
				
							<div class="col-lg-12 input_fields_wrap">

<?php
$ty = App\SalesItems::where(['id_sales' => $sales->id, 'deleted_at' => null])->get();
$v = 9990;
$y = 0;
if($ty->count() > 0):
foreach ($ty as $e):
$v++;
?>

<div class="row rowinvoice">
	<div class="col-sm-12">
		<div class="col-sm-1">
			<div class="row">
				<div class="col-sm-12">
					<a href="{!! route('salesitems.destroy', $e->id) !!}" class="btn btn-danger" type="button">-</a>
					<input type="hidden" name="inv[{!! $v !!}][id]" value="{!! $e->id !!}">
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="row">
				<div class="col-sm-12 form-group <?php echo ( count($errors->get('inv.*.id_product')) ) >0 ? 'has-error' : '' ?>">
					<select name="inv[{!! $v !!}][id_product]" class="series form-control">
						<option value="">Choose Product</option>
						<?php $cate = App\ProductCategory::where(['active' => 1])->get(); ?>
						@foreach ($cate as $key)
							<optgroup label="{!! $key->product_category !!}">
								<?php $pro = App\Product::where(['id_category' => $key->id, 'active' => 1])->get() ?>
								@foreach($pro as $r)
									<option value="{!! $r->id !!}" data-commission="{!! $r->commission !!}" data-retail="{!! $r->retail !!}" {!! ($r->id == $e->id_product)? 'selected="selected"' : '' !!}>{!! $r->product !!}</option>
								@endforeach
							</optgroup>
						@endforeach
					</select>
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="row">
				<div class="col-sm-12 form-group {!! ( count($errors->get('inv.*.commission')) ) >0 ? 'has-error' : '' !!}">
					<input <?=(auth()->user()->id_group == 1)? 'type="text"' : 'type="hidden"' ?> name="inv[{!! $v !!}][commission]" class="comm form-control" value="{!! $e->commission !!}" placeholder="Commission (RM)" />
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="row">
				<div class="col-sm-12 form-group {!! ( count($errors->get('inv.*.retail')) ) >0 ? 'has-error' : '' !!}">
					<input type="text" name="inv[{!! $v !!}][retail]" class="rate form-control" value="{!! $e->retail !!}" placeholder="Retail (RM)"/>
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="row">
				<div class="col-sm-12 form-group {!! ( count($errors->get('inv.*.quantity')) ) >0 ? 'has-error' : '' !!}">
					<input type="text" name="inv[{!! $v !!}][quantity]" class="quan form-control" value="{!! $e->quantity !!}" placeholder="Quantity" />
				</div>
			</div>
		</div>
		<div class="col-sm-1">
			<div class="row">
				<div class="col-sm-12">
					<p class="text-right"><span class="total_price">{!! $e->retail * $e->quantity !!}</span></p><?php $y += $e->retail * $e->quantity ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
endforeach;
endif;
?>

							</div>
				
							<div class="col-lg-12">
								<div class="row">
									<div class="col-sm-12 col-md-4 col-md-offset-8">

<?php
$ttax = App\SalesTax::where(['id_sales' => $sales->id, 'deleted_at' => null])->get();
$tcharge = 0;
foreach ($ttax as $k) {
	$tcharge += App\Taxes::findOrFail($k->id_tax)->amount;
}
$y = $y + ( $y * ($tcharge / 100) );
?>

										<div class="row">
											<div class="col-lg-12">
												<div class="col-xs-2">
													<p><strong>Tax</strong></p>
												</div>
												<div class="col-xs-6">
													<p>
														<select name="tax[]" id="taxs" multiple="multiple">
															<option value="">Choose Tax</option>
															@foreach(App\Taxes::all() as $r) :
															<option value="{!! $r->id !!}<?php

															$ty = App\SalesTax::where(['id_sales' => $sales->id, 'deleted_at' => NULL])->get();
															foreach ($ty as $op) {
																if ($op->id_tax == $r->id) {
																	// echo '_'.$op->id;
																}
															}
															?>" data-amount="{!! $r->amount !!}" 

															<?php
															$ty = App\SalesTax::where(['id_sales' => $sales->id, 'deleted_at' => NULL])->get();
															foreach ($ty as $op) {
																if ($op->id_tax == $r->id) {
																	echo 'selected';
																}
															}
															?>
															 >{!! $r->tax !!}</option>
															@endforeach
														</select>
													</p>

												</div>
												<div class="col-xs-4 text-right">
													<p><strong id="total_tax">{!! $tcharge !!}</strong>%</p>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-12">
												<div class="col-xs-8">
													<p><strong>Total Amount</strong></p>
												</div>
												<div class="col-xs-4 text-right">
													<p>RM<strong id="total_amount">{!! $y !!}</strong></p>
												</div>
											</div>
										</div>


									</div>
								</div>
							</div>
							<div class="col-lg-12">
								<p><button class="btn btn-default add_field" type="button">Add Products</button></p>
							</div>
						</div>
					</div>
				</div>
				</div>

				<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">AddPayment</div>
						<div class="panel-body">

							<div class="col-lg-12">
								<div class="col-lg-12 payment_wrap">
<?php
$nm = App\Payments::where(['id_sales' => $sales->id, 'deleted_at' => null])->get();
$z = 990;
$m = 0;
if($nm->count() > 0):
foreach($nm as $o):
$z++;
?>
<div class="row rowpayment">
	<div class="col-sm-12">
		<div class="col-sm-1">
			<div class="row">
				<div class="col-sm-12">
					<a href="{!! route('payments.destroy', $o->id) !!}" class="btn btn-danger" type="button">-</a>
					<input type="hidden" name="pay[{!! $z !!}][id]" value="{!! $o->id !!}">
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-12 form-group <?php echo ( count($errors->get('pay.*.id_bank')) ) >0 ? 'has-error' : '' ?>">
					<select name="pay[{!! $z !!}][id_bank]" class="form-control">
						<option value="">Choose Bank</option>
						<?php $cate = App\Banks::where(['active' => 1])->get(); ?>
						@foreach ($cate as $r)
						<option value="{!! $r->id !!}" {!! ($r->id == $o->id_bank)? 'selected="selected"' : '' !!}>{!! $r->bank !!}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="row">
				<div class="col-sm-12 form-group {!! ( count($errors->get('pay.*.date_payment')) ) >0 ? 'has-error' : '' !!}">
					<input type="text" name="pay[{!! $z !!}][date_payment]" value="{!! $o->date_payment !!}" class="form-control date" placeholder="Date Payment"/>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="row">
				<div class="col-sm-12 form-group {!! ( count($errors->get('pay.*.amount')) ) >0 ? 'has-error' : '' !!}">
					<input type="text" name="pay[{!! $z !!}][amount]" value="{!! $o->amount !!}" class="pamount form-control" placeholder="Amount (RM)"/><?php $m += $o->amount ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
endforeach;
endif;
?>
								</div>
								<div class="col-lg-12">
									<div class="row">
										<div class="col-sm-12 col-md-3 col-md-offset-9">
											<div class="col-xs-8">
												<p><strong>Total Payment</strong></p>
											</div>
											<div class="col-xs-4 text-right">
												<p>RM<strong id="total_payment">{!! $m !!}</strong></p>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12 col-md-3 col-md-offset-9">
											<div class="col-xs-8">
												<p><strong>Balance</strong></p>
											</div>
											<div class="col-xs-4 text-right">
												<p>RM<strong id="balance">{!! $m - $y !!}</strong></p>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-12">
									<p><button class="btn btn-default add_payment" type="button">Add Payment</button></p>
								</div>
							</div>

						</div>
					</div>
				</div>
				</div>

				<!-- end here -->
				<div class="form-group col-lg-12">
					<div class="col-sm-offset-2 col-sm-10">
						<p>{!! Form::submit('Update', ['class' => 'btn btn-primary btn-lg btn-block']) !!}</p>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection


@section('jquery')
////////////////////////////////////////////////////////////////////////////////////
// ucwords input
	$(document).on('keyup', '#pel', function () {
	// $("input").keyup(function() {
		toUpper(this);
	});

////////////////////////////////////////////////////////////////////////////////////
// uppercase input for tracking number and customer section
	$(document).on('keyup', '#apel, #catel', function () {
		toUppercase(this);
	});

////////////////////////////////////////////////////////////////////////////////////
// date input helper
	$(document).on('mouseenter', '.date', function () {
		$(this).datepicker({
			autoclose:true,
			format:'yyyy-mm-dd',
			todayHighlight : true,
			// startDate: '<?= 1 - (date('j', mktime(0, 0, 0, date('m'), 0, date('Y'))) + date('j') ) ?>d',
			// endDate: '<?= (date('j') == 1) ? 1 - date('j') : 0 - date('j') ?>d',
		});	
	});

////////////////////////////////////////////////////////////////////////////////////
// capital case helper
	function toUppercase(obj) {
		var mystring = obj.value;
		var sp = mystring.split(' ');
		var wl=0;
		var f ,r;
		var word = new Array();
		for (i = 0 ; i < sp.length ; i ++ ) {
			f = sp[i].substring(0,1).toUpperCase();
			r = sp[i].substring(1).toUpperCase();
			word[i] = f+r;
		}
		newstring = word.join(' ');
		obj.value = newstring;
		return true;
	}

////////////////////////////////////////////////////////////////////////////////////
// slip serial number : add and remove row
var add_buttons	= $(".add_serial");
var wrappers	= $(".serial_wrap");

var xs = 0;
$(add_buttons).click(function(){
	// e.preventDefault();
	xs++;
	wrappers.append(
		'<div class="form-group <?php echo ( count($errors->get('serial[][tracking_number]')) ) > 0 ? 'has-error' : '' ?> rowserial">' +
			'<button type="button" class="btn btn-danger col-lg-1 remove_serial">-</button>{!! Form::label('catel', 'Receipt or Postage Tracking :', ['class' => 'col-sm-4 control-label']) !!}' +
			'<input type="hidden" name="serial[' + xs + '][id]" value="">' +
			'<div class="col-sm-7">' +
				'<input type="text" name="serial[' + xs + '][tracking_number]" value="{!! @$value !!}" class="form-control" placeholder="Receipt or Tracking Serial Number" id="catel" />' +
			'</div>' +
		'</div>'
	); //add input box
});
			
$(wrappers).on("click",".remove_serial", function(e){
	//user click on remove text
	e.preventDefault();
	$(this).parent('.rowserial').remove();
})

////////////////////////////////////////////////////////////////////////////////////
// helper tax 
$(document).on('change', '#taxs', function () {
	var se=0;
	var arr = [];
 	$('#taxs :selected').each(function(){
		se += ((($(this).data('amount')) * 1000) / 1000);
		arr.push( $(this).data('amount') );
	});
	var er = 0;
	for (var i = arr.length - 1; i >= 0; i--) {
		er += ((arr[i] * 100) / 100);
	}
	console.log(er);
	console.log(se);
	$('#total_tax').text( er );

	// update each time user change the value
	update_tamount();
	update_balance();
});

////////////////////////////////////////////////////////////////////////////////////
// selecting series will auto populate comm and rate
// $('#custsel').change(function() {		<---- cant use it for append
$(document).on('change', '#custsel', function () {
	selectedOption = $('option:selected', this);
	var client = $('#client');
	var address = $('#address');
	var poskod = $('#poskod');
	var email = $('#email');
	var phone = $('#phone');

	$(client).text( selectedOption.data('client') );
	$(address).text( selectedOption.data('client_address') );
	$(poskod).text( selectedOption.data('client_poskod') );
	$(email).text( selectedOption.data('client_email') );
	$(phone).text( selectedOption.data('client_phone') );
});

////////////////////////////////////////////////////////////////////////////////////
// helper NaN
function num(obj) {
	var mystring = obj.value;
	if( !isNaN(mystring) == false ){
		mystring = 0;
	}
	return mystring;
}

////////////////////////////////////////////////////////////////////////////////////
// adding and removing invoice item
var add_button	= $(".add_field");
var wrapper		= $(".input_fields_wrap");
var x = 0;
$(add_button).click(function(){
	x++;
	$(wrapper).append(
		'<div class="row rowinvoice">' +
			'<div class="col-sm-12">' +
				'<div class="col-sm-1">' +
					'<div class="row">' +
						'<div class="col-sm-12">' +
							'<button class="btn btn-danger remove_field" type="button">-</button>' +
							'<input type="hidden" name="inv[' + x + '][id]" value="">' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-4">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group <?php echo ( count($errors->get('inv[][id_product]')) ) >0 ? 'has-error' : '' ?>">' +
							'<select name="inv[' + x + '][id_product]" class="series form-control">' +
								'<option value="">Choose Product</option>' +

								<?php $cate = App\ProductCategory::where(['active' => 1])->get(); ?>
								@foreach ($cate as $key)
									'<optgroup label="{!! $key->product_category !!}">' +
										<?php $pro = App\Product::where(['id_category' => $key->id, 'active' => 1])->get() ?>
										@foreach($pro as $r)
											'<option value="{!! $r->id !!}" data-commission="{!! $r->commission !!}" data-retail="{!! $r->retail !!}">{!! $r->product !!}</option>' +
										@endforeach
									'</optgroup>' +
								@endforeach
							'</select>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-2">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group {!! ( count($errors->get('inv[][commission]')) ) >0 ? 'has-error' : '' !!}">' +
							'<input <?=(auth()->user()->id_group == 1)? 'type="text"' : 'type="hidden"' ?> name="inv[' + x + '][commission]" class="comm form-control" placeholder="Commission (RM)" />' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-2">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group {!! ( count($errors->get('inv[][retail]')) ) >0 ? 'has-error' : '' !!}">' +
							'<input type="text" name="inv[' + x + '][retail]" class="rate form-control" placeholder="Retail (RM)"/>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-2">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group {!! ( count($errors->get('inv[][quantity]')) ) >0 ? 'has-error' : '' !!}">' +
							'<input type="text" name="inv[' + x + '][quantity]" class="quan form-control" placeholder="Quantity" />' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-1">' +
					'<div class="row">' +
						'<div class="col-sm-12">' +
							'<p class="text-right"><span class="total_price">0.00</span></p>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>'
	); //add input box
});

$(wrapper).on("click",".remove_field", function(e){
	//user click on remove text
	e.preventDefault();
	// $(this).parent().parent().parent().parent().parent('.rowinvoice').css({"color": "red", "border": "2px solid red"});
	$(this).parent().parent().parent().parent().parent('.rowinvoice').remove();

	// update total amount
	update_tamount();
	update_balance();
})

////////////////////////////////////////////////////////////////////////////////////
// selecting series will auto populate comm and rate
// $('.series').change(function() {		<---- cant use it for append
$(document).on('change', '.series', function () {
	selectedOption = $('option:selected', this);
	var comm = $(this).parent().parent().parent().parent().children().children().children().children('.comm');
	var rate = $(this).parent().parent().parent().parent().children().children().children().children('.rate');
	var quan = $(this).parent().parent().parent().parent().children().children().children().children('.quan');
	var total_price = $(this).parent().parent().parent().parent().children().children().children().children().children('.total_price');

	$(comm).val( selectedOption.data('commission') );
	$(rate).val( selectedOption.data('retail') );

	// check if its (Not A Number)
	// num( this );

	// update total_price
	// $(total_price).text( ((selectedOption.data('retail') * 10) * ($(quan).val() * 10)) / 100 );

	// console.log( num($(quan).val()) );
	// console.log( num( selectedOption.data('retail') ) );

	$(total_price).text( (((selectedOption.data('retail') * 10) * ( $(quan).val() * 10)) / 100).toFixed(2) );

	// update total amount
	update_tamount();
	update_balance();
});

////////////////////////////////////////////////////////////////////////////////////
// payment add and remove row
var add_buttonp	= $(".add_payment");
var wrapperp		= $(".payment_wrap");
var xp = 0;
$(add_buttonp).click(function(){
	xp++;
	$(wrapperp).append(
		'<div class="row rowpayment">' +
			'<div class="col-sm-12">' +
				'<div class="col-sm-1">' +
					'<div class="row">' +
						'<div class="col-sm-12">' +
							'<button class="btn btn-danger remove_payment" type="button">-</button>' +
							'<input type="hidden" name="pay[' + xp + '][id]" value="">' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-6">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group <?php echo ( count($errors->get('pay[][id_bank]')) ) >0 ? 'has-error' : '' ?>">' +
							'<select name="pay[' + xp + '][id_bank]" class="form-control">' +
								'<option value="">Choose Bank</option>' +
								<?php $cate = App\Banks::where(['active' => 1])->get(); ?>
								@foreach ($cate as $r)
								'<option value="{!! $r->id !!}" >{!! $r->bank !!}</option>' +
								@endforeach
							'</select>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-2">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group {!! ( count($errors->get('pay[][date_payment]')) ) >0 ? 'has-error' : '' !!}">' +
							'<input type="text" name="pay[' + xp + '][date_payment]" class="form-control date" placeholder="Date Payment"/>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-sm-3">' +
					'<div class="row">' +
						'<div class="col-sm-12 form-group {!! ( count($errors->get('pay[][amount]')) ) >0 ? 'has-error' : '' !!}">' +
							'<input type="text" name="pay[' + xp + '][amount]" class="pamount form-control" placeholder="Amount (RM)"/>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>'
	); //add input box
});
			
$(wrapperp).on("click",".remove_payment", function(e){
	//user click on remove text
	e.preventDefault();
	$(this).parent().parent().parent().parent().parent('.rowpayment').remove();

	// update total payment
	update_tpayment();
	update_balance();
})

////////////////////////////////////////////////////////////////////////////////////
// keyup on input rate to sum up all the price
$(document).on('keyup', '.rate', function () {
	var comm = $(this).parent().parent().parent().parent().children().children().children().children('.comm');
	var rate = $(this).parent().parent().parent().parent().children().children().children().children('.rate');
	var quan = $(this).parent().parent().parent().parent().children().children().children().children('.quan');
	var total_price = $(this).parent().parent().parent().parent().children().children().children().children().children('.total_price');

	// check if its (Not A Number)
	// console.log( num( this ) );

	// $(total_price).text( (($(rate).val() * 10) * ($(quan).val() * 10)) / 100 );
	$(total_price).text( (((num( this ) * 10) * ($(quan).val() * 10)) / 100).toFixed(2) );

	update_tamount();
	update_balance();
});

////////////////////////////////////////////////////////////////////////////////////
// keyup on input quan to sum up all the price
$(document).on('keyup', '.quan', function () {
	var comm = $(this).parent().parent().parent().parent().children().children().children().children('.comm');
	var rate = $(this).parent().parent().parent().parent().children().children().children().children('.rate');
	var quan = $(this).parent().parent().parent().parent().children().children().children().children('.quan');
	var total_price = $(this).parent().parent().parent().parent().children().children().children().children().children('.total_price');

	// check if its (Not A Number)
	// console.log( num( this ) );

	// $(total_price).text( (($(rate).val() * 10) * ($(quan).val() * 10)) / 100 );
	$(total_price).text( ((($(rate).val() * 10) * (num( this ) * 10)) / 100).toFixed(2) );

	update_tamount();
	update_balance();
});

////////////////////////////////////////////////////////////////////////////////////
// keyup on input pamount to sum up all the price
$(document).on('keyup', '.pamount', function () {
	update_tpayment();
	update_balance();
});

////////////////////////////////////////////////////////////////////////////////////
// helper total amount
function update_tamount() {
	var tax = $("#total_tax").text();
	var myNodelist = $(".total_price");
	var ssum = 0;
	var stsum = 0;
	for (var i = myNodelist.length - 1; i >= 0; i--) {
		// myNodelist[i].style.backgroundColor = "red";
		ssum = ((ssum * 100) + (myNodelist[i].innerHTML * 100)) / 100;	//make sure the process is accurate
		// console.log(ssum);
		stsum = ssum + (ssum * ((tax * 100) / 10000));
	}
	$('#total_amount').text( stsum.toFixed(2) );
}

////////////////////////////////////////////////////////////////////////////////////
// helper total payment
function update_tpayment() {
	var myNodelistp = $(".pamount");
	var psum = 0;
	for (var ip = myNodelistp.length - 1; ip >= 0; ip--) {
		// myNodelistp[ip].style.backgroundColor = "red";
		psum = ((psum * 10000) + (myNodelistp[ip].value * 10000)) / 10000;	//make sure the process is accurate
		// console.log(psum);
		// console.log(myNodelistp[ip].value);
	}
	$('#total_payment').text( psum.toFixed(2) );
}

////////////////////////////////////////////////////////////////////////////////////
// helper balance
function update_balance() {
	var ta = $('#total_amount');	// amount invoice
	var tp = $('#total_payment');
	var bal = ( ( $(tp).text() * 10000 ) - ( $(ta).text() * 10000 ) )/10000;
	
	// console.log($(tp).text());
	if (bal == 0) {
		$('#balance').text( bal.toFixed(2) ).css({"color": "blue"});
	} else {
		if (bal < 0) {
			$('#balance').text( bal.toFixed(2) ).css({"color": "red"});
		} else {
			$('#balance').text( bal.toFixed(2) ).css({"color": "green"});
		}
	}
}

////////////////////////////////////////////////////////////////////////////////////

@endsection