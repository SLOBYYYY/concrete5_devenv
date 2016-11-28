$(document).ready(function() {

	$('#submit-form').click(function(){
		$(this).closest("form").submit();
		return false;
	});
	
	$('#save-test').click(function(){
		$('<input>').attr({
type: 'hidden',
id: 'save_test',
name: 'save_test',
value: '1'
		}).appendTo('form.exam');
		$('form.exam').submit();

	});
	
	$('#delete-test').click(function(){
	var r=confirm("Are you sure you want to delete this test?");
	if (r==true){
		$('<input>').attr({
			type: 'hidden',
			id: 'delete_test',
			name: 'delete_test',
			value: '1'
		}).appendTo('form.exam');
		$('form.exam').submit();
	} else {
	return false;
	}
	});
	
	
	$('a.submit-roll').click(function(){
		
		$(this).closest("form").submit();
		return false;
	});
	
	$('a.grade-roll').click(function(){
		$(this).closest("form").submit();
		$(this).removeClass('grade-roll');
		return false;
	});
	
	$('a.update-roll').click(function(){
		$('input[name=action]').val('update');
		$(this).closest("form").submit();
		return false;
	});
	
	
	$('input#same_billing').change(function(){
		$('.billing-address').toggle();
	});
	
	$('form#purchase :input').change(function(){
		$('fieldset.details').show();
		var input = $(this);
		
		if ($(input).hasClass('exclusive')){
			var featureGroup = $(this).attr('name');
			if ($(this).attr('checked')){
				$('input[name='+featureGroup+']').attr('checked',false);
				$('input[name='+featureGroup+']').attr('disabled',true);
				$(this).attr('disabled',false);
				$(this).attr('checked',true);
			} else {
				$('input[name='+featureGroup+']').attr('disabled',false);
			}
			
		}
		
		if ($(input).attr('name') == 'type'){
			var type_sel = $(input).val();
			var exam = type_sel.split("_")[0]
			$('.feature-list').hide();
			$('.description').hide();
			$('#'+exam+'_description').show();
			$('#'+exam+'_feature').show();
			$('.feature-list input').attr('checked', false);
			$('.feature-list input').attr('disabled', false);
			$('.feature-list-off').hide();
			$('.shipping-list').hide();
			$('#'+exam+'_shipping').show();
			$('.shipping-list input').attr('checked', false);
			$('input:radio[name='+exam+'_shipping]:first').attr('checked', true);
			$('.shipping-list-off').hide();
		}
		
		var total = parseFloat(0.00);
		$('form#purchase input:checked').each(function(index){
			var sel_name = $(this).val();
			var price = $(this).next('span.' + sel_name).text();
			price = price.substr(1);
			if (price != "ree"){
				total = total + parseFloat(price);
			}
			total = Math.round(total*100)/100;
		});
		$('#totalchargedisp').text(total);
		$('input[name=total]').val(total);
	});
	
	
	$('.shipping-table :input').change(function(){
		var featureGroup = $(this).attr('name');
		if (featureGroup != "type[]"){
			$(this).parent().parent().find(".price-column").addClass("hidden");
			if ($(this).attr('checked')){
				$('input[name='+featureGroup+']').attr('checked',false);
				$(this).attr('checked',true);
				$(this).parent().next().removeClass("hidden");
			} else {
				if($(this).hasClass("exclusive")){
					$('input[name='+featureGroup+']').first().attr('checked',true);
					$('input[name='+featureGroup+']').first().parent().next().removeClass("hidden");
				}
			}
		} else {
			if ($(this).attr('checked')){
				$(this).parent().parent().find(".price-column").removeClass("hidden");
				$(this).parent().parent().find(".features").show();
				$(this).parent().parent().find(".shipping").show();
				$(this).parent().parent().find(".features").find(".price-column").addClass("hidden");
				$(this).parent().parent().find(".features").find('input:checked').first().parent().next().removeClass("hidden");
				$(this).parent().parent().find(".shipping").find(".price-column").addClass("hidden");
				$(this).parent().parent().find(".shipping").find('input[name='+featureGroup+']:checked').first().parent().next().removeClass("hidden");
			} else {
				$(this).parent().parent().find(".price-column").addClass("hidden");
				$(this).parent().parent().find(".features").hide();
				$(this).parent().parent().find(".shipping").hide();
			}
		}
		total_shopping_cart();
	});
});


function total_shopping_cart(){
	var total = 0;
	$(".price-column:visible").each(function(){
		var cost = $(this).html();
		var number = Number(cost.replace(/[^0-9\.]+/g,""));
		total = total + number;
	});
	$("span#total").html(total);
}; 	



