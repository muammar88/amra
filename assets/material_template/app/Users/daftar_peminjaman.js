function daftar_peminjaman_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarPeminjaman">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_peminjaman()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Peminjaman
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3">
                     <select class="form-control form-control-sm filter" id="statusPeminjaman" name="statusPeminjaman" onchange="get_daftar_peminjaman(20)">
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="lunas">Lunas</option>
                     </select>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_peminjaman(20)" id="searchAllDaftarPeminjaman" name="searchAllDaftarPeminjaman" placeholder="Nama Jamaah" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_peminjaman(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Register Number</th>
                              <th style="width:22%;">Info Jamaah</th>
                              <th style="width:25%;">Info Peminjaman</th>
                              <th style="width:30%;">Detail Pembayaran</th>
                              <th style="width:13%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_peminjaman">
                           <tr>
                              <td colspan="5">Daftar peminjaman tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_peminjaman"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_peminjaman_getData(){
   get_daftar_peminjaman(20);
}

function get_daftar_peminjaman(perpage){
   get_data( perpage,
             { url : 'Daftar_peminjaman/server_daftar_peminjaman',
               pagination_id: 'pagination_daftar_peminjaman',
               bodyTable_id: 'bodyTable_daftar_peminjaman',
               fn: 'ListDaftarPeminjaman',
               warning_text: '<td colspan="5">Daftar peminjaman tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarPeminjaman').val(), status: $('#statusPeminjaman').val() } } );
}

function ListDaftarPeminjaman(JSONData){
   var json = JSON.parse( JSONData );

    var html =   `<tr>
                     <td>${json.register_number}</td>
                     <td>
                        <table class="table table-hover my-1">
                           <tbody>
                              <tr>
                                 <td class="text-left" style="width:40%;">NAMA JAMAAH</td>
                                 <td class="px-0" style="width:1%;">:</td>
                                 <td class="text-left" style="width:59%;">${
                                   json.fullname
                                 }</td>
                              </tr>
                              <tr>
                                 <td class="text-left border-bottom-0">NO IDENTITAS JAMAAH</td>
                                 <td class="px-0 border-bottom-0">:</td>
                                 <td class="text-left border-bottom-0">${
                                   json.identity_number
                                 }</td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                     <td>
                        <table class="table table-hover my-1">
                           <tbody>
                              <tr>
                                 <td class="text-left" style="width:40%;">JUMLAH PEMINJAMAN</td>
                                 <td class="px-0" style="width:1%;">:</td>
                                 <td class="text-left" style="width:59%;">Rp ${numberFormat(json.biaya)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">JUMLAH DP</td>
                                 <td class="px-0">:</td>
                                 <td class="text-left">Rp ${numberFormat(json.dp)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">TENOR</td>
                                 <td class="px-0">:</td>
                                 <td class="text-left">${json.tenor}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">BAYAR PER BULAN</td>
                                 <td class="px-0">:</td>
                                 <td class="text-left">Rp ${numberFormat(json.perbulan)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">MULAI PEMBAYARAN</td>
                                 <td class="px-0">:</td>
                                 <td class="text-left">${
                                   json.mulai_pembayaran
                                 }</td>
                              </tr>
                              <tr>
                                 <td class="text-left">SUDAH BAYAR</td>
                                 <td class="px-0">:</td>
                                 <td class="text-left">${
                                   numberFormat(json.sudah_bayar) 
                                 }</td>
                              </tr>
                               <tr>
                                 <td class="text-left border-bottom-0">STATUS PEMINJAMAN</td>
                                 <td class="px-0 border-bottom-0">:</td>
                                 <td class="text-left border-bottom-0">${
                                   json.status_peminjaman == 'belum_lunas' ? '<b>BELUM LUNAS</b>' : '<b>LUNAS</b>'
                                 }</td>
                              </tr>
                             
                           </tbody>
                        </table>
                     </td>
                     <td>
                        <table class="table table-hover my-1">
                           <thead>
                              <tr>
                                 <th class="text-center" colspan="4" style="background-color: #e7e7e7;"><b>RIWAYAT PEMBAYARAN PEMINJAMAN</b></th>
                              </tr>
                              <tr>
                                 <th style="width:25%;">#Invoice</th>
                                 <th style="width:40%;">Biaya</th>
                                 <th style="width:20%;">Status</th>
                                 <th style="width:15%;">Aksi</th>
                              </tr>
                           </thead>
                           <tbody>`;

                for( x in json.detail_pembayaran){
                  html += `<tr>
                              <td><b>#${json.detail_pembayaran[x].invoice}</b></td>
                              <td>Rp ${numberFormat(json.detail_pembayaran[x].bayar)}</td>
                              <td>${json.detail_pembayaran[x].status}</td>
                              <td> 
                                 <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Pembayaran Peminjaman"
                                    onclick="cetak_kwitansi_cicilan('${json.detail_pembayaran[x].id}')" style="margin:.15rem .1rem  !important">
                                    <i class="fas fa-print" style="font-size: 11px;"></i>
                                 </button>
                              </td>
                           </tr>`;
               }

               html +=    `</tbody>
                        </table>
                     </td>
                     <td>
                        <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Peminjaman"
                           onclick="cetak_kwitansi_peminjaman('${json.id}')" style="margin:.15rem .1rem  !important">
                           <i class="fas fa-print" style="font-size: 11px;"></i>
                        </button>
                        <button type="button" class="btn btn-default btn-action" title="Pembayaran Cicilan"
                           onclick="pembayaran_cicilan('${json.id}')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-money-bill-wave" style="font-size: 11px;"></i>
                        </button>
                         <button type="button" class="btn btn-default btn-action" title="Edit Skema Cicilan"
                           onclick="edit_skema_cicilan('${json.id}')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-list-ol" style="font-size: 11px;"></i>
                        </button>
                        <button type="button" class="btn btn-default btn-action" title="Delete Peminjaman"
                           onclick="delete_cicilan_peminjaman('${json.id}')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </td>
                  </tr>`;
   return html;
}

function delete_cicilan_peminjaman(peminjaman_id){
    ajax_x(
      baseUrl + "Daftar_peminjaman/delete_cicilan_peminjaman",
      function (e) {
            // error
            if( e['error'] == false ){
               smile_alert(e["error_msg"])
               get_daftar_peminjaman(20);
            }else{
               frown_alert(e["error_msg"]);
            }
         },
      [{peminjaman_id:peminjaman_id}]
   );

}

function cetak_kwitansi_peminjaman(peminjaman_id){
   ajax_x(
      baseUrl + "Daftar_peminjaman/cetak_kwitansi_peminjaman",
      function (e) {
            // error
            if( e['error'] == false ){
               window.open(baseUrl + "Kwitansi/", "_blank");
            }else{
               frown_alert(e["error_msg"]);
            }
         },
      [{peminjaman_id:peminjaman_id}]
   );
}

function pembayaran_cicilan(peminjaman_id){
   ajax_x(
      baseUrl + "Daftar_peminjaman/info_pembayaran_cicilan",
      function (e) {
            $.confirm({
               columnClass: "col-4",
               title: "Form Pembayaran Peminjaman",
               theme: "material",
               content: formaddupdate_pembayaran_peminjaman(
                  JSON.stringify(e["data"])
               ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  simpan: {
                    text: "Bayar",
                    btnClass: "btn-blue",
                    action: function () {
                        var sisa_utang = $('#sisa_utang').val();
                        var biaya = hide_currency( $('#biaya').val() );

                        if( biaya > sisa_utang ) {
                           frown_alert('Biaya yang dibayarkan tidak boleh lebih besar dari sisa hutang.' );
                           return false;
                        } else{
                           ajax_submit_t1("#form_utama", function (e) {
                              e["error"] == true ? frown_alert(e["error_msg"]) : smile_alert(e["error_msg"]);
                              if (e["error"] == true) {
                                 return false;
                              } else {
                                window.open(baseUrl + "Kwitansi/", "_blank");
                                 get_daftar_peminjaman(20);
                                 return true;
                              }
                           });
                        }
                     },
                  },
               },
            });
         },
      [{peminjaman_id:peminjaman_id}]
   );
}

function formaddupdate_pembayaran_peminjaman(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<form action="${baseUrl }Daftar_peminjaman/proses_addupdate_pembayaran_peminjaman" id="form_utama" class="formName ">
               <div class="row px-0 mx-0">
                  <div class="col-12">
                     <input type="hidden" name="peminjaman_id" value="${json.peminjaman_id}">
                     <input type="hidden" id="sisa_utang" value="${json.sisa_utang}">
                     <input type="hidden" id="invoice" name="invoice" value="${json.invoice}">
                  </div>
                  <div class="col-12">
                     <div class="form-group">
                        <label>Sisa Hutang</label>
                        <input class="form-control form-control-sm" type="text" readonly value="Rp ${numberFormat(json.sisa_utang)}" style="font-size: 12px;">
                     </div>
                  </div>
                  <div class="col-12">
                     <div class="form-group">
                        <label>Invoice</label>
                        <input class="form-control form-control-sm" type="text" readonly value="${json.invoice}" style="font-size: 12px;">
                     </div>
                  </div>
                  <div class="col-12">
                     <div class="form-group">
                        <label>Biaya Pembayaran</label>
                        <input class="form-control form-control-sm currency" type="text" id="biaya" name="biaya" style="font-size: 12px;" placeholder="Biaya Pembayaran" >
                     </div>
                  </div>
               </div>
            </form>
            <script>
               $(document).on( "keyup", ".currency", function(e){
                   var e = window.event || e;
                   var keyUnicode = e.charCode || e.keyCode;
                       if (e !== undefined) {
                           switch (keyUnicode) {
                               case 16: break;
                               case 27: this.value = ''; break;
                               case 35: break;
                               case 36: break;
                               case 37: break;
                               case 38: break;
                               case 39: break;
                               case 40: break;
                               case 78: break;
                               case 110: break;
                               case 190: break;
                               default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                           }
                       }
               } );
            </script>`;
   return html;
}

function cetak_kwitansi_cicilan(id){
    ajax_x(
      baseUrl + "Daftar_peminjaman/cetak_kwitansi_cicilan",
      function (e) {
            // error
            if( e['error'] == false ){
                window.open(baseUrl + "Kwitansi/", "_blank");
            }else{
               frown_alert(e["error_msg"]);
            }
         },
      [{id:id}]
   );
}

function edit_skema_cicilan(id){
    ajax_x(
      baseUrl + "Daftar_peminjaman/info_skema_peminjaman",
      function (e) {
            // error
            if( e['error'] == false ){
               // confirm
               $.confirm({
                  columnClass: "col-6",
                  title: "Form Edit Skema Peminjaman",
                  theme: "material",
                  content: formaddupdate_skema_peminjaman(
                     JSON.stringify(e["data"])
                  ),
                  closeIcon: false,
                  buttons: {
                     cancel: function () {
                       return true;
                     },
                     simpan: {
                       text: "Simpan Perubahan Skema",
                       btnClass: "btn-blue",
                       action: function () {

                           var total_utang = $('#total_utang_base').val();

                           var total_amount = 0;
                           $('.amount').each(function(index){
                              if( hide_currency($(this).val()) != 0 ){
                                 total_amount = total_amount + hide_currency($(this).val());
                              }
                           });

                           if( total_utang == total_amount ){
                              ajax_submit_t1("#form_utama", function (e) {
                                 e["error"] == true ? frown_alert(e["error_msg"]) : smile_alert(e["error_msg"]);
                                 if (e["error"] == true) {
                                    return false;
                                 } else {
                                   //  window.open(baseUrl + "Kwitansi/", "_blank");
                                    get_daftar_peminjaman(20);
                                    return true;
                                 }
                              });
                           }else{
                              frown_alert('Total Amount Harus Sama Dengan Total Utang Yaitu : <b>Rp ' + numberFormat(total_utang) + '</b>' );
                              return false;
                           }
                        },
                     },
                  },
               });
            }else{
               frown_alert(e["error_msg"]);
            }
         },
      [{id:id}]
   );
}


function formaddupdate_skema_peminjaman(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<form action="${baseUrl }Daftar_peminjaman/proses_addupdate_skema_peminjaman" id="form_utama" class="formName ">
               <div class="row px-0 mx-0">
                  <div class="col-6">
                     <input type="hidden" name="peminjaman_id" value="${json.peminjaman_id}" >
                     <input type="hidden" id="total_utang_base" value="${json.total_utang}" >
                     <label>Total Pinjaman : Rp <span id="total_utang">${numberFormat(json.total_utang)}</span></label>
                  </div>
                  <div class="col-6 text-right">
                     <label>DP : Rp ${numberFormat(json.dp)}</label>
                  </div>
                  <div class="col-12">
                     <div class="row">
                        <table class="table">
                           <thead>
                              <tr>
                                 <th style="width:20%">Term</th>
                                 <th style="width:40%">Amount</th>
                                 <th style="width:40%">Tanggal Jatuh Tempo</th>
                              </tr>
                           </thead>
                           <tbody>`;

                  for( x in json.skema ){
                    html += `<tr>
                                 <td>${json.skema[x].term}</td>
                                 <td>
                                    <input type="text" name="amount[${json.skema[x].id}]" value="Rp ${numberFormat(json.skema[x].amount)}" class="form-control form-control-sm currency amount" placeholder="Amount" onKeyup="countAmount()" />
                                 </td>
                                 <td>
                                    <input type="date" name="due_date[${json.skema[x].id}]" value="${json.skema[x].due_date}" class="form-control form-control-sm" placeholder="Due Date" />
                                 </td>
                              </tr>`;
                  }
                              
            html +=       `</tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </form>
            <script>
               $(document).on( "keyup", ".currency", function(e){
                   var e = window.event || e;
                   var keyUnicode = e.charCode || e.keyCode;
                       if (e !== undefined) {
                           switch (keyUnicode) {
                               case 16: break;
                               case 27: this.value = ''; break;
                               case 35: break;
                               case 36: break;
                               case 37: break;
                               case 38: break;
                               case 39: break;
                               case 40: break;
                               case 78: break;
                               case 110: break;
                               case 190: break;
                               default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                           }
                       }
               } );
            </script>`;
   return html;
}

function countAmount(){

  var total_utang = $('#total_utang_base').val();

   var total_amount = 0;
  $('.amount').each(function(index){
      if( hide_currency($(this).val()) != 0 ){
         total_amount = total_amount + hide_currency($(this).val());
      }
   });

   if( total_amount == total_utang ){
      $('#total_utang').css("color", "black");
   }else{
      $('#total_utang').css("color", "red");
   }

  $('#total_utang').html(numberFormat(total_amount));
}

// <div class="col-12">
//                            <div class="form-group">
//                               <label>NOMOR REGISTER</label>
//                               <input type="text" readonly class="form-control form-control-sm" value="${json.register_number}" />
//                               <input type="hidden" name="register_number" value="${json.register_number}" />
//                            </div>
//                         </div>
//                         <div class="col-12">
//                            <div class="form-group">
//                               <label>Nama Jamaah</label>
//                               <select class="form-control form-control-sm" name="jamaah" id="jamaah">
//                                  <option value="0">Pilih Jamaah</option>`;
//                      for ( x in json.jamaah ) {
//                         html += `<option value=${x}>${json.jamaah[x]}</option>`;
//                      }
//                html +=       `</select>
//                            </div>
//                         </div>
//                         <div class="col-12">
//                            <div class="form-group">
//                               <label>Biaya Dipinjam</label>
//                               <input type="text" name="biaya" class="form-control form-control-sm currency" placeholder="Biaya Dipinjam" />
//                            </div>
//                         </div>
//                         <div class="col-8">
//                            <div class="form-group">
//                               <label>DP</label>
//                               <input type="text" name="dp" class="form-control form-control-sm currency" placeholder="DP" />
//                            </div>
//                         </div>
//                         <div class="col-4">
//                            <div class="form-group">
//                               <label>Tenor</label>
//                               <input type="text" name="tenor" class="form-control form-control-sm" placeholder="Tenor" />
//                            </div>
//                         </div>
//                         <div class="col-12">
//                            <div class="form-group">
//                               <label>Mulai Pembayaran</label>
//                               <input type="date" name="mulai_pembayaran" class="form-control form-control-sm" placeholder="Mulai Pembayaran" />
//                            </div>
//                         </div>

function add_peminjaman(){
   ajax_x(
      baseUrl + "Daftar_peminjaman/info_peminjaman",
      function (e) {
            // error
            if( e['error'] == false ){
               // confirm
               $.confirm({
                  columnClass: "col-5",
                  title: "Form Tambah Peminjaman",
                  theme: "material",
                  content: formaddupdate_peminjaman(
                     JSON.stringify(e["data"])
                  ),
                  closeIcon: false,
                  buttons: {
                     cancel: function () {
                       return true;
                     },
                     simpan: {
                       text: "Tambah Peminjaman",
                       btnClass: "btn-blue",
                       action: function () {
                           ajax_submit_t1("#form_utama", function (e) {
                              e["error"] == true ? frown_alert(e["error_msg"]) : smile_alert(e["error_msg"]);
                              if (e["error"] == true) {
                                 return false;
                              } else {
                                 window.open(baseUrl + "Kwitansi/", "_blank");
                                 get_daftar_peminjaman(20);
                                 return true;
                              }
                           });
                        },
                     },
                  },
               });
            }else{
               frown_alert(e["error_msg"]);
            }
         },
      []
   );
}


function formaddupdate_peminjaman(JSONData){

   var json = JSON.parse( JSONData );

   var html = `<form action="${baseUrl }Daftar_peminjaman/proses_addupdate_peminjaman" id="form_utama" class="formName ">
               <div class="row px-0 mx-0">
                  <div class="col-12">
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group">
                              <label>NOMOR REGISTER</label>
                              <input type="text" readonly class="form-control form-control-sm" value="${json.register_number}" />
                              <input type="hidden" name="register_number" value="${json.register_number}" />
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <label>Nama Jamaah</label>
                              <select class="form-control form-control-sm" name="jamaah" id="jamaah">
                                 <option value="0">Pilih Jamaah</option>`;
                     for ( x in json.jamaah ) {
                        html += `<option value=${x}>${json.jamaah[x]}</option>`;
                     }
               html +=       `</select>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <label>Biaya Dipinjam</label>
                              <input type="text" name="biaya" class="form-control form-control-sm currency" placeholder="Biaya Dipinjam" />
                           </div>
                        </div>
                        <div class="col-8">
                           <div class="form-group">
                              <label>DP</label>
                              <input type="text" name="dp" class="form-control form-control-sm currency" placeholder="DP" />
                           </div>
                        </div>
                        <div class="col-4">
                           <div class="form-group">
                              <label>Tenor</label>
                              <input type="text" name="tenor" class="form-control form-control-sm" placeholder="Tenor" />
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group">
                              <label>Sudah Berangkat</label>
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" value="sudah_berangkat" id="sudah_berangkat" name="sudah_berangkat">
                                 <label class="form-check-label" for="flexCheckChecked">
                                    Sudah Berangkat
                                 </label>
                              </div>
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group">
                              <label>Mulai Pembayaran</label>
                              <input type="date" name="mulai_pembayaran" class="form-control form-control-sm" placeholder="Mulai Pembayaran" />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
            <script>
               $(document).on( "keyup", ".currency", function(e){
                   var e = window.event || e;
                   var keyUnicode = e.charCode || e.keyCode;
                       if (e !== undefined) {
                           switch (keyUnicode) {
                               case 16: break;
                               case 27: this.value = ''; break;
                               case 35: break;
                               case 36: break;
                               case 37: break;
                               case 38: break;
                               case 39: break;
                               case 40: break;
                               case 78: break;
                               case 110: break;
                               case 190: break;
                               default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                           }
                       }
               } );
            </script>`;
   return html;

}