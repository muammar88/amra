function sign_up(e){
   ajax_submit(e, "#sign_up_area", function(e) {
      if (e["error"] == false)
      {
        $.confirm({
           icon: 'far fa-smile',
           title: 'Berhasil',
           content: e['error_msg'],
           type: e['error'] == true ? 'red' :'green',
           buttons: {
             close: function(){
                 window.location.href = baseUrl + "Users/Payment?code=" + e["data"];
             }
           }
        });
        setTimeout(function(){ window.location.href = baseUrl + "Users/Payment?code=" + e["data"]; }, 1000);
      }
   });
}
// window.location.href = baseUrl + "Users/Sign_in";

function durasi_berlangganan(e, param){

   if(param == 1){
      total = 'Rp 50.000,00';
   }else if ( param == 3 ) {
      total = 'Rp 150.000,00';
   }else if ( param == 6 ) {
      total = 'Rp 300.000,00';
   }else if ( param == 12 ) {
      total = 'Rp 600.000,00';
   }

   $('.card').removeClass('actived');
   $(e).addClass('actived');

   $('#duration').val(param);
   $('#title-durasi').html(`Durasi berlangganan AMRA selama ${param} Bulan`);
   $('#price-durasi').html(total);
}
