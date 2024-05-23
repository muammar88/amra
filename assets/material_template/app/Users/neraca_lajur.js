function neraca_lajur_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-12 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="download_neraca_lajur()">
                        <i class="fas fa-download"></i> Download Neraca Lajur
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="periode" id="periode" onChange="get_neraca_lajur()">
                           <option value="0">Periode Sekarang</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka" id="table-buku-besar">
                        <thead>
                           <tr>
                              <th style="width:7%;" rowspan="2">Kode Akun</th>
                              <th style="width:23%;" rowspan="2">Nama Akun</th>
                              <th style="width:14%;" colspan="2">Saldo Awal</th>
                              <th style="width:14%;" colspan="2">Penyesuaian</th>
                              <th style="width:14%;" colspan="2">Saldo Disesuaikan</th>
                              <th style="width:14%;" colspan="2">Neraca</th>
                              <th style="width:14%;" colspan="2">Laba Rugi</th>
                           </tr>
                           <tr>
                              <th style="width:7%;">Debet</th>
                              <th style="width:7%;">Kredit</th>
                              <th style="width:7%;">Debet</th>
                              <th style="width:7%;">Kredit</th>
                              <th style="width:7%;">Debet</th>
                              <th style="width:7%;">Kredit</th>
                              <th style="width:7%;">Debet</th>
                              <th style="width:7%;">Kredit</th>
                              <th style="width:7%;">Debet</th>
                              <th style="width:7%;">Kredit</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_neraca_lajur">
                           <tr><td colspan="14">Daftar Neraca Lajur tidak ditemukan</td></tr>
                        </tbody>
                        <tfoot style="background-color: #ededed;" id="footers"></tfoot>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_neraca_lajur"></div>
                  </div>
               </div>
            </div>`;
}

function neraca_lajur_getData(){
   ajax_x(
      baseUrl + "Neraca_lajur/get_filter_neraca_lajur", function(e) {
         if( e['error'] == false ) {
            var list_periode = '';
            for( y in e['list_periode'] ) {
               list_periode += `<option value="${y}">${e['list_periode'][y]}</option>`;
            }
            $('#periode').html(list_periode);
            get_neraca_lajur();
         }
      },[]
   );
}

// get neraca lajur
function get_neraca_lajur() {
   get_data_non_pagination_laporan( { url : 'Neraca_lajur/daftar_neraca_lajur',
                                      bodyTable_id: 'bodyTable_neraca_lajur',
                                      fn: 'ListNeracaLajur',
                                      warning_text: '<td colspan="14">Daftar neraca lajur tidak ditemukan</td>',
                                      param : { periode: $('#periode').val() } },
                                      function(seed) {
                                         var e = JSON.parse(seed);

                                         var a_debet = ( e.total.laba_kredit - e.total.laba_debet ) > 0 ? 0 : e.total.laba_debet - e.total.laba_kredit;
                                         var a_kredit = ( e.total.laba_kredit - e.total.laba_debet ) > 0 ? e.total.laba_kredit - e.total.laba_debet : 0;

                                         $('#footers').html(`<tr>
                                                               <td colspan="2" class="text-right"><b>TOTAL</b></td>
                                                               <td><b>${numberFormat(e.total.saldo_awal_debet)} </b></td>
                                                               <td><b>${numberFormat(e.total.saldo_awal_kredit)}</b></td>
                                                               <td><b>${numberFormat(e.total.penyesuaian_akun_debet)} </b></td>
                                                               <td><b>${numberFormat(e.total.penyesuaian_akun_kredit)}</b></td>
                                                               <td><b>${numberFormat(e.total.saldo_disesuaikan_debet)} </b></td>
                                                               <td><b>${numberFormat(e.total.saldo_disesuaikan_kredit)}</b></td>
                                                               <td><b>${numberFormat(e.total.neraca_debet)} </b></td>
                                                               <td><b>${numberFormat(e.total.neraca_kredit)}</b></td>
                                                               <td><b>${numberFormat(e.total.laba_debet)} </b></td>
                                                               <td><b>${numberFormat(e.total.laba_kredit)}</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8" class="text-right">${ a_kredit > 0 ? '<b style="color:green">LABA</b>' : '<b style="color:red">RUGI</b>'}</td>
                                                                <td><b>${numberFormat(a_debet)} </b></td>
                                                                <td><b>${numberFormat(a_kredit)} </b></td>
                                                                <td><b>${numberFormat(a_debet)} </b></td>
                                                                <td><b>${numberFormat(a_kredit)} </b></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8" class="text-right"><b>NRC</b></td>
                                                                <td><b>${numberFormat( (e.total.neraca_debet + a_debet).toString() )} </b></td>
                                                                <td><b>${numberFormat((e.total.neraca_kredit + a_kredit).toString())} </b></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>`);
                                   } );
}

function ListNeracaLajur(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td><center>${json.nomor_akun_secondary}</center></td>
                  <td class="text-left">${json.nama_akun_secondary}</td>
                  <td style="border-left:1px solid #add8dc;"><center>${numberFormat(json.saldo_awal_debet)}</center></td>
                  <td><center>${numberFormat(json.saldo_awal_kredit)}</center></td>
                  <td style="border-left:1px solid #add8dc;"><center>${numberFormat(json.penyesuaian_akun_debet)}</center></td>
                  <td><center>${numberFormat(json.penyesuaian_akun_kredit)}</center></td>
                  <td style="border-left:1px solid #add8dc;"><center>${numberFormat(json.saldo_disesuaikan_debet)}</center></td>
                  <td><center>${numberFormat(json.saldo_disesuaikan_kredit)}</center></td>
                  <td style="border-left:1px solid #add8dc;"><center>${numberFormat(json.neraca_debet)}</center></td>
                  <td><center>${numberFormat(json.neraca_kredit)}</center></td>
                  <td style="border-left:1px solid #add8dc;"><center>${numberFormat(json.laba_debet)}</center></td>
                  <td><center>${numberFormat(json.laba_kredit)}</center></td>
               </tr>`;
   return html;
}

function download_neraca_lajur(){
   ajax_x_t2(
     baseUrl + "Neraca_lajur/download_excel_neraca_lajur",
     function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
     },
     [{ periode : $('#periode').val()}]
   );
}
/// <tr style="background-color:#cbdbdd4d"><td><center>11010</center></td><td>Kas</td><td style="border-left:1px solid #add8dc;"><center>200,000,000</center></td><td><center>0</center></td><td style="border-left:1px solid #add8dc;"><center>160,712,940</center></td><td><center>113,547,019</center></td><td style="border-left:1px solid #add8dc;"><center>247,165,921</center></td><td><center>0</center></td><td style="border-left:1px solid #add8dc;"><center>247,165,921</center></td><td><center>0</center></td><td style="border-left:1px solid #add8dc;"><center>0</center></td><td><center>0</center></td></tr>
