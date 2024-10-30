var $=jQuery.noConflict();
var bmpajaxurl=$('meta[name="bmp_adminajax"]').attr('content');
$('#auto_add_bmp_user_fill').click(function(e){
	e.preventDefault();
	
	var number=$('#number').val();
	var sponsor=$('#sponsor').val();
	var epin=$('#epin').val();
	var position=$('#position').val();

	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_auto_add',
	        'number': number,
	        'sponsor': sponsor,
	        'epin': epin,
	        'position': position,
	    },
	    success: function (data) {
	    	$('.bmp_username_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_username_message').html(obj.message);
	    		$('#bmp_username').val('');
	    	} else{
	    		$('.bmp_username_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});
});


// user name exist check
$('#bmp_username').blur(function(){

	var username=$('#bmp_username').val();

	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_username_exist',
	        'username': username,
	    },
	    success: function (data) {
	    	$('.bmp_username_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_username_message').html(obj.message);
	    		$('#bmp_username').val('');
	    	} else{
	    		$('.bmp_username_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

});
// user name exist check
$('#bmp_position').blur(function(){
	var position=$('#bmp_position').val();
	var sponsor=$('#bmp_sponsor_id').val();

	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_position_exist',
	        'position': position,
	        'sponsor': sponsor,
	    },
	    success: function (data) {
	    	$('.bmp_position_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_position_message').html(obj.message);
	    		$('#bmp_position').val('');
	    	} else{
	    		$('.bmp_position_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

});



// user email exist check
$('#bmp_email').blur(function(){

	var email=$('#bmp_email').val();
	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_email_exist',
	        'email': email,
	    },
	    success: function (data) {
	    	$('.bmp_email_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_email_message').html(obj.message);
	    		$('#bmp_email').val('');
	    	} else{
	    		$('.bmp_email_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

});


// user epin exist check
$('#bmp_epin').blur(function(){

	var epin=$('#bmp_epin').val();
	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_epin_exist',
	        'epin': epin,
	    },
	    success: function (data) {
	    	$('.bmp_epin_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_epin_message').html(obj.message);
	    		$('#bmp_epin').val('');
	    	} else{
	    		$('.bmp_epin_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

});// update epin
$('#epin_value').blur(function(){

	var epin=$('#epin_value').val();
	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_epin_exist',
	        'epin': epin,
	    },
	    success: function (data) {
	    	$('.bmp_epin_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_epin_message').html(obj.message);
	    		$('#bmp_epin').val('');
	    	} else{
	    		$('.bmp_epin_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

});

$('#bmp_join_epin').blur(function(){
    
	var epin=$('#bmp_join_epin').val();	 
	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_epin_exist',
	        'epin': epin,
	    },
	    success: function (data) {
	    	$('.bmp_user_success_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_epin_join_message').html(obj.message);
	    		$('#bmp_join_epin').val('');
	    	} else{
                $('.bmp_epin_join_message').html(obj.message);	    		
	    	}
            return false;
	    }
	});
 

});
// user password exist check
$('#bmp_confirm_password').blur(function(){

	var password=$('#bmp_password').val();
	var confirm_password=$('#bmp_confirm_password').val();

	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_password_validation',
	        'password': password,
	        'confirm_password': confirm_password,
	    },
	    success: function (data) {
	    	$('.bmp_confirm_password_message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==false){
	    		$('.bmp_confirm_password_message').html(obj.message);
	    		$('#bmp_confirm_password').val('');
	    		$('#bmp_password').val('');
	    	} else {
	    		$('.bmp_confirm_password_message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

});


// Register form submit

$("#bmp_register_form").submit(function(e) {
	e.preventDefault(); 
    var form = $(this);
    var postdata=form.serialize();
    $.ajax({
           type: "POST",
           url: bmpajaxurl,
           data: postdata,
           success: function(data)
           {
           	var obj = $.parseJSON(data);
           	if(obj.status==false){
           		$.each(obj.error , function (key, value) {
				    $('.'+key).html('<span style="color:red;">'+value+'</span>');
				});

           	} else{
           		$('#bmp_user_success_message').html(obj.message);
           		$('#bmp_register_form').remove();
           	}

           }
         });
     return false;
});

$("#bmp_join_network_form").submit(function(e) {
	e.preventDefault(); 
    var join_form = $(this);
    var postjoindata=join_form.serialize();
    $.ajax({
           type: "POST",
           url: bmpajaxurl,
           data: postjoindata,
           success: function(data)
           {
           	var obj = $.parseJSON(data);
           	if(obj.status==false){
           		$.each(obj.error , function (key, value) {
				    $('.'+key).html('<span style="color:red;">'+value+'</span>');
				});

           	} else{
           		$('#bmp_user_success_message').html(obj.message);
           		// $('#bmp_join_network_form').remove();
           	}

           }
         });
     return false;
});


$('#downlines-search').click(function(e){
	e.preventDefault();
	$('.search-message').html('');
	var username=$('#downlines-username').val();
	if(username==''){
		$('.search-message').html('<span style="color:red;">Username could not be empty.</span>');
		return false;
	}
	$.ajax({
	    type: 'POST',
	    url: bmpajaxurl,
	    data: {
	        'action': 'bmp_username_downline_search',
	        'username': username,
	    },
	    success: function (data) {
	    	$('.search-message').html('');
	    	var obj = $.parseJSON(data);
	    	if(obj.status==true){
	    		$('.search-message').html(obj.message);
	    		$( "#downlines-usersearch" ).submit();
	    	} else{
	    		$('.search-message').html(obj.message);
	    		
	    	}
	        return false;
	    }
	});

	return false;
});
