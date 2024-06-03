function neraca_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-12 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="download_neraca()">
                        <i class="fas fa-download"></i> Download Neraca
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="periode" id="periode" onChange="get_neraca()">
                           <option value="0">Periode Sekarang</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-6">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr style="background-color: #d5dbf7;"><th colspan="3" class="text-left">AKTIVA</th></tr>
                           <tr><th colspan="3" class="text-left">Asset</th></tr>
                        </thead>
                        <tbody id="bodyTable_asset">
                           <tr><td colspan="3">Data asset tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;">
                           <tr>
                              <td class="text-left" colspan="2"><b>SUBTOTAL ASSET</b></td>
                              <td class="text-left" id="subtotal_asset"></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-6">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr style="background-color: #d5dbf7;"><th colspan="3" class="text-left">PASSIVA</th></tr>
                           <tr><th colspan="3" class="text-left">Kewajiban</th></tr>
                        </thead>
                        <tbody id="bodyTable_kewajiban">
                           <tr><td colspan="3">Data kewajiban tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;">
                           <tr>
                              <td class="text-left" colspan="2"><b>SUBTOTAL KEWAJIBAN</b></td>
                              <td id="subtotal_kewajiban" class="text-left"></td>
                           </tr>
                        </tfoot>
                     </table>
                     <table class="table table-hover tablebuka mt-3">
                        <thead>
                           <tr><th colspan="3" class="text-left">Ekuitas</th></tr>
                        </thead>
                        <tbody id="bodyTable_ekuitas">
                           <tr><td colspan="3">Data ekuitas tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;">
                           <tr>
                              <td class="text-left" colspan="2"><b>SUBTOTAL EKUITAS</b></td>
                              <td id="subtotal_ekuitas" class="text-left"></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-6 px-2 pb-3" >
                     <table class="table table-hover tablebuka mt-3">
                        <tbody >
                           <tr style="background-color: #ffc0c0;">
                              <td colspan="2" class="text-left" style="width:60%"><b>TOTAL AKTIVA</b></td>
                              <td class="text-left" id="total_aktiva" style="width:40%"></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-6 px-2 pb-3" >
                     <table class="table table-hover tablebuka mt-3">
                        <tbody >
                           <tr style="background-color: #ffc0c0;">
                              <td colspan="2" class="text-left" style="width:60%"><b>TOTAL PASSIVA</b></td>
                              <td class="text-left" id="total_passiva" style="width:40%"></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>`;
}

function neraca_getData(){
   ajax_x(
      baseUrl + "Neraca/get_filter_neraca", function(e) {
         if( e['error'] == false ) {
            var list_periode = '';
            for( y in e['list_periode'] ) {
               list_periode += `<option value="${y}">${e['list_periode'][y]}</option>`;
            }
            $('#periode').html(list_periode);
            get_neraca();
         }
      },[]
   );
}

function get_neraca(){
   ajax_x(
      baseUrl + "Neraca/daftar_neraca", function(e) {
         if( e['error'] == false ) {

            var asset  = e['list'][1];
            var kewajiban = e['list'][2];
            var ekuitas = e['list'][3];

            // asset
            var html_asset = '';
            var total_asset = 0;
            for( x in asset ){
               html_asset += `<tr><td style="width:10%">${asset[x]['nomor_akun']}</td><td class="text-left" style="width:50%">${asset[x]['nama_akun_secondary']}</td><td class="text-left" style="width:40%">${kurs} ${numberFormat(asset[x]['saldo'].toString())}</td></tr>`;
               total_asset = total_asset + asset[x]['saldo'];
            }
            $('#bodyTable_asset').html(html_asset);
            $('#subtotal_asset').html('<b>' + kurs + ' ' + numberFormat(total_asset) + '</b>');
            $('#total_aktiva').html('<b>'  + kurs + ' ' + numberFormat(total_asset) + '</b>');

            // kewajiban
            var html_kewajiban = '';
            var total_kewajiban = 0;
            for( x in kewajiban ){
               html_kewajiban += `<tr><td style="width:10%">${kewajiban[x]['nomor_akun']}</td><td class="text-left" style="width:50%">${kewajiban[x]['nama_akun_secondary']}</td><td class="text-left" style="width:40%">${kurs} ${numberFormat(kewajiban[x]['saldo'].toString())}</td></tr>`;
               total_kewajiban = total_kewajiban + kewajiban[x]['saldo'];
            }
            $('#bodyTable_kewajiban').html(html_kewajiban);
            $('#subtotal_kewajiban').html('<b>'+ kurs + ' ' + numberFormat(total_kewajiban) + '</b>');


            var html_ekuitas = '';
            var total_ekuitas = 0;
            for( x in ekuitas ){
               html_ekuitas += `<tr><td style="width:10%">${ekuitas[x]['nomor_akun']}</td><td class="text-left" style="width:50%">${ekuitas[x]['nama_akun_secondary']}</td><td class="text-left" style="width:40%">${kurs} ${numberFormat(ekuitas[x]['saldo'].toString())}</td></tr>`;
               total_ekuitas = total_ekuitas + ekuitas[x]['saldo'];
            }

            console.log(total_kewajiban);
            console.log(total_asset)

            $('#bodyTable_ekuitas').html(html_ekuitas);
            $('#subtotal_ekuitas').html('<b>'+  + kurs + ' ' + numberFormat(total_ekuitas) + '</b>');
            $('#total_passiva').html('<b>'  + kurs + ' '+ numberFormat(total_kewajiban + total_ekuitas) + '</b>');
         }else{
            $('#bodyTable_asset').html(`<tr><td colspan="3">Data asset tidak ditemukan</td></tr>`);
            $('#subtotal_asset').html(`<b>${kurs} 0</b>`);
            $('#total_aktiva').html(`<b>${kurs} 0</b>`);
            $('#bodyTable_kewajiban').html(`<tr><td colspan="3">Data kewajiban tidak ditemukan</td></tr>`);
            $('#subtotal_kewajiban').html(`<b>${kurs} 0</b>`);
            $('#bodyTable_ekuitas').html(`<tr><td colspan="3">Data ekuitas tidak ditemukan</td></tr>`);
            $('#subtotal_ekuitas').html(`<b>${kurs} 0</b>`);
            $('#total_passiva').html(`<b>${kurs} 0</b>`);
         }
      },[{periode: $('#periode').val()}]
   );
}

function download_neraca(){
   ajax_x_t2(
     baseUrl + "Neraca/download_excel_neraca",
     function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
     },[{ periode : $('#periode').val()}]
  );
}
