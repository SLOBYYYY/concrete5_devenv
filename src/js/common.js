
//functionality to make checkboxes behave like radio buttons
$('.exclusive-checkboxes input[type|="checkbox"]').click(function(){
	if ($(this).prop( "checked" ) ){
		$(this).closest('.exclusive-checkboxes').find('input[type|="checkbox"]').prop("checked",false);
		$(this).prop("checked",true);
	}
})

//functionality to make checkboxes behave like radio buttons
$('.bls-eligible input[type|="checkbox"]').click(function(){
	if ($(this).prop( "checked" ) ){
		if($('.bls-options input[type|="checkbox"]:checked').length > 0){
        } else {
            $('.bls-options').hide().filter(':last').show();
            $(this).closest('.bls-eligible').next('.bls-options').show();
        }
	} else {
        var blsSelection = $(this).closest('.bls-eligible').next('.bls-options').find('input[type|="checkbox"]:checked');
        if(blsSelection.length > 0){
            var blsSelected = $(blsSelection).val();
            $(this).closest('.bls-eligible').next('.bls-options').find('input[type|="checkbox"]:checked').prop("checked",false);
            $(this).closest('.bls-eligible').next('.bls-options').hide();
            var eligibleSelected = $('.bls-eligible input[type|="checkbox"]:checked').filter(":first");
            if(eligibleSelected.length>0){
                $(eligibleSelected).closest('.bls-eligible').next('.bls-options').show();
                $(eligibleSelected).closest('.bls-eligible').next('.bls-options').find('input[value|="'+blsSelected+'"]').prop("checked",true);
            } else {
                $('.bls-options:last').find('input[value|="'+blsSelected+'"]').prop("checked",true);
            }
        } else {
            $(this).closest('.bls-eligible').next('.bls-options').hide();
        }
    }
})
$('.bls-options:last input[type|="checkbox"]').click(function(){
	if ($(this).prop( "checked" ) ){
        $('.bls-options').find('input[type|="checkbox"]:checked').prop("checked",false);
        $('.bls-options').hide().filter(":last").show();
        $(this).prop("checked",true);
    } else {
        var eligibleSelected = $('.bls-eligible input[type|="checkbox"]:checked').filter(":first");
        if(eligibleSelected.length>0){
                $(eligibleSelected).closest('.bls-eligible').next('.bls-options').show();
            }
    }
});

$(function () {
  $('[data-toggle="popover"]').popover(
  {'trigger':'hover',
  'placement':'top',
  'html':1,
  'content': function(){
      var product = $(this).parent().find('input').val();
      var productParts = product.split("_");
      var course = productParts[0].toUpperCase();
      var type = productParts[1];
      if(type == 1){
          var typeVerbose = "Certification";
          var credits = 8;
      } else {
          var typeVerbose = "Recertification";
          var credits = 4;
      }
      if(course=="BLS"){
          credits = credits/2;
      }
      
      return "<div class='popoverBody'><ul><li>Online "+typeVerbose+" & Practice Exams</li><li>Online "+course+" Provider Manual Included</li><li>Free Instant "+course+" Provider Card</li><li>Unlimited Final Exam Retakes</li><li>"+credits+" CEH with No Skills Test Required</li></ul></div>";
      
  },
  'title': function(){
    var includesText = $(this).text();
    var courseInfo = $(this).parent().text();
    var trimmedInfo = courseInfo.substr(0,courseInfo.indexOf(includesText));
    return "<div class='popoverTitle'><h3>"+trimmedInfo+"</h3></div>";
  }
  }
  )
})