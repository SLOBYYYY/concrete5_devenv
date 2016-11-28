$(document).ready(function() {
    
    $(document).on('click','#addThresh', function(event){
        event.preventDefault();
        $('.quantity-row:last').clone(true)
                .find("input:text").val("").end()
                .insertAfter('.quantity-row:last');
    })
    
    $(document).on('click','.deleteRow', function(event){
        event.preventDefault();
        $(this).closest('.quantity-row').remove();
    })
    
    
    
    $('input[name|="dType"').change(function(){
       switch ($(this).val()){
            case "percent":
            $('.quant-percent').html('% off');

            $('.quant-dollar').hide();
            break;
            case "amount":
            $('.quant-percent').html('off');
            $('.quant-dollar').show();
            break;
            case "bundle":
            $('.quant-percent').html('total');
            $('.quant-dollar').show();
            break;
            
       }       
    });
    
    $('.comparison-select').click(function(){
            var comparator = $(this).data('value');
            var group = $(this).closest('.drop-button');
            $(group).find('input[name|="comparison[]"]').val(comparator);
            var symbol = "";
            switch(comparator){
                case "equal":
                symbol = '=';
                break;
                case "greater":
                symbol = '>';
                break;
                case "less":
                symbol = '<';
                break;
            }
            $(group).find('button').html(symbol);
    });
    
    
         $('.deleteQuestion').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'deleteQuestion'}),
				function(r) { 
                $(chosenbutton).closest('tr').hide();
                },'json');
    });
    
    
    $('#addQuestion').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'addQuestion'}),
				function(r) { 
                location.reload();
                },'json');
    });
    
    
    $('.updateQuestion').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'updateQuestion'}),
				function(r) {
				if (r.message) {
					closeContext(chosenbutton);
			}
			},'json');	
    }); 
    
     $('.deleteShip').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'deleteShip'}),
				function(r) { 
                $(chosenbutton).closest('tr').hide();
                },'json');
    });
    
    
    $('#addShip').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'addShip'}),
				function(r) { 
                location.reload();
                },'json');
    });
    
    
    $('.updateShip').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'updateShip'}),
				function(r) {
				if (r.message) {
					closeContext(chosenbutton);
			}
			},'json');	
    });   
    
    $('.deleteType').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'deleteType'}),
				function(r) { 
                $(chosenbutton).closest('tr').hide();
                },'json');
    });
    
    
    $('#addType').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'addType'}),
				function(r) { 
                location.reload();
                },'json');
    });
    
    
    $('.updateType').click(function(event){
			event.preventDefault();
			var chosenbutton = this;
			var typeData = $(this).closest('tr').find(":input").serialize();
			console.log(typeData);
            $.post("",typeData+'&'+$.param({'action':'updateType'}),
				function(r) {
				if (r.message) {
					closeContext(chosenbutton);
			}
			},'json');	
    });
    
    $('#courseSelect').change(function(){
       var tablePrefix = $(this).val();
       window.location.search = '?course=' + tablePrefix;
    });

	$(".show_orders").click(function(){
			var this_row = $(this).parent().parent().parent().parent();
			this_row.next('.order-row').toggle();
			if (this_row.next('.order-row').is(":visible")) {
			$(this).find('i').removeClass('icon-chevron-down');
			$(this).find('i').addClass('icon-chevron-up');
			} else {
			$(this).find('i').removeClass('icon-chevron-up');
			$(this).find('i').addClass('icon-chevron-down');
			}
		return false;
		});
		
		$('.delete_user').click(function(){
		var answer = confirm("Are you sure you want to delete the user and all associated orders and exams?");
			if (answer) {
				
				var url = $(this).attr('href');
				var member_row = $(this).parent().parent();
				var uID = $('td:first', member_row).text();
				$.post(url, 
				{user: uID, action: 'delete_user'}

				);
			}else{
			return false;
			}

		
		});
	
$('.edit_type').click(function(){
			var type_row = $(this).parent().parent().parent();
			type_row.find('.display').hide();
			type_row.find('.edit').show();
			type_row.find('.success').show();
			$(this).parent().hide();
		return false;
		});

	$('.delete_type').click(function(){
		var url = $(this).attr('href');
		var type_row = $(this).parent().parent().parent();
		var name = type_row.find('.type_name.display').text();
		$.post(url, 
				{name: name, action: 'delete_type'},
				function(r) {
				window.location.href = url;
				}
				);
		


	});
	
	$('.save_type').click(function(){	
			var url = $(this).attr('href');
			var type_row = $(this).parent().parent();
			var raw_typeID = type_row.attr('id');
			var typeID = raw_typeID.split("_")[1];
			var name = type_row.find('.type_name input').val();
			var cost = type_row.find('.type_cost input').val();
			var pquest = type_row.find('.type_pquest input').val();
			var fquest = type_row.find('.type_fquest input').val();
			var pgrade = type_row.find('.type_pgrade input').val();
			var retakes = type_row.find('.type_retakes input').val();
			var credits = type_row.find('.type_credits input').val();
			var save_button = $(this);
			var action_button = $(this).next();
			action_button.show();
			save_button.hide();

			$.post(url, 
				{typeID: typeID, name: name, cost: cost, practiceQuestions: pquest, finalQuestions: fquest, passingGrade: pgrade, retakes: retakes, credits: credits, action: 'edit_type'},
				function(r) {
				if (r.message) {
			$('.alert-message').children('p').html(r.message);
			$('.alert-message').removeClass('error');
			$('.alert-message').addClass('info').fadeIn(400);
			type_row.find('.type_name.display').text(name);
			type_row.find('.type_cost.display').text('$'.cost);
			type_row.find('.type_pquest.display').text(pquest);
			type_row.find('.type_fquest.display').text(fquest);
			type_row.find('.type_pgrade.display').text(pgrade + '%');
			type_row.find('.type_retakes.display').text(retakes);
			type_row.find('.type_credits.display').text(credits);
			}
		if (r.error) {
			$('.alert-message').children('p').html(r.error);
			$('.alert-message').removeClass('info');
			$('.alert-message').addClass('error').fadeIn(400);
			} else {
			type_row.find('.display').show();
			type_row.find('.edit').hide();
			}
			},'json');
		return false;
	});
	
	$('.edit_shipping').click(function(){
			var shipping_row = $(this).parent().parent().parent();
			shipping_row.find('.display').hide();
			shipping_row.find('.edit').show();
			shipping_row.find('.success').show();
			$(this).parent().hide();
		return false;
		});

	$('.delete_shipping').click(function(){
		var url = $(this).attr('href');
		var shipping_row = $(this).parent().parent().parent();
		var name = shipping_row.find('.shipping_name.display').text();
		$.post(url, 
				{name: name, action: 'delete_shipping'},
				function(r) {
				window.location.href = url;
				}
				);
		

	});
	
	$('.save_shipping').click(function(){	
			var url = $(this).attr('href');
			var shipping_row = $(this).parent().parent();
			var raw_shippingID = shipping_row.attr('id');
			var shippingID = raw_shippingID.split("_")[1];
			var name = shipping_row.find('.shipping_name input').val();
			var cost = shipping_row.find('.shipping_cost input').val();
			var save_button = $(this);
			var action_button = $(this).next();
			action_button.show();
			save_button.hide();

			$.post(url, 
				{shippingID: shippingID, name: name, cost: cost, action: 'edit_shipping'},
				function(r) {
				if (r.message) {
			$('.alert-message').children('p').html(r.message);
			$('.alert-message').removeClass('error');
			$('.alert-message').addClass('info').fadeIn(400);
			shipping_row.find('.shipping_name.display').text(name);
			shipping_row.find('.shipping_cost.display').text('$' + cost);
			}
		if (r.error) {
			$('.alert-message').children('p').html(r.error);
			$('.alert-message').removeClass('info');
			$('.alert-message').addClass('error').fadeIn(400);
			} else {
			shipping_row.find('.display').show();
			shipping_row.find('.edit').hide();
			}
			},'json');
		return false;
	});
	
		$('.edit_feature').click(function(){
			var feature_row = $(this).parent().parent().parent();
			feature_row.find('.display').hide();
			feature_row.find('.edit').show();
			feature_row.find('.success').show();
			$(this).parent().hide();
		return false;
		});

	$('.delete_feature').click(function(){
		var url = $(this).attr('href');
		var feature_row = $(this).parent().parent().parent();
		var name = feature_row.find('.feature_name.display').text();
		$.post(url, 
				{name: name, action: 'delete_feature'},
				function(r) {
				window.location.href = url;
				}
				);
		

	});
	
	$('.save_feature').click(function(){	
			var url = $(this).attr('href');
			var feature_row = $(this).parent().parent();
			var raw_featureID = feature_row.attr('id');
			var featureID = raw_featureID.split("_")[1];
			var name = feature_row.find('.feature_name input').val();
			var cost = feature_row.find('.feature_cost input').val();
			var featureGroup = feature_row.find('.feature_featureGroup input').val();
			var save_button = $(this);
			var action_button = $(this).next();
			action_button.show();
			save_button.hide();

			$.post(url, 
				{featureID: featureID, name: name, cost: cost, featureGroup: featureGroup, action: 'edit_feature'},
				function(r) {
				if (r.message) {
			$('.alert-message').children('p').html(r.message);
			$('.alert-message').removeClass('error');
			$('.alert-message').addClass('info').fadeIn(400);
			feature_row.find('.feature_name.display').text(name);
			feature_row.find('.feature_cost.display').text('$' + cost);
			feature_row.find('.feature_featureGroup.display').text(featureGroup);
			}
		if (r.error) {
			$('.alert-message').children('p').html(r.error);
			$('.alert-message').removeClass('info');
			$('.alert-message').addClass('error').fadeIn(400);
			} else {
			feature_row.find('.display').show();
			feature_row.find('.edit').hide();
			}
			},'json');
		return false;
	});
	
	$('.edit_question').click(function(){
			var answer_row = $(this).parent().parent().parent();
			answer_row.find('.display').hide();
			answer_row.find('.edit').show();
			answer_row.find('.success').show();
			$(this).parent().hide();
		return false;
		});
		
		$('.delete_answer').click(function(){
		var answer_div = $(this).parent().parent();
		var answer_list = answer_div.parent();
		answer_div.remove();
		answer_list.find('input[name^=correct]').each(function(idx,item){
		$(this).val(idx);
		});
		return false;
		});
		
		$('.add_answer').click(function(){
		var answer_list = $(this).parent();
		var new_answer = $(this).prev().clone(true)
		new_answer.find('input').val("");
		new_answer.appendTo(answer_list);
		$(this).appendTo(answer_list);
		var answer_list = new_answer.parent();
		answer_list.find('input[name^=correct]').each(function(idx,item){
		$(this).val(idx);
		});
		return false;
		});
		
		$('.save_question').click(function(){
		var url = $(this).attr('href');
		var question_row = $(this).parent().parent();
		var data = $(question_row).find('input, textarea').serialize();
		var save_button = $(this);
		var action_button = $(this).next();
		action_button.show();
		save_button.hide();
			
		$.ajax({
			type: 'POST', 
			url: url,
			data: data,
			dataType: 'json',
			success: function(r) {
				if(r.add) {
				location.reload(true);
				}
				if (r.message) {
			$('.alert-message').children('p').html(r.message);
			$('.alert-message').removeClass('error');
			$('.alert-message').addClass('info').fadeIn(400);
			question_row.find('.question_question.display').text(r.question);
			}
		if (r.error) {
			$('.alert-message').children('p').html(r.error);
			$('.alert-message').removeClass('info');
			$('.alert-message').addClass('error').fadeIn(400);
			} else {
			question_row.find('.display').show();
			question_row.find('.edit').hide();
			}
		  }
		});
		return false;
		});
		
		$('.delete_question').click(function(){
		var agree=confirm("Are you sure you want to delete?");
		if (agree){
		var url = $(this).attr('href');
		var question_row = $(this).parent().parent();
		var question = $(this).parent().parent().parent();
		var qid = question_row.find('input').val();
		$.post(url, 
				{qid: qid, action: 'delete_question'},
				function(r) {
				question.remove();
				}
				);
		
	} else {
	return false;
	}
	});
	
	
	$(".actionSelect").change(function() {
	
		var action = $(this).val();
		var actionform = $(this).closest("form");
		var dialog_owner = actionform.attr("id");
		$("#dialog_owner").val(dialog_owner);
		
		if (action == "fulfill"){
		$(this).closest("form").submit();
		}
		
		if (action == "reship"){
		$(this).closest("form").submit();
		}
		
		if (action == "unreship"){
		$(this).closest("form").submit();
		}
		
		if (action == "retry"){
			jQuery.fn.dialog.open({
			  title: 'Are you sure?',
			  element: '#retry_dialog',
			  width: 220,
			  modal: true,
			  height: 90,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
			}
			
		
		if (action == "unfulfill"){
			jQuery.fn.dialog.open({
			  title: 'Are you sure?',
			  element: '#unfulfill_dialog',
			  width: 220,
			  modal: true,
			  height: 50,
			  onClose: function() {
				 //actionform.submit();
			  }
			});
		
		}
		
		if (action == "delete"){
			jQuery.fn.dialog.open({
			  title: 'Are you sure?',
			  element: '#delete_dialog',
			  width: 220,
			  modal: true,
			  height: 50,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
		
		}
		
		if (action == "pass"){
			jQuery.fn.dialog.open({
			  title: 'Are you sure?',
			  element: '#pass_dialog',
			  width: 220,
			  modal: true,
			  height: 90,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
		
		}
		
		if (action == "refund"){
			jQuery.fn.dialog.open({
			  title: 'Refund this Order',
			  element: '#refund_dialog',
			  width: 300,
			  modal: true,
			  height: 300,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
		
		} 
		
        if (action == "change_date"){
			jQuery.fn.dialog.open({
			  title: 'Change Pass Date',
			  element: '#change_date_dialog',
			  width: 300,
			  modal: true,
			  height: 300,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
		
		}
        
        if (action == "swap_course"){
			jQuery.fn.dialog.open({
			  title: 'Swap Course with Another',
			  element: '#swap_dialog',
			  width: 300,
			  modal: true,
			  height: 300,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
		
		}
        
        if (action == "add"){
			jQuery.fn.dialog.open({
			  title: 'Add New Course',
			  element: '#add_dialog',
			  width: 300,
			  modal: true,
			  height: 300,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
		
		}
        
		if (action.substring(0, 4) == "add_") {
		jQuery.fn.dialog.open({
			  title: 'Add This Feature',
			  element: '#feature_dialog',
			  width: 300,
			  modal: true,
			  height: 300,
			  onClose: function() {
			   //actionform.submit();
			  }
			});
			}

		
	
	});
	
	$().change (function(){
	
	});
	


		
});

function closeContext(context){
	$(context).closest('tr').animateHighlight("#daf7e2",1000);
	return false;
}

orderaction = function() {
    var order_id = $("#dialog_owner").val();
	$("#" + order_id).submit();
}; 

function copy_to_hidden(form_element){
    var order_id = $("#dialog_owner").val();
    var elem_id = $(form_element).attr("id");
    var elem_val = $(form_element).val();
    $('<input>').attr({
    type: 'hidden',
    id: elem_id,
    name: elem_id,
    value: elem_val
}).appendTo("#" + order_id);
}


add_form_element = function(form_element) {
	var order_id = $("#dialog_owner").val();
	var elem_id = $(form_element).attr("id");
	$("#" + order_id).find("#" + elem_id).remove();
	$(form_element).clone(false).appendTo("#" + order_id).hide();
}

$.fn.animateHighlight = function (highlightColor, duration) {
        var highlightBg = highlightColor || "#FFFF9C";
        var animateMs = duration || "fast"; // edit is here
        var originalBg = this.css("background-color");

        if (!originalBg || originalBg == highlightBg)
            originalBg = "#FFFFFF"; // default to white

        jQuery(this)
            .css("backgroundColor", highlightBg)
            .animate({ backgroundColor: originalBg }, animateMs, null, function () {
                jQuery(this).css("backgroundColor", originalBg); 
            });
    };