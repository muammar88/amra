function superman_sign_in(e){
	e.preventDefault();
   	var error = 0;
   	var error_msg = '';
   	ajax_submit(e, "#superman_sign_in_area", function(a) {
        if ( a.error == false) {
   			$.confirm({
				icon: 'far fa-smile',
				title: 'Berhasil',
				content: a.error_msg,
				type: a.error == true ? 'red' :'blue',
				buttons: {
					close: function() {
						window.location.href = baseUrl + "Superman" ;
					}
				}
			});
            setTimeout(function(){ window.location.href = baseUrl + "Superman" ; }, 1000);
        } else {
        	frown_alert( a.error_msg );
        }
    });
}
