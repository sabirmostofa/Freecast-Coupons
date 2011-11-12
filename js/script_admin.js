jQuery(document).ready(function($){
    $( "#datepicker" ).datepicker();
    $( "#datepicker" ).datepicker("option", "dateFormat",'yy-mm-dd');
    
    
           $('a.delete-fc-coupon').bind('click',function(evt){
        evt.preventDefault();
      
        var ans = confirm('All Coupons from this lot will be deleted. Are you sure?');
        if(ans==false)return;
        var key = $(this).parent().next().text();
  
			
        $.ajax({
            type :  "post",
            url : ajaxurl,
            timeout : 5000,
            data : {
                'action' : 'coupon_ajax_remove',
                'key' : key		  
            },			
            success :  function(data){						
                window.location.href=window.location.href;
            }
        })	//end of ajax					
			
    });
});