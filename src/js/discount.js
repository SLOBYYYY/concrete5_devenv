function get_discount(quantity){
	var discount = 0;
	if (quantity >= 5){
	discount = .10;
	}
	if (quantity >= 10){
	discount = .15;
	}
	
	if (quantity >= 25){
	discount = .20;
	}
	
	if (quantity >= 50){
	discount = .25;
	}
	
	if (quantity >= 100){
	discount = .30;
	}
	$('.exam-type').each(function(){
		var features;
		var boxes = $(this).find('.feature-options :checked');
		var features = boxes.length;
		if(features >= 2){
		var disc = $('input[name="discount"]').val();
		switch (disc) {
			case "seaphecc":
				discount = .20;
			break;
			
			case "aremt":
				discount = .20;
			break;
			case "pediatrix":
				if(features > 2){
				discount = .46601941747572816;
				}
			break;
			case "aa":
				if(features > 2){
				discount = .46601941747572816;
				}
			break;
		}
		}
	});
	
	return discount;
	}