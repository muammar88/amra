function trans_paket_la_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
                <input type="hidden" id="nums" value="1">
               <div class="row" id="contentTransPaketLA">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_trans_paket_la()">
                        <i class="fas fa-box"></i> Tambah Transaksi Paket LA
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_trans_paket_la(20)" id="searchNamaKlien" name="searchNamaKlien" placeholder="Nomor Registrasi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_trans_paket_la(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:8%;">Nomor Register</th>
                              <th style="width:18%;">Info Klien</th>
                              <th style="width:51%;">Info Item Transaksi</th>
                              <th style="width:15%;">Info Harga</th>
                              <th style="width:8%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody class="bodyTable" id="bodyTable_trans_paket_la">
                           <tr>
                              <td colspan="5">Daftar transaksi paket la tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_trans_paket_la"></div>
                  </div>
               </div>
            </div>`;
}


function trans_paket_la_getData(){
   get_trans_paket_la(20);
}

function get_trans_paket_la(perpage){
   get_data( perpage,
             { url : 'Trans_paket_la/daftar_trans_paket_la',
               pagination_id: 'pagination_trans_paket_la',
               bodyTable_id: 'bodyTable_trans_paket_la',
               fn: 'ListDaftarTransPaketLA',
               warning_text: '<td colspan="5">Daftar transaksi paket la tidak ditemukan</td>',
               param : { search : $('#searchNamaKlien').val() } } );
}



function ListDaftarTransPaketLA(JSONData){
   var json = JSON.parse(JSONData);
   var tipe_paket = 'tipe paket tidak ditemukan.';
   var except_type = ['hotel', 'handling'];

   var except_header = ['Deskripsi', 'Check-in/Check-out', 'Day', 'Pax', 'Price', 'Amount'];
   var fasilitas = '';
   if( json.fasilitas.length > 0 ) {
      for ( let x in json.fasilitas ) {
         fasilitas += `<table class="table mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left border border-right-0 " style="width:10%;background-color: #e7e7e7;">INVOICE</td>
                              <td class="border border-left-0 border-right-0 border-top-1 px-2" style="width:1%;">:</td>
                              <td class="border text-left border-left-0 border-top-1 px-0" style="width:39%;">${json.fasilitas[x].invoice}</td>
                              <td class="text-left border border-right-0 " style="width:10%;background-color: #e7e7e7;">TYPE</td>
                              <td class="border border-left-0 border-right-0 border-top-1 px-2" style="width:1%;">:</td>
                              <td class="border text-left border-left-0 border-top-1 px-0"  style="width:39%;text-transform:uppercase;">${json.fasilitas[x].type.replace('_', ' ')}</td>
                           <tr>
                           <tr>
                             <td class="text-left border border-right-0 " style="width:10%;background-color: #e7e7e7;">TOTAL</td>
                              <td class="border border-left-0 border-right-0 border-top-1 px-2" style="width:1%;">:</td>
                              <td class="border text-left border-left-0 border-top-1 px-0" colspan="4" style="width:39%;">Rp ${numberFormat(json.fasilitas[x].total_price)}</td>
                           <tr>
                        </tbody>
                     </table>`;

         fasilitas +=`<table class="table mb-4 mt-1">
                        <thead>`;
            if( json.fasilitas[x].type == 'hotel' || json.fasilitas[x].type == 'handling') {
               fasilitas +=  `<tr>
                                 <th style="width:25%;background-color: #e7e7e7;">Deskripsi</th>
                                 <th style="background-color: #e7e7e7;">Check-in</th>
                                 <th style="background-color: #e7e7e7;">Check-out</th>
                                 <th style="background-color: #e7e7e7;">Day</th>
                                 <th style="background-color: #e7e7e7;">Pax</th>
                                 <th style="background-color: #e7e7e7;">Price</th>
                                 <th style="width:15%;background-color: #e7e7e7;">Aksi</th>
                              </tr>`;
            }else{
               fasilitas +=  `<tr>
                                 <th style="background-color: #e7e7e7;">Deskripsi</th>
                                 <th style="background-color: #e7e7e7;">Pax</th>
                                 <th style="background-color: #e7e7e7;">Price</th>
                                 <th style="width:15%;background-color: #e7e7e7;">Aksi</th>
                              </tr>`;
            }
         fasilitas +=  `</thead>
                        <tbody>`;
         if( json.fasilitas[x].type == 'hotel' || json.fasilitas[x].type == 'handling') {
            if( json.fasilitas[x].detail.length > 0  ) {
               for ( let c in  json.fasilitas[x].detail ) {
                  fasilitas += `<tr>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].description}</td>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].check_in}</td>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].check_out}</td>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].day}</td>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].pax}</td>
                                    <td class="align-middle">Rp ${numberFormat(json.fasilitas[x].detail[c].price)}</td>
                                    <td class="align-middle">
                                       <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Detail Item Paket LA"
                                          onclick="cetak_kwitansi_detail_item_paket_la('${json.fasilitas[x].detail[c].id}')" style="margin:.15rem .1rem  !important">
                                           <i class="fas fa-print" style="font-size: 11px;"></i>
                                       </button>
                                       <button type="button" class="btn btn-danger btn-action" title="Delete Detail Item Paket LA"
                                          onclick="delete_detail_item_paket_la('${json.fasilitas[x].detail[c].id}')" style="margin:.15rem .1rem  !important">
                                           <i class="fas fa-times" style="font-size: 11px;"></i>
                                       </button>
                                    </td>
                                </tr>`;  
               }
            }else{
                fasilitas =   `<tr>
                                 <td colspan="7">Detail Item Tidak Ditemukan</td>
                               </tr>`;
            }
         }else{
            if( json.fasilitas[x].detail.length > 0  ) {
               for ( let c in  json.fasilitas[x].detail ) {
                  fasilitas += `<tr>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].description}</td>
                                    <td class="align-middle">${json.fasilitas[x].detail[c].pax}</td>
                                    <td class="align-middle">Rp ${numberFormat(json.fasilitas[x].detail[c].price)}</td>
                                    <td class="align-middle">
                                       <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Detail Item Paket LA"
                                          onclick="cetak_kwitansi_detail_item_paket_la('${json.fasilitas[x].detail[c].id}')" style="margin:.15rem .1rem  !important">
                                           <i class="fas fa-print" style="font-size: 11px;"></i>
                                       </button>
                                       <button type="button" class="btn btn-danger btn-action" title="Delete Paket LA"
                                          onclick="delete_detail_item_paket_la('${json.fasilitas[x].detail[c].id}')" style="margin:.15rem .1rem  !important">
                                           <i class="fas fa-times" style="font-size: 11px;"></i>
                                       </button>
                                    </td>
                                </tr>`;  
               }
            }else{
               fasilitas =    `<tr>
                                 <td colspan="4">Detail Item Tidak Ditemukan</td>
                              </tr>`;
            }
         }               
         fasilitas +=  `</tbody>
                      </table>`;
      }
   }else{
      fasilitas = `<center>Daftar Item Fasilitas Tidak Ditemukan</center>`;
   }
   var html =  `<tr>
                  <td>${json.register_number}</td>
                  <td>
                     <ul class="pl-2 list">
                        <li>Nama Klien : ${json.client_name}</li>
                        <li>Nomor HP Klien : ${json.client_hp_number}</li>
                        <li>Alamat Klien : ${json.client_address}</li>
                     </ul>
                  </td>
                  <td >
                     ${fasilitas}
                  </td>
                  <td>
                     <ul class="pl-2 list">
                        <li>Total Harga : Rp ${numberFormat(json.total_price)}</li>
                        <li>Diskon : Rp ${numberFormat(json.discount)}</li>
                        <li>Sudah Dibayar : Rp ${numberFormat(json.sudah_dibayar)}</li>
                        <li>Sisa : Rp ${numberFormat(json.sisa)}</li>
                     </ul>
                  </td>
                  <td>`;
         if( json.status == 'close' ) {
            html += `<span class="float-center">Paket La ini sudah ditutup.</span>`;
         }else{
            html += `<button type="button" class="btn btn-default btn-action" title="Tambah Item Detail Paket LA"
                        onclick="add_item_detail_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-box" style="font-size: 11px;"></i>
                     </button>
                   
                     <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Awal Paket LA"
                        onclick="cetak_kwitansi_awal('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Pembayaran Paket LA"
                        onclick="pembayaran_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="far fa-money-bill-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Refund Paket LA"
                        onclick="refund_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-undo-alt" style="font-size: 11px;"></i>
                     </button>
                      <button type="button" class="btn btn-default btn-action" title="K & T Paket LA"
                        onclick="k_t_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-list-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Edit Paket LA"
                        onclick="edit_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Paket LA"
                        onclick="delete_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>`;
         }
         
         html += `</td>
               </tr>`;
   return html;
}

function add_item_detail_paket_la(id){
   $.confirm({
      columnClass: 'col-4',
      title: 'Pilih Tipe Item Paket LA',
      theme: 'material',
      content: form_select_item_paket_la(),
      closeIcon: false,
      buttons: {
         cancel:function () {
             return true;
         },
         simpan: {
            text: 'Lanjutkan',
            btnClass: 'btn-blue',
            action: function () {
               var tipe = $('#tipe').val();
               if( tipe == 0 ) {
                  frown_alert('Untuk melanjutkan anda harus memilih salah satu tipe item');
                  return false;
               }else{
                  if( tipe == 'hotel' || tipe == 'handling') {
                     hotel_handling(id, tipe);
                  }else{
                     add_detail_paket_la(id, tipe);
                  }
               }
            }
         }
      }
   });
}

function form_add_detail_paket_la(id, type) {
   var html = `<form id="form_utama" action="${baseUrl }Trans_paket_la/add_update_new_item" class="formName">
                  <div class="row px-0 py-3 mx-0">
                     <input type="hidden" name="id" value="${id}">
                     <input type="hidden" name="type" value="${type}">
                     <div class="col-12 py-2" style="background-color: #e3e3e3;">
                        <div class="row">
                           <div class="col-6">
                              <center>
                                 <label class="mb-0">DESKRIPSI</label>
                              </center>
                           </div>
                           <div class="col-2">
                              <center>
                                 <label class="mb-0">PAX</label>
                              </center>
                           </div>
                           <div class="col-2">
                              <center>
                                 <label class="mb-0">PRICE</label>
                              </center>
                           </div>
                        </div>  
                     </div>
                     <div class="col-12" id="area-row">
                        ${form_add_row()}
                     </div>
                     <div class="col-12 text-right py-1" style="background-color: #e3e3e3;">
                        <button type="button" class="btn btn-default" title="Delete Paket LA" onclick="add_row_tambah_row_paket_la()" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row Baru
                        </button>
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

function add_row_tambah_row_paket_la() {
   $('#area-row').append(form_add_row());
}

function form_add_row() {
   return `<div class="row py-3" style="background-color: #efefef;">
               <div class="col-6">
                  <div class="form-group mb-0">
                     <textarea class="form-control form-control-sm" name="deskripsi[]" id="deskripsi" placeholder="Deskripsi" rows="3" style="resize: none;"></textarea>
                  </div>
               </div>
               <div class="col-2">
                  <div class="form-group">
                     <input type="number" class="form-control form-control-sm" name="pax[]" value="" placeholder="Pax">
                  </div>
               </div>
               <div class="col-3">
                  <div class="form-group">
                     <input type="text" class="form-control form-control-sm currency" name="price[]" value="" placeholder="Price">
                  </div>
               </div>
               <div class="col-1 text-center">
                  <button type="button" class="btn btn-danger w-100" title="Delete Paket LA" onclick="delete_row_paket_la(this)" style="height: 32px;margin: 0px !important;">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </div>
            </div>`;
}

function delete_row_paket_la(e) {
   $(e).parent().parent().remove();
}

function add_detail_paket_la(id, type) {
   $.confirm({
      columnClass: 'col-9',
      title: 'Tambah Item Paket LA',
      theme: 'material',
      content: form_add_detail_paket_la(id, type),
      closeIcon: false,
      buttons: {
         cancel:function () {
             return true;
         },
         simpan: {
            text: 'Lanjutkan',
            btnClass: 'btn-blue',
            action: function () {
               ajax_submit_t1("#form_utama", function(e) {
                  e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                  if ( e['error'] == true ) {
                     return false;
                  } else {
                     get_trans_paket_la(20);
                  }
               });
            }
         }
      }
   });
}

function count_date_between_two_date(num) {
   let start_date = $('#check_in_' + num).val();
   let end_date = $('#check_out_' + num).val();
   // filter
   if( start_date != '' && end_date != ''){
      let date1 = new Date(start_date);
      let date2 = new Date(end_date);
      // The number of milliseconds in one day
      const ONE_DAY = 1000 * 60 * 60 * 24;
      // Calculate the difference in milliseconds
      const differenceMs = Math.abs(date1 - date2);
      // day
      var day = Math.round(differenceMs / ONE_DAY);
      // day
      $('#day_' +num ).val(day);
   }
}

function hotel_handling(id, type) {
   $.confirm({
      columnClass: 'col-12',
      title: 'Tambah Item Paket LA',
      theme: 'material',
      content: form_add_hotel_detail_paket_la(id, type),
      closeIcon: false,
      buttons: {
         cancel:function () {
             return true;
         },
         simpan: {
            text: 'Lanjutkan',
            btnClass: 'btn-blue',
            action: function () {
               ajax_submit_t1("#form_utama", function(e) {
                  e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                  if ( e['error'] == true ) {
                     return false;
                  } else {
                     get_trans_paket_la(20);
                  }
               });
            }
         }
      }
   });
}
function form_add_hotel_detail_paket_la(id, type){
   var html = `<form id="form_utama" action="${baseUrl }Trans_paket_la/add_update_new_item" class="formName">
                  <div class="row px-0 py-3 mx-0">
                     <input type="hidden" name="id" value="${id}">
                     <input type="hidden" name="type" value="${type}">
                     <div class="col-12 py-2" style="background-color: #e3e3e3;">
                        <div class="row">
                           <div class="col-3">
                              <center>
                                 <label class="mb-0">DESKRIPSI</label>
                              </center>
                           </div>
                           <div class="col-2">
                              <center>
                                 <label class="mb-0">CHECK IN</label>
                              </center>
                           </div>
                           <div class="col-2">
                              <center>
                                 <label class="mb-0">CHECK OUT</label>
                              </center>
                           </div>
                           <div class="col-1">
                              <center>
                                 <label class="mb-0">DAY</label>
                              </center>
                           </div>
                           <div class="col-1">
                              <center>
                                 <label class="mb-0">PAX</label>
                              </center>
                           </div>
                           <div class="col-2">
                              <center>
                                 <label class="mb-0">PRICE</label>
                              </center>
                           </div>
                        </div>  
                     </div>
                     <div class="col-12" id="area-row-hotel">
                       
                        ${form_add_row_hotel()}
                     </div>
                     <div class="col-12 text-right py-1" style="background-color: #e3e3e3;">
                        <button type="button" class="btn btn-default" title="Delete Paket LA" onclick="add_row_tambah_row_hotel_paket_la()" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row Baru
                        </button>
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

function form_add_row_hotel(){
   let num = parseInt($('#nums').val());
   let ns = num  + 1;
   $('#nums').val(ns);
   return `<div class="row py-3" style="background-color: #efefef;">
               <div class="col-3">
                  <div class="form-group mb-0">
                     <textarea class="form-control form-control-sm" name="deskripsi[]" id="deskripsi" placeholder="Deskripsi" rows="3" style="resize: none;"></textarea>
                  </div>
               </div>
               <div class="col-2">
                  <div class="form-group">
                     <input type="date" class="form-control form-control-sm" id="check_in_${num}" name="check_in[]" value="" onChange="count_date_between_two_date(${num})" placeholder="Check In">
                  </div>
               </div>
               <div class="col-2">
                  <div class="form-group">
                     <input type="date" class="form-control form-control-sm" id="check_out_${num}" name="check_out[]" value="" onChange="count_date_between_two_date(${num})" placeholder="Check Out">
                  </div>
               </div>
               <div class="col-1">
                  <div class="form-group">
                     <input type="number" class="form-control form-control-sm" id="day_${num}" name="day[]" value="" disabled="" placeholder="Day">
                  </div>
               </div>
               <div class="col-1">
                  <div class="form-group">
                     <input type="number" class="form-control form-control-sm" name="pax[]" value="" placeholder="Pax">
                  </div>
               </div>
               <div class="col-2">
                  <div class="form-group">
                     <input type="text" class="form-control form-control-sm currency" name="price[]" value="" placeholder="Price">
                  </div>
               </div>
               <div class="col-1 text-center">
                  <button type="button" class="btn btn-danger w-100" title="Delete Paket LA" onclick="delete_row_paket_la(this)" style="height: 32px;margin: 0px !important;">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </div>
            </div>`;



}


function add_row_tambah_row_hotel_paket_la() {
   $('#area-row-hotel').append(form_add_row_hotel());
}

function form_select_item_paket_la() {
    var html = `<form id="form_utama" class="formName">
                  <div class="row px-0 py-3 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Pilih Tipe Item</label>
                                 <select id="tipe" name="tipe" class="form-control form-control-sm" title="Pilih Tipe Item">
                                    <option value="0">Pilih Item Paket LA</option>
                                    <option value="tiket_pesawat">Tiket Pesawat</option>
                                    <option value="hotel">Hotel</option>
                                    <option value="bus">Bus</option>
                                    <option value="mobil">Mobil</option>
                                    <option value="handling" >Handling</option>
                                    <option value="visa">Visa</option>
                                    <option value="mutowif_tour_guide">Mutowif / Tour Guide</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;            

}

function delete_detail_item_paket_la(id){
    ajax_x(
      baseUrl + "Trans_paket_la/delete_detail_item_paket_la", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if ( e['error'] == true ) {
            return false;
         } else {
            get_trans_paket_la(20);
         }
      },[{id:id}]
   );

}

function cetak_kwitansi_awal(id){
   ajax_x(
      baseUrl + "Trans_paket_la/cetak_kwitansi_pertama_paket_la", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if ( e['error'] == true ) {
            return false;
         } else {
            get_trans_paket_la(20);
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{id:id}]
   );
}

function refund_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/info_refund_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Refund Transaksi Paket LA',
               theme: 'material',
               content: form_refund_trans_paket_la(id, e['invoice'], e['wasPaid']),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_trans_paket_la(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function form_refund_trans_paket_la(id, invoice, wasPaid){
   var html = `<form action="${baseUrl }Trans_paket_la/proses_refund_trans_paket_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <label class="float-right">Nomor Invoice : <b style="color:red;">#${invoice}</b></label>
                        <input type="hidden" name="invoice" value="${invoice}">
                        <input type="hidden" name="id" value="${id}">
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Sudah Dibayar</label>
                                 <input type="text" id="sudah_dibayar" value="Rp ${numberFormat(wasPaid)}" class="form-control form-control-sm" placeholder="Sudah Dibayar" disabled/>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Refund</label>
                                 <input type="text" name="refund" id="refund" class="form-control form-control-sm currency" placeholder="Refund" onKeyup="countRefund()" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nama Penyetor</label>
                                 <input type="text" name="nama_penyetor" id="nama_penyetor" class="form-control form-control-sm" placeholder="Nama Penyetor"/>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nomor HP Penyetor</label>
                                 <input type="text" name="no_hp_penyetor" id="no_hp_penyetor" class="form-control form-control-sm" placeholder="Nomor HP Penyetor"/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alamat Penyetor</label>
                                 <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3"
                                    style="resize: none;" placeholder="Alamat Penyetor" required></textarea>
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


function edit_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/info_edit_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-5',
               title: 'Edit Transaksi Paket LA',
               theme: 'material',
               content: formaddupdate_trans_paket_la('', JSON.stringify(e['kostumer']), JSON.stringify(e['value'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              // window.open(baseUrl + "Kwitansi/", "_blank");
                              get_trans_paket_la(20);
                           }
                        });
                     }
                  }

               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function add_trans_paket_la(){
   ajax_x(
      baseUrl + "Trans_paket_la/get_info_trans_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-5',
               title: 'Tambah Transaksi Paket LA',
               theme: 'material',
               content: formaddupdate_trans_paket_la(e['nomor_register'], JSON.stringify(e['kostumer'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              // window.open(baseUrl + "Kwitansi/", "_blank");
                              get_trans_paket_la(20);
                           }
                        });
                     }
                  }

               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function formaddupdate_trans_paket_la(nomor_register, JSONKostumer, JSONValue){
   var kostumer = JSON.parse(JSONKostumer);
   var paket_la_id = '';
   var diskon = '';
   var jamaah = '';
   var kostumer_selected = '';
   var tanggal_keberangkatan = '';
   var tanggal_kepulangan = '';
   var no_register = '';
   var total_price = '';
   var no_register = `<input type="hidden" name="no_register" value='${nomor_register}' >`;

   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      paket_la_id = `<input type="hidden" name="paket_la_id" value="${value.id}">`;
      no_register = `<input type="hidden" name="no_register" value='${value.register_number}' >`;
      kostumer_selected = value.costumer_id;
      nomor_register = value.register_number;
            diskon = value.discount;
      jamaah = value.jamaah;
      tanggal_keberangkatan = value.departure_date;
      tanggal_kepulangan = value.arrival_date;
   }
   var html = `<form action="${baseUrl }Trans_paket_la/proses_addupdate_trans_paket_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        ${paket_la_id}
                        ${no_register}
                        <label class="float-right">Nomor Register : <b style="color:red;">#${nomor_register}</b></label>
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Kostumer Paket LA</label>
                                 <select class="form-control form-control-sm" name="kostumer_paket_la" id="kostumer_paket_la" >
                                    <option value="0">Pilih kostumer paket la</option>`;
                        for( x in kostumer ) {
                           html += `<option value="${x}" ${x == kostumer_selected ? 'selected' : '' }>${kostumer[x]['name']} : ${kostumer[x]['mobile_number']}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Diskon</label>
                                 <input type="text"  name="diskon" value="${diskon}" class="form-control form-control-sm currency" placeholder="Diskon" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>J. Jamaah</label>
                                 <input type="number" name="jamaah" value="${jamaah}" class="form-control form-control-sm" placeholder="Jumlah Jamaah" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Tanggal Keberangkatan</label>
                                 <input type="date" name="tanggal_keberangkatan" value="${tanggal_keberangkatan}" class="form-control form-control-sm" placeholder="Tanggal Keberangkatan" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Tanggal Kepulangan</label>
                                 <input type="date" name="tanggal_kepulangan" value="${tanggal_kepulangan}" class="form-control form-control-sm currency" placeholder="Diskon" />
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

// get list fasilitas tipe paket la
function getListFasilitasTipePaketLA(){
   var jenis_paket = $('#jenis_paket').val();
   var html = '';
   var total = 0;
   if( jenis_paket != 0 ) {
      var jsonpakettype = JSON.parse($('#jsonpakettype').val());
      var fasilitas = jsonpakettype[jenis_paket]['list_fasilitas'];
      for( x in fasilitas ) {
         html += row_fasilitas($('#jsonfasilitas').val(), fasilitas[x][0], fasilitas[x][1], 'Rp ' + numberFormat(fasilitas[x][3]));
      }
   } else {
      html += '<label style="font-size: 11px;font-weight: normal;">Daftar fasilitas tidak ditemukan</label>';
   }
   $('#list_fasilitas').html(html);
   sumTotalFasilitas();
}

// jumlah total fasilitas
function sumTotalFasilitas(){
   var jumlah_jamaah = $('input[name^=jamaah]').val();
   if(jumlah_jamaah == '' ){
      jumlah_jamaah = 0;
   }
   var diskon = hide_currency($('input[name^=diskon]').val());
   var pax = $('input[name^=pax]').map(function(idx, elem) {
      return $(elem).val();
   }).get();
   var harga = $('input[name^=harga]').map(function(idx, elem) {
      return hide_currency($(elem).val());
   }).get();
   var total = 0;
   for( x in pax ){
      total = total + (pax[x] * harga[x]);
   }
   $('#total').val( 'Rp ' + numberFormat((total * jumlah_jamaah) - diskon) );
   $('#total_harga_fasilitas').html(`<div class="row"><div class="col-7 text-left">Total Harga Fasilitas</div><div class="col-5 pl-3 text-left">Rp ${numberFormat(total)}</div></div>`);
}

function pembayaran_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/get_info_pembayaran", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-9',
               title: 'Form Pembayaran Transaksi Paket LA',
               theme: 'material',
               content: formpembayaran_trans_paket_la(id, JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  cetak:{
                     text: 'Cetak Kwitansi Terakhir',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_x(
                           baseUrl + "Trans_paket_la/lastKwitansiPembayaran", function(e) {
                             window.open(baseUrl + "Kwitansi/", "_blank");
                           },[{id: id}]
                        );
                     }
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_trans_paket_la(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }

               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function formpembayaran_trans_paket_la(id, JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Trans_paket_la/proses_addupdate_pembayaran_trans_paket_la" id="form_utama" class="formName">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <label class="float-right">Nomor Invoice : <b style="color:red;">#${json.invoice}</b></label>
                        <input type="hidden" name="invoice" value="${json.invoice}">
                        <input type="hidden" name="id" value="${id}">
                     </div>
                     <div class="col-12">
                        <table class="table table-hover tablebuka">
                           <thead>
                              <tr>
                                 <th style="width:15%;">Nomor Invoice</th>
                                 <th style="width:20%;">Biaya</th>
                                 <th style="width:20%;">Status</th>
                                 <th style="width:20%;">Tanggal Transaksi</th>
                                 <th style="width:20%;">Penerima</th>
                                 <th style="width:5%;">Aksi</th>
                              </tr>
                           </thead>
                           <tbody id="bodyTable_trans_paket_la">`;
               if( json.riwayat.length > 0 ) {
                  for( x in json.riwayat ) {
                     html += `<tr>
                                 <td>${json.riwayat[x]['invoice']}</td>
                                 <td>Rp ${numberFormat(json.riwayat[x]['paid'])}</td>
                                 <td>${json.riwayat[x]['status']}</td>
                                 <td>${json.riwayat[x]['tanggal_transaksi']}</td>
                                 <td>${json.riwayat[x]['receiver']}</td>
                                 <td>
                                    <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Paket LA"
                                        onclick="cetak_kwitansi_paket_la('${json.riwayat[x]['invoice']}')">
                                        <i class="fas fa-print" style="font-size: 11px;"></i>
                                    </button>
                                 </td>
                              </tr>`;
                  }
               }else{
                  html += `<tr>
                              <td colspan="6">Daftar riwayat transaksi paket la tidak ditemukan</td>
                           </tr>`;
               }
               html +=    `</tbody>
                        </table>
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Total Harga</label>
                                 <input type="text" id="total_harga" value="Rp ${numberFormat(json.total_harga)}" class="form-control form-control-sm" placeholder="Total Harga" disabled/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Bayar</label>
                                 <input type="text" name="bayar" id="bayar" class="form-control form-control-sm currency" placeholder="Bayar" onKeyup="countPembayaran()" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sudah Dibayar</label>
                                 <input type="text" id="sudah_dibayar" value="Rp ${numberFormat(json.total_bayar)}" class="form-control form-control-sm" placeholder="Sudah Dibayar" disabled/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sisa</label>
                                 <input type="text" id="sisa" value="Rp ${numberFormat(json.sisa)}" class="form-control form-control-sm" placeholder="Sisa" disabled/>
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nama Penyetor</label>
                                 <input type="text" name="nama_penyetor" class="form-control form-control-sm" placeholder="Nama Penyetor" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nomor HP Penyetor</label>
                                 <input type="text" name="no_hp_penyetor" id="no_hp_penyetor" value="" class="form-control form-control-sm" placeholder="No HP Penyetor" />
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Alamat Penyetor</label>
                                 <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3"
                                    style="resize: none;" placeholder="Alamat Penyetor" required></textarea>
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

function cetak_kwitansi_paket_la( invoice ){

   ajax_x(
      baseUrl + "Trans_paket_la/cetak_kwitansi_paket_la", function(e) {
         if( e['error'] == false ){
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
            if ( e['error'] == true ) {
               return false;
            } else {
               get_trans_paket_la(20);
               window.open(baseUrl + "Kwitansi/", "_blank");
            }

            // $.confirm({
            //    columnClass: 'col-6',
            //    title: 'Refund Transaksi Paket LA',
            //    theme: 'material',
            //    content: form_refund_trans_paket_la(id, e['invoice'], e['wasPaid']),
            //    closeIcon: false,
            //    buttons: {
            //       cancel:function () {
            //           return true;
            //       },
            //       simpan: {
            //          text: 'Simpan',
            //          btnClass: 'btn-blue',
            //          action: function () {
            //             ajax_submit_t1("#form_utama", function(e) {
            //                e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
            //                if ( e['error'] == true ) {
            //                   return false;
            //                } else {
            //                   get_trans_paket_la(20);
            //                   window.open(baseUrl + "Kwitansi/", "_blank");
            //                }
            //             });
            //          }
            //       }
            //    }
            // });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{invoice:invoice}]
   );

}

function countPembayaran(){
   var total_harga = hide_currency($('#total_harga').val());
   var sudah_dibayar = hide_currency($('#sudah_dibayar').val());
   var pembayaran = hide_currency($('#bayar').val());
   if( total_harga >= (pembayaran + sudah_dibayar) ) {
      var sisa = total_harga - (sudah_dibayar + pembayaran);
      $('#sisa').val('Rp ' + numberFormat(sisa));
   }else{
      var minPembayaran = pembayaran.toString().length - 1;
      var realPembayaran = pembayaran.toString().substring(0, parseInt(minPembayaran));
      $('#bayar').val('Rp ' + numberFormat(realPembayaran) );
      frown_alert('Pembayaran tidak boleh lebih dari total harga');
   }
}


function checkDistincJenisPaket(e){
   var list_jenis_fasilitas = new Array();
   $('.jenisFasilitas').each(function(index) {
      if( $(this).val() != 0 ) {
         list_jenis_fasilitas.push($(this).val());
      }
   });
   var unique_jenis_fasilitas = list_jenis_fasilitas.filter((v, i, a) => a.indexOf(v) === i);
   if(list_jenis_fasilitas.length != unique_jenis_fasilitas.length ) {
      $(e).val(0);
      frown_alert('Tidak dapat memilih fasilitas yang sama.');
   }
}

function form_k_t_paket_la(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<div class="content" id="contents">
                     <div class="col-12 col-lg-12 my-3 px-0">
                        <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                           <tbody>
                              <tr>
                                 <td style="width:45%;" class="text-left">KEGIATAN ANGGARAN CLIENT ${json.client_name.toUpperCase()}</td>
                                 <td style="width:1%;">:</td>
                                 <td style="width:54%;" class="text-left total_anggaran" id="total_anggaran">Rp ${numberFormat(json.total_price)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">AKTUALISASI BELANJA CLIENT ${json.client_name.toUpperCase()}</td>
                                 <td>:</td>
                                 <td class="text-left undefined" id="aktualisasi_anggaran">Rp ${numberFormat(json.total_aktualisasi)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">KEUNTUNGAN PROGRAM CLIENT ${json.client_name.toUpperCase()}</td>
                                 <td>:</td>
                                 <td class="text-left undefined" id="keuntungan">Rp ${numberFormat(json.keuntungan)}</td>
                              </tr>
                           </tbody>
                         </table>
                     </div>
                     <div class="col-12 my-3 px-0">
                        <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                           <thead>
                              <tr>
                                 <th style="width:5%;">NO</th>
                                 <th style="width:38%;">URAIAN</th>
                                 <th style="width:5%;">QT</th>
                                 <th style="width:19%;">HARGA</th>
                                 <th style="width:19%;">JUMLAH HARGA</th>
                                 <th style="width:14%;">AKSI</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr style="font-weight:bold;">
                                 <td></td>
                                 <td class="text-left">Potensi Pendapatan Paket LA Client ${json.client_name}</td>
                                 <td></td>
                                 <td></td>
                                 <td class="text-right">Rp ${numberFormat(json.total_price)}</td>
                                 <td></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-12 my-3 px-0">
                        <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                           <tbody id="table_rincian">
                              <tr style="background-color: #f1f5fd;font-weight:bold;">
                                 <td style="width:5%;">A</td>
                                 <td style="width:38%;" class="text-left">Rincian Pendapatan Paket LA Client ${json.client_name}</td>
                                 <td style="width:5%;"></td>
                                 <td style="width:19%;"></td>
                                 <td style="width:19%;" class="text-right">Rp ${numberFormat(json.total_price)}</td>
                                 <td style="width:14%;"></td>
                              </tr>
                              <tr>
                                 <td >1</td>
                                 <td class="text-left">Sudah Dibayar</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right">Rp ${numberFormat(json.sudah_bayar)}</td>
                                 <td ></td>
                              </tr>
                              <tr>
                                 <td >2</td>
                                 <td class="text-left">Diskon</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right">Rp ${numberFormat(json.discount)}</td>
                                 <td ></td>
                              </tr>
                              <tr>
                                 <td >3</td>
                                 <td class="text-left">Piutang</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right">Rp ${numberFormat( ( json.total_price -json.discount ) - json.sudah_bayar )}</td>
                                 <td ></td>
                              </tr>
                              <tr style="background-color: #f1f5fd;font-weight:bold;">
                                 <td style="vertical-align: middle;">B</td>
                                 <td colspan="3" style="vertical-align: middle;" class="text-left">Aktualisasi Kegiatan Anggaran Paket LA Client ${json.client_name}</td>
                                 <td style="vertical-align: middle;" class="text-right">Rp ${numberFormat(json.total_aktualisasi)}</td>
                                 <td style="vertical-align: middle;">
                                    <button class="btn btn-default btn-action" title="Tambah uraian aktualisasi anggaran" onclick="add_aktualisasi('${json.id}')">
                                        <i class="fas fa-plus" style="font-size: 11px;"></i>
                                    </button>
                                 </td>
                              </tr>`;
                  let i = 1;
                  for( x in json.aktualisasi ){
                     html += `<tr>
                                 <td class="position">${i}</td>
                                 <td class="text-left" id="u${i}">${json.aktualisasi[x]['name']}</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right" id="h${i}">Rp ${numberFormat( json.aktualisasi[x]['harga'] )}</td>
                                 <td class="px-0" id="btn${i}">
                                    <button class="btn btn-default btn-action mx-1" title="Edit Kas Transaksi" onclick="edit_kas_transaksi_paket_la( ${i}, '${json.aktualisasi[x]['id']}', '${json.aktualisasi[x]['ket']}', '${json.aktualisasi[x]['action']}', '${json.id}')">
                                        <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                                    </button>`;
                        if( json.aktualisasi[x]['ket'] == "add" ){
                           html +=    `<button class="btn btn-default btn-action mx-1" title="Delete Kas Transaksi" onclick="delete_kas_transaksi_paket_la('${json.aktualisasi[x]['id']}', '${json.id}')">
                                           <i class="fas fa-times" style="font-size: 11px;"></i>
                                       </button>`;
                        }
                        html += `</td>
                              </tr>`;
                     i++;
                  }
               html +=    `</tbody>
                        </table>
                     </div>
               </div>
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

// close paket la
// function close_paket_la(id){
//    ajax_x(
//       baseUrl + "Trans_paket_la/close_paket_la", function(e) {
//          if( e['error'] == false ){
            
//             get_trans_paket_la(20);
//             smile_alert(e['error_msg']);


//          }else{
//             frown_alert(e['error_msg']);
//          }
//       },[{id:id}]
//    );
// }

function edit_kas_transaksi_paket_la(position, id, ket, action, paket_la_id) {
   if( $('.input_harga').length == 0  ) {
      if( ket == 'add' ) {
         $('#u'+position).html(`<input type="text" id="uraian${position}" value="${$('#u'+position).text()}" class="form-control form-control-sm" placeholder="Uraian">`);
      }
      $('#h'+position).html(`<input type="text" value="${$('#h'+position).text()}" id="harga${position}" class="form-control form-control-sm currency" placeholder="Harga">`);
      var btn =  `<button class="btn btn-default btn-action mx-1 input_harga" title="Simpan Kas Transaksi"
                        onclick="save_kas_transaksi_paket_la( ${position}, '${id}', '${ket}', '${action}', '${paket_la_id}')">
                     <i class="fas fa-save" style="font-size: 11px;"></i>
                  </button>`;
      if( ket == "add" ) {
          btn += `<button class="btn btn-default btn-action mx-1" title="Delete Kas Transaksi"
                        onclick="delete_kas_transaksi_paket_la('${id}', '${paket_la_id}')">
                     <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>`;
      }
      $('#btn'+position).html(btn);
   }else{
      frown_alert('Anda tidak dapat mengedit kolom ini, karena masih terdapat kolom yang belum disimpan');
   }
}

function delete_kas_transaksi_paket_la(id, paket_la_id){
   ajax_x(
      baseUrl + "Trans_paket_la/delete_kas_transaksi_paket_la", function(e) {
         if( e['error'] == false ){
            $('#contents').replaceWith(form_k_t_paket_la(JSON.stringify(e['data'])));
         }else{
            frown_alert(e['error_msg']);
         }
      },[{paket_la_id:paket_la_id, id:id}]
   );
}

function add_aktualisasi(paket_la_id){
   if( $('.input_harga').length == 0  ) {
      var position = $('.position').length + 1;
      var html = `<tr>
                  <td class="position">${position}</td>
                  <td class="text-left" id="u${position}">
                     <input type="text" id="uraian${position}" value="" class="form-control form-control-sm" placeholder="Uraian"></td>
                  <td ></td>
                  <td ></td>
                  <td class="text-right" id="h${position}">
                     <input type="text" value="" id="harga${position}" class="form-control form-control-sm currency" placeholder="Harga">
                  </td>
                  <td class="px-0" id="btn${position}">
                     <button class="btn btn-default btn-action mx-1 input_harga" title="Simpan Kas Transaksi"
                           onclick="save_kas_transaksi_paket_la( ${position}, '', 'add', 'insert', '${paket_la_id}')">
                        <i class="fas fa-save" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
      $('#table_rincian').append(html);
   }else{
      frown_alert('Anda tidak dapat mengedit kolom ini, karena masih terdapat kolom yang belum disimpan');
   }
}

function save_kas_transaksi_paket_la(position, id, ket, action, paket_la_id){
   var uraian = '';
   var harga = $('#harga'+position).val();
   if( ket == 'add'){
      uraian = $('#uraian'+position).val();
   }
   ajax_x(
      baseUrl + "Trans_paket_la/proses_addupdate_kas_transaksi_paket_la", function(e) {
         if( e['error'] == false ){
            $('#contents').replaceWith(form_k_t_paket_la(JSON.stringify(e['data'])));
         }else{
            frown_alert(e['error_msg']);
         }
      },[{paket_la_id:paket_la_id, id:id, ket:ket, action:action, harga:harga, uraian:uraian}]
   );
}

function k_t_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/get_info_kas_transaksi", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-8',
               title: 'K & T Paket LA',
               theme: 'material',
               content: form_k_t_paket_la(JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  tutup:function () {
                      return true;
                  },
                  tutup_paket: {
                     text: 'Tutup Paket',
                     btnClass: 'btn-red',
                     action: function () {

                        $.confirm({
                           columnClass: 'col-3',
                           title: 'K & T Paket LA',
                           theme: 'material',
                           content: 'Apakah anda yakin ingin menutup Paket La ini?',
                           closeIcon: false,
                           buttons: {
                              tidak:function () {
                                  return true;
                              },
                              iya: {
                                 text: 'Iya',
                                 btnClass: 'btn-red',
                                 action: function () {
                                    ajax_x(
                                       baseUrl + "Trans_paket_la/close_paket_la", function(e) {
                                          if( e['error'] == false ){
                                             get_trans_paket_la(20);
                                             smile_alert(e['error_msg']);
                                          }else{
                                             frown_alert(e['error_msg']);
                                          }
                                       },[{id:id}]
                                    );
                                 }
                              }
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function delete_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/delete_paket_la", function(e) {
         if( e['error'] == false ){
             get_trans_paket_la(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
