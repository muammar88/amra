function laba_rugi_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-12 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="download_laba_rugi()">
                        <i class="fas fa-download"></i> Download Laba Rugi
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="periode" id="periode" onChange="get_laba_rugi()">
                           <option value="0">Periode Sekarang</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka" id="table-pendapatan">
                        <thead>
                           <tr><th colspan="3" class="text-left">PENDAPATAN</th></tr>
                        </thead>
                        <tbody id="bodyTable_pendapatan">
                           <tr><td colspan="3">Data pendapatan tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;" id="footer_pendapatan">
                           <tr>
                              <td class="text-left" colspan="2"><b>SUBTOTAL PENDAPATAN</b></td>
                              <td class="text-left" id="subtotal_pendapatan"></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka" id="table-biaya-penjualan">
                        <thead>
                           <tr><th colspan="3" class="text-left">BIAYA PENJUALAN</th></tr>
                        </thead>
                        <tbody id="bodyTable_biaya_penjualan">
                           <tr><td colspan="3">Data biaya penjualan tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;" id="footer_biaya_penjualan">
                           <tr>
                              <td class="text-left" colspan="2"><b>SUBTOTAL BIAYA PENJUALAN</b></td>
                              <td id="subtotal_biaya_penjualan" class="text-left"></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-12 mb-3">
                     <table class="table table-hover tablebuka" id="table-laba-kotor">
                        <tbody id="bodyTable_laba_kotor">
                           <tr style="background-color: #ffc0c0;">
                              <td class="text-left" colspan="2" style="width:40%;"><b>LABA KOTOR</b></td>
                              <td id="laba_kotor" class="text-left" style="width:60%;"></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka" id="table-pengeluaran">
                        <thead>
                           <tr><th colspan="3" class="text-left">PENGELUARAN</th></tr>
                        </thead>
                        <tbody id="bodyTable_pengeluaran">
                           <tr><td colspan="3">Data pengeluaran tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;" id="footer_pengeluaran">
                           <tr>
                              <td class="text-left" colspan="2"><b>SUBTOTAL PENGELUARAN</b></td>
                              <td id="subtotal_pengeluaran" class="text-left"></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka" id="table-laba-bersih">
                        <tbody id="bodyTable_laba_bersih">
                           <tr style="background-color: #ffc0c0;">
                              <td class="text-left" colspan="2" style="width:40%;"><b>LABA BERSIH</b></td>
                              <td id="laba_bersih" class="text-left" style="width:60%;"></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" ></div>
               </div>
            </div>`;
}

function laba_rugi_getData(){
   ajax_x(
      baseUrl + "Laba_rugi/get_filter_laba_rugi", function(e) {
         if( e['error'] == false ) {
            var list_periode = '';
            for( y in e['list_periode'] ) {
               list_periode += `<option value="${y}">${e['list_periode'][y]}</option>`;
            }
            $('#periode').html(list_periode);
            get_laba_rugi();
         }
      },[]
   );
}

function get_laba_rugi(){
   ajax_x(
     baseUrl + "Laba_rugi/daftar_laba_rugi", function(e) {
         if( e['error'] == false ) {
            // pendapatan
            var html_pendapatan = '';
            var pendapatan = e['list'][4];
            var total_pendapatan = 0;
            for( x in pendapatan ) {
               html_pendapatan +=  `<tr>
                                       <td class="text-left" style="width:10%;">${pendapatan[x]['nomor_akun']}</td>
                                       <td class="text-left" style="width:30%;">${pendapatan[x]['nama_akun_secondary']}</td>
                                       <td class="text-left" style="width:60%;">Rp ${numberFormat(pendapatan[x]['saldo'])}</td>
                                    </tr>`;
               total_pendapatan = total_pendapatan + pendapatan[x]['saldo'];
            }
            $('#subtotal_pendapatan').html('<b>Rp ' + numberFormat(total_pendapatan) + '</b>');
            $('#bodyTable_pendapatan').html(html_pendapatan);
            // penjualan
            var html_penjualan = '';
            var penjualan = e['list'][5];
            var total_penjualan = 0;
            for( x in penjualan ) {
               html_penjualan += `<tr>
                                       <td class="text-left" style="width:10%;">${penjualan[x]['nomor_akun']}</td>
                                       <td class="text-left" style="width:30%;">${penjualan[x]['nama_akun_secondary']}</td>
                                       <td class="text-left" style="width:60%;">Rp ${numberFormat(penjualan[x]['saldo'])}</td>
                                    </tr>`;
               total_penjualan = total_penjualan + penjualan[x]['saldo'];
            }
            $('#subtotal_biaya_penjualan').html('<b>Rp ' + numberFormat(total_penjualan) + '</b>');
            $('#bodyTable_biaya_penjualan').html(html_penjualan);
            $('#laba_kotor').html('<b>Rp ' + numberFormat(total_pendapatan - total_penjualan ).toString() + '</b>');
            // pengeluaran
            var html_pengeluaran = '';
            var pengeluaran = e['list'][6];
            var total_pengeluaran = 0;
            for( x in pengeluaran ) {
               html_pengeluaran += `<tr>
                                       <td class="text-left" style="width:10%;">${pengeluaran[x]['nomor_akun']}</td>
                                       <td class="text-left" style="width:30%;">${pengeluaran[x]['nama_akun_secondary']}</td>
                                       <td class="text-left" style="width:60%;">Rp ${numberFormat(pengeluaran[x]['saldo'])}</td>
                                    </tr>`;
               total_pengeluaran = total_pengeluaran + pengeluaran[x]['saldo'];
            }
            $('#subtotal_pengeluaran').html('<b>Rp ' + numberFormat(total_pengeluaran) + '</b>');
            $('#bodyTable_pengeluaran').html(html_pengeluaran);
            $('#laba_bersih').html('<b>Rp ' + numberFormat(total_pendapatan - total_penjualan - total_pengeluaran).toString() + '</b>');
         }else{
            $('#subtotal_pendapatan').html('<b>Rp 0</b>');
            $('#bodyTable_pendapatan').html(`<tr><td colspan="3">Data pendapatan tidak ditemukan</td></tr>`);

            $('#subtotal_biaya_penjualan').html('<b>Rp 0</b>');
            $('#bodyTable_biaya_penjualan').html(`<tr><td colspan="3">Data penjualan tidak ditemukan</td></tr>`);
            $('#laba_kotor').html('<b>Rp 0</b>');

            $('#subtotal_pengeluaran').html('<b>Rp 0</b>');
            $('#bodyTable_pengeluaran').html(`<tr><td colspan="3">Data pengeluaran tidak ditemukan</td></tr>`);
            $('#laba_bersih').html('<b>Rp 0</b>');
         }
     },[{ periode: $('#periode').val()}]
   );
}

function download_laba_rugi(){
   ajax_x_t2(
     baseUrl + "Laba_rugi/download_excel_laba_rugi",
     function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
     },[{ periode : $('#periode').val()}]
  );
}
