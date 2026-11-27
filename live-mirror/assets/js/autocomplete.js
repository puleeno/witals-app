// autocomplet : this function will be executed every time we change the text

function autocomplet(keyword) {
	var min_length = 2; // min caracters to display the autocomplete
	//var keyword = $(this).val();

	if (keyword.length >= min_length) {
		$.ajax({
			url: '/index4.php?page=autocomplete',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('.searchresult').show();
				$('.searchresult').html(data);
			}
		});
	} else {
		$('.searchresult').hide();
	}
}
$(function(){
$(document).click(function(){
	$('.searchresult').hide();	
});

$('.keysearch, .keysearch-mobile').keyup(function() {
  if($(this).val()!='') autocomplet($(this).val()); 	
  
});

$('.keysearch, .keysearch-mobile').click(function(){
	if($(this).val()!='') autocomplet($(this).val()); 	
});
$('.keysearch, .keysearch-mobile').focus(function(){
	if($(this).val()!='') autocomplet($(this).val()); 	
});
$('.searchresult,.searchform,.keysearch').click(function(e){
	e.stopPropagation();	
})
});
// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('.keysearch').val(item);
	// hide proposition list
	$('.searchresult').hide();
}




