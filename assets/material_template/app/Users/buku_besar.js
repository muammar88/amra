function buku_besar_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-6 col-lg-6 my-3 ">
                     <button class="btn btn-default" type="button" onclick="download_buku_besar()">
                        <i class="fas fa-download"></i> Download Buku Besar
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="periode" id="periode" onChange="get_buku_besar()">
                           <option value="0">Periode Sekarang</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="akun" id="akun" onChange="get_buku_besar()">

                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka" id="table-buku-besar">
                        <thead>
                           <tr>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:15%;">Ref</th>
                              <th style="width:25%;">Keterangan</th>
                              <th style="width:15%;">Debet</th>
                              <th style="width:15%;">Kredit</th>
                              <th style="width:15%;">Saldo</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_buku_besar">
                           <tr>
                              <td colspan="8">Daftar buku besar tidak ditemukan</td>
                           </tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;" id="footers">
                           <tr>
                              <td colspan="3" class="text-right"><b>TOTAL</b></td>
                              <td id="total_akun_debet"><b>${kurs} 10.000.000,-</b></td>
                              <td id="total_akun_kredit"><b>${kurs} 10.000.000,-</b></td>
                              <td id="total_saldo"><b>${kurs} 10.000.000,-</b></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_buku_besar"></div>
                  </div>
               </div>
            </div>`;
}


function buku_besar_getData(){
   ajax_x(
      baseUrl + "Buku_besar/get_filter_buku_besar", function(e) {
         if( e['error'] == false ) {
            var list_akun = '';
            for( x in e['list_akun'] ) {
               list_akun += `<option value="${x}">${e['list_akun'][x]}</option>`;
            }
            var list_periode = '';
            for( y in e['list_periode'] ) {
               list_periode += `<option value="${y}">${e['list_periode'][y]}</option>`;
            }
            $('#akun').html(list_akun);
            $('#periode').html(list_periode);

            get_buku_besar();
         }
      },[]
   );
}

//  get buku besar
function get_buku_besar() {
   get_data_non_pagination_laporan( { url : 'Buku_besar/daftar_buku_besar',
                              bodyTable_id: 'bodyTable_buku_besar',
                              fn: 'ListBukuBesar',
                              warning_text: '<td colspan="9">Daftar buku besar tidak ditemukan</td>',
                              param : { akun: $('#akun').val(),
                                        periode: $('#periode').val() } },
                              function(seed){
                                 var e = JSON.parse(seed);
                                 if( $('#footers').length > 0 ){
                                    $("#total_saldo").html('<b>' + kurs + ' ' + numberFormat(e['saldo_akhir'] + '</b>'));
                                    $("#total_akun_debet").html('<b>' + kurs + ' ' + numberFormat(e['total_debet'] + '</b>'));
                                    $("#total_akun_kredit").html('<b>' + kurs + ' ' + numberFormat(e['total_kredit'] + '</b>'));
                                 }else{
                                    $('#table-buku-besar').append(`<tfoot style="background-color: #ededed;" id="footers">
                                                                      <tr>
                                                                         <td colspan="3" class="text-right"><b>TOTAL</b></td>
                                                                         <td id="total_akun_debet"><b>${kurs} ${numberFormat(e['total_debet'])} </b></td>
                                                                         <td id="total_akun_kredit"><b>${kurs} ${numberFormat(e['total_kredit'])}</b></td>
                                                                         <td id="total_saldo"><b>${kurs} ${numberFormat(e['saldo_akhir'])}</b></td>
                                                                      </tr>
                                                                   </tfoot>`);
                                 }
                            } );
}

function ListBukuBesar(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.last_update}</td>
                  <td>${json.ref}</td>
                  <td>${json.ket}</td>
                  <td>${json.akun_debet}</td>
                  <td>${json.akun_kredit}</td>
                  <td>${numberFormat(json.saldo)}</td>
               </tr>`;
   return html;
}

function download_buku_besar(){
   ajax_x_t2(
      baseUrl + "Buku_besar/download_excel_buku_besar",
      function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
      },
      [{ akun : $('#akun').val(), periode : $('#periode').val()}]
   );
}
