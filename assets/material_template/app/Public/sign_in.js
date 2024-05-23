function sign_in(e){
   e.preventDefault();
   var level = $('#level_akun').val();
   var error = 0;
   var error_msg = '';
   if( level == 'administrator') {
      console.log(level);
      console.log($('#token_input').length);
      if( $('#token_input').length == 0 ){
         var verified = ajax_r( baseUrl + "Users/Sign_in/check_verification",[{email:$('#email').val()}] );
         if(verified.verifikasi == false){
            error = 1; error_msg = '';
         }
      }
   }
   // sign_in_process
   if( error == 1 ){
      var html = `<div class="row px-3">
                     <label class="mb-1"><h6 class="mb-0 text-sm">Token</h6></label>
                     <div class="input-group mb-3">
                        <input type="text" id="token_input" name="token" maxlength="6" class="form-control mt-0 rounded-0" placeholder="Token" aria-label="Token" aria-describedby="button-addon2">
                        <button class="btn btn-blue" type="button" id="button-addon2" onClick="get_token()" >Get Token</button>
                        <small id="emailHelp" class="form-text text-muted"><i>Pesan Token OTP akan dikirimkan ke nomor Whatsapp anda.</i></small>
                     </div>
                  </div>`;
      $('#token').html(html);
   }else{
      ajax_submit(e, "#sign_in_area", function(a) {
         if ( a.error == false) {
            if(a.return_data != undefined ) {
               if( level == 'administrator') {
                  $.confirm({
                     columnClass: 'col-4',
                     title: 'Masa Berlanggan Berakhir',
                     theme: 'material',
                     content: 'Masa berlangganan akun ini sudah berakhir. Apakah anda ingin memperpanjang masa aktifnya?.',
                     closeIcon: false,
                     buttons: {
                        tidak:function () {
                           return true;
                        },
                        ya: {
                           text: 'Ya',
                           btnClass: 'btn-blue',
                           action: function () {
                              ajax_x(
                                 baseUrl + "Users/Sign_in/prepare_renew", function(e) {
                                    window.location.href = baseUrl + 'Users/Renew_subscribtion?code=' + a.return_data.code ;
                                 },[{code:a.return_data.code}]
                              );
                           }
                        }
                     }
                  });
               }else{
                  frown_alert("Masa berlangganan akun perusahaan ini sudah berakhir. Anda dapat menggunakan kembali akun ini jika masa berlangganan Akun perusahaan ini sudah diperpanjang.");
               }
            }else{
               $.confirm({
                  icon: 'far fa-smile',
                  title: 'Berhasil',
                  content: a.error_msg,
                  type: a.error == true ? 'red' :'blue',
                  buttons: {
                     close: function() {
                        window.location.href = baseUrl + "Users?company_code=" + a.company_code ;
                     }
                  }
               });
               setTimeout(function(){ window.location.href = baseUrl + "Users?company_code=" + a.company_code ; }, 1000);
            }
         } else {
            frown_alert( a.error_msg );
         }
      });
   }
}

function get_token(){
   ajax_x(
      baseUrl + "Users/Sign_in/get_otp", function(e) {
         if( e['error'] == false ){
            smile_alert(e['error_msg']);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{email:$('#email').val()}]
   );
}

function get_token_staff(){
   var kode_perusahaan = $('#kode_perusahaan').val();
   var nomor_whatsapp = $('#nomor_whatsapp').val();
   var error = 0;
   var errormsg = '';
   if( kode_perusahaan == '' ){
      error = 1; error_msg = 'Kode perusahaan tidak boleh kosong.';
   }
   if( nomor_whatsapp == '' ) {
      error = 1; error_msg = 'Nomor whatsapp tidak boleh kosong.';
   }
   if( error == 0 ) {
      ajax_x(
         baseUrl + "Users/Sign_in/get_otp_staff", function(e) {
            if( e['error'] == false ){
               smile_alert(e['error_msg']);
            }else{
               frown_alert(e['error_msg']);
            }
         },[{kode_perusahaan:$('#kode_perusahaan').val(), nomor_whatsapp: $('#nomor_whatsapp').val()}]
      );
   }else{
      frown_alert(error_msg);
   }
}
