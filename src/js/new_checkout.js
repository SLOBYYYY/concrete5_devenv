$(document).ready(function() {

	$('.exam-type').each(function(){
	var exam = $(this);
	var cost = total_order(exam);
	exam.find('.price-column').first().html("$"+ cost);
	total_all_orders();
	});

	$(".toggle_it").click(function(){
		var options = $(this).parent().next('div');
		var button = $(this).find('span');
		if (options.hasClass("hidden")){
		options.removeClass("hidden");
		button.removeClass("expand");
		button.addClass("contract");
		} else {
		options.addClass("hidden");
		button.addClass("expand");
		button.removeClass("contract");
		}
		return false;
	});
	
		$(".remove").click(function(){
		var exam = $(this).closest('.exam-type');
		var agree = confirm("Do you want to delete this item from your shopping cart?");
		if (agree){
		exam.remove();
		total_all_orders();
		var lineid = exam.attr('id');
		$.post('', 
			{action: 'delete', lineid: lineid},
			function(r) {
			},'json')
		} else {
		return false;
		}
		return false;
	});
	
	$("input.quantity").keyup(function(evt){
	var newquan = $(this).val();
         if (!isNormalInteger(newquan) || newquan == "0"){
			return false;
			} else {
		
		var exam = $(this).closest('.exam-type');	
		var cost = total_order(exam);
		$(this).parent().parent().find('.price-column').html("$"+ cost);
		exam.find('input.cost').val(cost);
		total_all_orders();
		}
	});
	
	$("input.quantity").change(function(evt){
		if (!isNormalInteger($("input.quantity").val())){
            alert("Quantity can only be a number");
			$("input.quantity").val(1);
			}
		var exam = $(this).closest('.exam-type');
		var quan = $(this).val();
		if (quan ==0 ){
		var agree = confirm("Do you want to delete this item from your shopping cart?");
		if (agree){
		exam.remove();
		total_all_orders();
		var lineid = exam.attr('id');
		$.post('', 
			{action: 'delete', lineid: lineid},
			function(r) {
				if (r.message){
					alert(r.message);
				}
			},'json')
		} else {
		$(this).val("1");
		}
		}
		
		var exam = $(this).closest('.exam-type');
		var cost = total_order(exam);
		$(this).parent().parent().find('.price-column').html("$"+ cost);
		exam.find('input.cost').val(cost);
		total_all_orders();
	});
	
	$("input.exclusive").click(function(event){

		var featureGroup = $(this).parent();
		if ($(this).attr('checked')){
		featureGroup.find("input").attr('checked',false); // uncheck all boxes in the group
		$(this).attr('checked',true);
		} else {
		if (featureGroup.hasClass("shipping-options")){
		featureGroup.find("input").first().attr('checked',true);
		}
		}
		var exam = $(this).closest('.exam-type');
		var cost = total_order(exam);
		exam.find('.price-column').first().html("$"+ cost);
		exam.find('input[name="cost"]').val(cost);
		total_all_orders();

	});
	
});

function total_order(exam){
	var featsum = 0;
	exam.find('.type-title').find('.added_courses').html('');
	var base_price = exam.find('.type-title').find('.cost').text();
	var shipping_price_input = exam.find('.shipping-options').find('input:checked')
	var shipping_price = shipping_price_input.next().find('.cost').text();
	var feature = exam.find('.feature-options').find('input:checked').next().find('.feature-name');

	exam.find('.feature-title').find('b').html("Course Includes:");
	$(feature).each(function( index ) {
	
	var feature_name = $(this).html();
	var feature_name =feature_name.replace("(No Test Required)",""); 
	exam.find('.type-title').find('.added_courses').append('<li>'+feature_name+'</li>');
	var feature_price = $(this).find('.cost');
		var thisfeat = $(feature_price).text();
		var feat = Number(thisfeat.replace(/[^0-9\.]+/g,""));
		
		if (feat > 100){
		exam.find('.exam-includes').find('h2').html("Each Course Includes");
		}
		
		featsum = featsum + feat;
	});
	var quantity = exam.find('input.quantity').val();
	var base = Number(base_price.replace(/[^0-9\.]+/g,""));
	var ship = Number(shipping_price.replace(/[^0-9\.]+/g,""));
	
	var quan = Number(quantity.replace(/[^0-9\.]+/g,""));
	total = quan*(base + ship + featsum);
	var money = parseFloat(Math.round(total * 100) / 100).toFixed(2);
	return money;
	}
	
function total_all_orders(){
	var total = 0;
	var quant = 0;
	$('.exam-type').each(function(){
		var this_quant = Number($(this).find('input.quantity').val().replace(/[^0-9\.]+/g,""));
		quant = quant + this_quant;
		var exam_cost = $(this).find('.price-column').first().text();
		var cost = Number(exam_cost.replace(/[^0-9\.]+/g,""));
		total = total + cost;
	});
	var daa = $('input[name="discount"]').val();
		if(quant>0){
	var discount = get_discount(quant);
		if (discount){
		$('.discount-line').removeClass('hidden');
		$('.sub-total-line').removeClass('hidden');
		var disc_percent = (discount * 100).toFixed(0);
		var discount_cost = (total * discount).toFixed(2);
		$('.savings').html("-$"+discount_cost);
		$('.discpercent').html(disc_percent);
		$('.subtotal').html(total);
		total = (total - discount_cost).toFixed(2);
		total = parseFloat(Math.round(total * 100) / 100).toFixed(2);
		$('.price-column').addClass('strike');
		} else {
		$('.price-column').removeClass('strike');
		$('.discount-line').addClass('hidden');
		$('.sub-total-line').addClass('hidden');
		}
        total = parseFloat(total).toFixed(2);
		$('.grand_total').html(total);
		$('input[name="order_total"]').val(total);
		$('.order_pricing').html("$"+total);
	} else {
	$("form.acls-shopping-cart").html("<h2>Your Shopping Cart is Empty</h2><p>Please return to the <a href='/'>Registration Page</a> to start your checkout again.</p>"); 
	}
}

function isNormalInteger(str) {
    var n = ~~Number(str);
    return String(n) === str && n >= 0;
}
