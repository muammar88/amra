function open_paket(paket_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/open_paket", function(e) {
         if( e['error'] != true) {
            get_data_k_t(paket_id);
            $('.tabletutup').addClass('tablebuka').removeClass('tabletutup');
            $('#status_paket').val('buka');
         }
         // alert
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
   },[{paket_id:paket_id }]);
}



function close_paket(paket_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/close_paket", function(e) {
         if( e['error'] != true) {
            get_data_k_t(paket_id);
            $('.tablebuka').addClass('tabletutup').removeClass('tablebuka');
            $('#status_paket').val('tutup');
         }
         // alert
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);

   },[{paket_id:paket_id }]);
}


function delete_detail_aktualisasi_anggaran( aktualisasi_detail_id ){
   ajax_x_t2(
      baseUrl + "Trans_paket/delete_aktualisasi_anggaran_detail", function(e) {
         get_data_k_t(e['paket_id']);
   },[{aktualisasi_detail_id:aktualisasi_detail_id }]);
}


function edit_detail_aktualisasi_anggaran(aktualisasi_detail_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/get_edit_info_aktualisasi_anggaran_detail", function(e) {
         var paket_id = e['paket_id'];
         $.confirm({
            title: 'Form Edit Aktualisasi Anggaran',
            theme: 'material',
            columnClass: 'col-4',
            content: formAddUpdateAktualisasiAnggaranDetail( e['aktualisasi_id'], e['value']),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        get_data_k_t(paket_id);
                     });
                  }
               }
            }
         });
      },[{aktualisasi_detail_id:aktualisasi_detail_id}]
   );
}


function formAddUpdateAktualisasiAnggaranDetail(aktualisasi_id, JSONValue){
   var uraian = '';
   var unit = '';
   var harga = kurs + ' 0';
   var id_area = '';
   if( JSONValue != undefined ){
      id_area = `<input type="hidden" name="aktualisasi_detail_id" value="${JSONValue.id}">`;
      uraian = JSONValue.uraian;
      unit = JSONValue.unit;
      harga = kurs + ' ' + numberFormat(JSONValue.biaya);
   }
   var form = `<form action="${baseUrl }Trans_paket/add_update_aktualisasi_anggaran_detail" id="form_utama" class="formName ">
                  <input type="hidden" name="aktualisasi_id" value="${aktualisasi_id}">
                  ${id_area}
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Uraian</label>
                           <textarea class="form-control form-control-sm" name="uraian" rows="3"
                              style="resize: none;" placeholder="Uraian anggaran" required>${uraian}</textarea>
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Unit</label>
                           <input type="number" required name="unit" placeholder="Unit" class="form-control form-control-sm"
                              id="unit" value="${unit}">
                        </div>
                     </div>
                     <div class="col-8" >
                        <div class="form-group">
                           <label>Harga</label>
                           <input type="text" required="" name="harga" placeholder="Harga"
                              class="form-control form-control-sm currency" id="harga" value="${harga}">
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
   return form;
}


function add_detail_aktualisasi_anggaran(aktualisasi_id){
   $.confirm({
      title: 'Form Tambah Aktualisasi Detail',
      theme: 'material',
      columnClass: 'col-5',
      content: formAddUpdateAktualisasiAnggaranDetail(aktualisasi_id),
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         formSubmit: {
            text: 'Simpan',
            btnClass: 'btn-blue',
            action: function () {
               ajax_submit_t1("#form_utama", function(e) {
                  get_data_k_t(e['paket_id']);
               });
            }
         }
      }
   });
}

function delete_aktualisasi_anggaran(aktualisasi_id) {
   $.confirm({
      title: 'Peringatan',
      theme: 'material',
      columnClass: 'col-4',
      content: 'Jika anda menghapus rincian aktualisasi anggaran ini, maka semua detail dibawahnya akan ikut terhapus. Apa anda ingin melanjutkan proses ini?.',
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         formSubmit: {
            text: 'Ya',
            btnClass: 'btn-blue',
            action: function () {
               ajax_x_t2(
                  baseUrl + "Trans_paket/delete_aktualisasi_anggaran", function(e) {
                     get_data_k_t(e['paket_id']);
               },[{aktualisasi_id:aktualisasi_id }]);
            }
         }
      }
   });
}

function edit_aktualisasi_anggaran(aktualisasi_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/get_edit_info_aktualisasi_anggaran", function(e) {
         var paket_id = e['paket_id'];
         $.confirm({
            title: 'Form Edit Aktualisasi Anggaran',
            theme: 'material',
            columnClass: 'col-4',
            content: addUpdateAktualisasi(paket_id, e['data'], e['value']),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     var error = 0;
                     var error_msg = 'berhasil';
                     var number = $('#number').val().split(",");
                     var nomor = $('#nomor').val();

                     if( number.includes(nomor) ){
                        error = 1;
                        error_msg = 'Nomor sudah terdaftar di rincian anggaran yang lain.';
                     }
                     if( error == 0 ){
                        ajax_submit_t1("#form_utama", function(e) {
                           get_data_k_t(paket_id);
                        });
                     }else{
                        frown_alert(error_msg);
                        return false;
                     }
                     error = 0;
                  }
               }
            }
         });
      },[{aktualisasi_id:aktualisasi_id}]
   );
}


function addUpdateAktualisasi(paket_id, JSONNumber, JSONValue ){
   var nomor = '';
   var uraian = '';
   var id_area = '';
   if(JSONValue != undefined ){
      var value = JSONValue;
      id_area = `<input type="hidden" id="aktualisasi_id" name="aktualisasi_id" value="${value.id}">`;
      nomor = value.number;
      uraian = value.uraian;
   }

   var form = `<form action="${baseUrl }Trans_paket/add_update_aktualisasi_anggaran" id="form_utama" class="formName ">
                  <input type="hidden" id="number" value="${JSONNumber}">
                  <input type="hidden" name="paket_id" value="${paket_id}">
                  ${id_area}
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Nomor Rician Anggaran</label>
                           <input type="number" required="" name="nomor" placeholder="Nomor" class="form-control form-control-sm" id="nomor" value="${nomor}">
                        </div>
                     </div>
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Uraian Rincian Anggaran</label>
                           <textarea class="form-control form-control-sm" name="uraian" rows="3" style="resize: none;" required placeholder="Uraian anggaran">${uraian}</textarea>
                        </div>
                     </div>
                  </div>
               </form>`;
   return form;
}

function add_aktualisasi_k_t(paket_id){

   console.log("123213213");
   ajax_x_t2(
      baseUrl + "Trans_paket/get_info_aktualisasi", function(e) {
         $.confirm({
            title: 'Form Add Aktualisasi Anggaran',
            theme: 'material',
            columnClass: 'col-4',
            content: addUpdateAktualisasi(paket_id, e['data']),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     var error = 0;
                     var error_msg = 'berhasil';
                     var number = $('#number').val().split(",");
                     var nomor = $('#nomor').val();
                     if( number.includes(nomor) ){
                        error = 1;
                        error_msg = 'Nomor sudah terdaftar di rincian anggaran yang lain.';
                     }
                     if( error == 0 ){
                        ajax_submit_t1("#form_utama", function(e) {
                           get_data_k_t(paket_id);
                        });
                     }else{
                        frown_alert(error_msg);
                        return false;
                     }
                     error = 0;
                  }
               }
            }
         });
      },[{paket_id:paket_id}]
   );
}

function titleListK_T(label, value, idValue, classValue){
   return  `<tr>
               <td style="width:25%;" class="text-left">${label}</td>
               <td style="width:1%;">:</td>
               <td style="width:74%;" class="text-left ${classValue}" id="${idValue}">${value}</td>
            </tr>`;
}

function headAktualisasi(number, label, value){
   return  `<tr style="background-color: #f1f5fd;">
               <td style="width:3%;"><b>${number}</b></td>
               <td colspan="5" class="text-left" style="width:69%;">${label}</td>
               <td class="text-right" style="width:13%;">${value}</td>
               <td style="width:15%;"></td>
            </tr>`;
}

function headAktualisasiBtn(paket_id, number, label, value, status){
   var form = `<tr style="background-color: #f1f5fd;">
                  <td style="width:3%;vertical-align: middle;"><b>${number}</b></td>
                  <td colspan="5" class="text-left" style="vertical-align: middle;width:69%;">${label}</td>
                  <td class="text-right" style="width:13%;vertical-align: middle;">${value}</td>
                  <td style="width:15%;vertical-align: middle;">`;
         if( status == 'buka' ){
            form += `<button class="btn btn-default btn-action" title="Tambah uraian aktualisasi anggaran"
                         onclick="add_aktualisasi_k_t(${paket_id})">
                         <i class="fas fa-plus" style="font-size: 11px;"></i>
                     </button>`;
         }else{
            form += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
         }
         form += `</td>
               </tr>`;
   return form;
}

function Aktualisasi(number, label, value){
   return  `<tr>
               <td style="width:3%;">${number}</td>
               <td colspan="5" class="text-left">${label}</td>
               <td class="text-right" style="width:13%;">${value}</td>
               <td style="width:15%;"></td>
            </tr>`;
}

function AktualisasiBtn(aktualisasi_id, number, label, value, status){
   var form = `<tr>
                  <td style="width:3%;vertical-align: middle;">${number}</td>
                  <td colspan="5" class="text-left" style="vertical-align: middle;">${label}</td>
                  <td class="text-right" style="width:13%;vertical-align: middle;">${value}</td>
                  <td style="width:15%;vertical-align: middle;">`;
         if( status == 'buka' ) {
            form +=    `<button class="btn btn-default btn-action" title="Tambah detail uraian aktualisasi anggaran" onclick="add_detail_aktualisasi_anggaran(${aktualisasi_id})">
                           <i class="fas fa-plus" style="font-size: 11px;"></i>
                        </button>
                        <button class="btn btn-default btn-action" title="Edit uraian aktualisasi anggaran" onclick="edit_aktualisasi_anggaran(${aktualisasi_id})">
                           <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                        </button>
                        <button class="btn btn-default btn-action" title="Delete uraian aktualisasi anggaran" onclick="delete_aktualisasi_anggaran(${aktualisasi_id})">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>`;
         }else{
            form += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
         }
         form += `</td>
               </tr>`;
   return form;
}

function detailAktualisasiBtn(detail_aktualisasi_id, label, unit, price_per_unit, value, status){
   var form = `<tr>
                  <td style="width:3%;vertical-align: middle;"></td>
                  <td style="width:3%;vertical-align: middle;">-</td>
                  <td class="text-left" style="width:27%;vertical-align: middle;">${label}</td>
                  <td style="width:3%;vertical-align: middle;">${unit}</td>
                  <td class="text-left" style="vertical-align: middle;width:12%;">${price_per_unit}</td>
                  <td class="text-left"  style="vertical-align: middle;width:24%;"></td>
                  <td class="text-right" style="width:13%;vertical-align: middle;">${value}</td>
                  <td style="width:15%;vertical-align: middle;">`;
      if( status == 'buka' ) {
         form +=       `<button class="btn btn-default btn-action" title="Edit detail uraian aktualisasi anggaran" onclick="edit_detail_aktualisasi_anggaran(${detail_aktualisasi_id})">
                           <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                        </button>
                        <button class="btn btn-default btn-action" title="Delete detail uraian aktualisasi anggaran" onclick="delete_detail_aktualisasi_anggaran(${detail_aktualisasi_id})">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>`;
      } else {
         form += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
      }
      form +=    `</td>
               </tr>`;
   return  form;
}

function k_t(paket_id){
   var html   =  `<div class="col-4 col-lg-4 mt-3 px-0">
                     <button class="btn btn-default buka_tutup_paket" id="buku_tutup_paket" type="button" onclick="close_paket(${paket_id})">
                        <i class="fas fa-lock"></i> Tutup Paket
                     </button>
                  </div>
                  <div class="col-8 col-lg-8 mt-3 px-0 text-right">
                     <span class="showPosition" style="color: #8c95bb !important;text-transform: uppercase;font-weight: bold;">Paket Umrah Mekah Madinah</span>
                  </div>
                  <div class="col-12 col-lg- my-3 px-0">
                     <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                        <tbody>
                           ${titleListK_T('RINCIAN KEGIATAN ANGGARAN PAKET', kurs +' 0', 'total_anggaran', 'total_anggaran')}
                           ${titleListK_T('RINCIAN AKTUALISASI BELANJA PAKET', kurs +' 0', 'aktualisasi_anggaran')}
                           ${titleListK_T('RINCIAN KEUNTUNGAN PROGRAM PAKET', kurs +' 0', 'keuntungan')}
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:3%;">No</th>
                              <th style="width:30%;">Uraian</th>
                              <th style="width:3%;">Qt</th>
                              <th style="width:12%;">Biaya</th>
                              <th style="width:12%;">Tot. Biaya Mahram</th>
                              <th style="width:12%;">Tot. Diskon</th>
                              <th style="width:13%;">Total Biaya</th>
                              <th style="width:15%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="potensi_pendapatan">
                           <tr><td colspan="8">Data potensi pendapatan tidak ditemukan</td></tr>
                        </tbody>
                      </table>
                  </div>

                  <div class="col-lg-12 px-0 mt-4">
                     <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                        <tbody id="bodyTable_daftar_aktualisasi">
                        </tbody>
                      </table>
                  </div>`;
   $('#contentDaftarPaket').html(html);
   // get data k t
   get_data_k_t(paket_id);
}


function get_data_k_t(paket_id){


   ajax_x_t2(
      baseUrl + "Trans_paket/get_data_k_t", function(e) {

         if( e['status_paket'] == 'tutup' ) {
            $('#buku_tutup_paket').replaceWith(`<button class="btn btn-default buka_tutup_paket" id="buku_tutup_paket" type="button" onclick="open_paket(${paket_id})">
               <i class="fas fa-lock-open"></i> Buka Paket
            </button>`);
            $('.tablebuka').addClass('tabletutup').removeClass('tablebuka');
         }else{
            $('#buku_tutup_paket').replaceWith(`<button class="btn btn-default buka_tutup_paket" id="buku_tutup_paket" type="button" onclick="close_paket(${paket_id})">
               <i class="fas fa-lock"></i> Tutup Paket
            </button>`);
         }

         $('.total_anggaran').html(kurs + ' ' + numberFormat(e['data']['total_paket_price']));

         var potensi_pendapatan =  `<tr>
                                       <td></td>
                                       <td colspan="5" class="text-left"><b>POTENSI PENDAPATAN PAKET</b></td>
                                       <td class="text-right" ><b>${kurs} ${numberFormat(e['data']['total_paket_price'])}</b></td>
                                       <td></td>
                                    </tr>`;
         var i = 1;
         for( x in e['data']['total_harga_per_tipe_paket']){
            potensi_pendapatan += `<tr>
                                    <td>${i}</td>
                                    <td class="text-left">${e['data']['total_harga_per_tipe_paket'][x]['paket_type_name']}</td>
                                    <td >${e['data']['total_harga_per_tipe_paket'][x]['jumlah_jamaah']}</td>
                                    <td class="text-right">${kurs} ${numberFormat(e['data']['total_harga_per_tipe_paket'][x]['harga_paket'])}</td>
                                    <td class="text-right">${kurs} ${numberFormat(e['data']['total_harga_per_tipe_paket'][x]['total_mahram_fee'])}</td>
                                    <td class="text-right">${kurs} ${numberFormat(e['data']['total_harga_per_tipe_paket'][x]['total_diskon'])}</td>
                                    <td class="text-right">${kurs} ${numberFormat(e['data']['total_harga_per_tipe_paket'][x]['total_harga_paket_type'])}</td>
                                    <td></td>
                                 </tr>`;
            i++;
         }
         $('#potensi_pendapatan').html(potensi_pendapatan);

         var aktualisasi = ``;
            aktualisasi += headAktualisasi('<b>A.</b>', '<b>KEBERANGKATAN</b>', `<b>${kurs} ${numberFormat(e['data']['total_paket_price'])}</b>`);
            aktualisasi += Aktualisasi('1', 'Pembayaran Jamaah', `${kurs} ${numberFormat(e['data']['total_sudah_dibayar'])}`);
            aktualisasi += Aktualisasi('2', 'Diskon', `${kurs} ${numberFormat(e['data']['total_diskon'])}`);
            aktualisasi += Aktualisasi('3', 'Piutang Jamaah', `${kurs} ${numberFormat(e['data']['total_piutang'])}`);
            aktualisasi += headAktualisasiBtn(paket_id, '<b>B.</b>', '<b>AKTUALISASI KEGIATAN ANGGARAN</b>', `<b>Rp${numberFormat(e['data']['total_aktualisasi'])}</b>`, e['status_paket']);
            aktualisasi += Aktualisasi('1', 'Fee Agen', `${kurs} ${numberFormat(e['data']['total_fee_agen'])}`);
            for( x in e['data']['aktualisasi']){
               aktualisasi += AktualisasiBtn( e['data']['aktualisasi'][x]['id'],
                                              e['data']['aktualisasi'][x]['number'],
                                              e['data']['aktualisasi'][x]['uraian'],
                                              e['data']['aktualisasi'][x]['total'] != '' ?
                                              `${kurs} ${numberFormat(e['data']['aktualisasi'][x]['total'])}` : '', e['status_paket'] );
               
               if( e['data']['aktualisasi'][x]['detail_aktualisasi'] != undefined ) {

                  var detail_aktualisasi = e['data']['aktualisasi'][x]['detail_aktualisasi'];
                  for ( y in detail_aktualisasi ){

                     //console.log(numberFormat(detail_aktualisasi[y]['subtotal']));

                     aktualisasi += detailAktualisasiBtn(detail_aktualisasi[y]['id'],
                                                         detail_aktualisasi[y]['uraian'],
                                                         detail_aktualisasi[y]['unit'],
                                                         `${kurs} ${numberFormat(detail_aktualisasi[y]['biaya'])}`,
                                                         `${kurs} ${numberFormat(detail_aktualisasi[y]['biaya'] * detail_aktualisasi[y]['unit'])}`,
                                                         e['status_paket']);
                  }
               }
            }
         $('#bodyTable_daftar_aktualisasi').html(aktualisasi);
         $('#aktualisasi_anggaran').html(kurs + ' ' + numberFormat(e['data']['total_aktualisasi']));
         $('#keuntungan').html( kurs + ' ' + numberFormat(e['data']['keuntungan']));
      },[{paket_id:paket_id}]
   );
}


function agen_paket(paket_id){
   var html   =  `<div class="col-8 col-lg-8 my-3 px-0">
                     <label class="float-right py-2 my-0 mx-2">Filter :</label>
                  </div>
                  <div class="col-4 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_agen_paket(${paket_id}, 20)" id="searchDaftarAgenPaket" name="searchDaftarAgenPaket" placeholder="Nama / No Identitas Agen" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_agen_paket(${paket_id}, 20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:31%;">Info Agen</th>
                              <th style="width:30%;">Info Jamaah</th>
                              <th style="width:12%;">Fee</th>
                              <th style="width:12%;">Sudah Bayar</th>
                              <th style="width:5%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_agen_paket">
                           <tr>
                              <td colspan="5">Daftar Agen tidak ditemukan.</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 mb-4" >
                     <div class="row" id="pagination_daftar_agen_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);

   get_daftar_agen_paket(paket_id, 20);
}

function get_daftar_agen_paket(paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_agen_paket',
               pagination_id: 'pagination_daftar_agen_paket',
               bodyTable_id: 'bodyTable_daftar_agen_paket',
               fn: 'ListDaftarAgenPaket',
               warning_text: '<td colspan="4">Daftar agen tidak ditemukan.</td>',
               param : { search : $('#searchDaftarAgenPaket').val(), paket_id:paket_id } } );
}

function ListDaftarAgenPaket(JSONData){
   var status_paket = $('#status_paket').val();
   var json = JSON.parse(JSONData);

   var info_agen =`<table class="table table-hover">
                     <tbody>
                        <tr>
                           <td class="text-left" style="width:40%;">NAMA AGEN</td>
                           <td class="px-0" style="width:1%;">:</td>
                           <td class="text-left" style="width:59%;">${json.nama_agen}</td>
                        </tr>
                        <tr>
                           <td class="text-left" >NO IDENTITAS</td>
                           <td class="px-0">:</td>
                           <td class="text-left" >${json.no_identitas_agen}</td>
                        </tr>
                        <tr>
                           <td class="text-left" >LEVEL</td>
                           <td class="px-0">:</td>
                           <td class="text-left" >${json.level_agen}</td>
                        </tr>
                        <tr>
                           <td class="text-left" >NOMOR WA</td>
                           <td class="px-0">:</td>
                           <td class="text-left" >${json.wa_agen}</td>
                        </tr>
                        <tr>
                           <td class="text-left" >ALAMAT AGEN</td>
                           <td class="px-0">:</td>
                           <td class="text-left" >${json.alamat_agen}</td>
                        </tr>
                     </tbody>
                  </table>`;

   var info_jamaah =`<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;">NAMA JAMAAH</td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;">${json.nama_jamaah}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >NO IDENTITAS</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.no_identitas_jamaah}</td>
                           </tr>
                        </tbody>
                     </table>`;


   var form = `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
                  <td>${info_agen}</td>
                  <td>${info_jamaah}</td>
                  <td>${kurs} ${numberFormat(json.fee)}</td>
                  <td>${kurs} ${numberFormat(json.sudah_bayar)}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Bayar Fee Keagenan" onclick="development_alert()">
                        <i class="fas fa-money-bill-alt" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return form;
}

function edit_bus_paket(paket_id, bus_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/get_info_bus_paket", function(e) {
         $.confirm({
            title: 'Form Edit Bus',
            theme: 'material',
            columnClass: 'col-8',
            content: formAddUpdateBus(paket_id, JSON.stringify(e), JSON.stringify(e['data'])),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        navBtnParam(this, 'bus_paket',  paket_id, 'Bus Paket', 'true');
                     });
                  }
               }
            }
         });
      },[{paket_id:paket_id, id:bus_id}]
   );
}

function delete_bus_paket(paket_id, id){
   ajax_x(
      baseUrl + "Trans_paket/delete_bus_paket", function(e) {
         navBtnParam(this, 'bus_paket',  paket_id, 'Bus Paket', 'true');
      },[{paket_id:paket_id, id:id}]
   );
}

function delete_jamaah_bus_paket(e){
   if($('.jamaah').length > 1 ){
      $(e).parent().parent().remove();
   }else{
      frown_alert('Anda tidak dapat menghapus semua jamaah!!!.');
   }
}

function add_jamaah_bus_paket(){
   var jsonDataJamaah = JSON.parse($('#daftarJamaahJSON').val());

   var jamaah = new Array();
   if( $('.jamaah').length > 0 ){
       $('.jamaah').each(function(index){
          if( $(this).val() != 0 ){
             jamaah.push($(this).val());
          }
       });
   }

   var html = '';
      html += `<div class="col-12">
                  <div class="row" >
                     <div class="col-sm-11 pt-0 pb-2">
                        <select class="form-control form-control-sm jamaah" name="jamaah[]">`;
               for( x in jsonDataJamaah ){
                  if( !jamaah.includes(x) ){
                     html +=  `<option value="${x}">${jsonDataJamaah[x]}</option>`;
                  }
               }
            html +=       `</select>
                     </div>
                     <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                        <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_bus_paket(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>
               </div>`;
   $('#listJamaah').append(html);
}


function formAddUpdateBus(paket_id, JSONData, JSONValue){
   var data = JSON.parse(JSONData);

   var val_bus_number= '';
   var val_bus_capacity = '';
   var val_bus_leader = '';
   var val_city_id = '';

   var listJamaah = '';
   var id_area = '';

   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id_area = `<input type="hidden" name="id" id="id" value="${value.id}">`;

      val_bus_number= value.bus_number;
      val_bus_capacity = value.bus_capacity;
      val_bus_leader = value.bus_leader;
      val_city_id = value.city_id;

      var jamaah = value.jamaah;

      if( Object.keys(jamaah).length > 0  ) {
         listJamaah = ``;
         for( y in jamaah ) {
            listJamaah += `<div class="col-12">
                              <div class="row">
                                 <div class="col-sm-11 pt-0 pb-2">
                                    <select class="form-control form-control-sm jamaah" name="jamaah[]" >
                                       <option value="0">Pilih Jamaah</option>`;
                  for( x in data['jamaah'] ) {
                     listJamaah += `<option value="${x}" ${ jamaah[y] == x ? 'selected' : '' }>${data['jamaah'][x]}</option>`;
                  }
                  listJamaah +=    `</select>
                                 </div>
                                 <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                                    <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_bus_paket(this)">
                                       <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>`;
         }
      }else{
         listJamaah += `<div class="col-12">
                           <div class="row">
                              <div class="col-sm-11 pt-0 pb-2">
                                 <select class="form-control form-control-sm jamaah" name="jamaah[]" >
                                    <option value="0">Pilih Jamaah</option>`;
                        for( x in data['jamaah'] ) {
                           listJamaah += `<option value="${x}">${data['jamaah'][x]}</option>`;
                        }
               listJamaah +=    `</select>
                              </div>
                              <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                                 <button type="button" class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_bus_paket(this)">
                                    <i class="fas fa-times" style="font-size: 11px;"></i>
                                 </button>
                              </div>
                           </div>
                        </div>`;
      }
   }else{
      listJamaah += `<div class="col-12">
                        <div class="row">
                           <div class="col-sm-11 pt-0 pb-2">
                              <select class="form-control form-control-sm jamaah" name="jamaah[]" >
                                 <option value="0">Pilih Jamaah</option>`;
                     for( x in data['jamaah'] ) {
                        listJamaah += `<option value="${x}">${data['jamaah'][x]}</option>`;
                     }
            listJamaah +=    `</select>
                           </div>
                           <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                              <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_bus_paket(this)">
                                 <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </div>
                     </div>`;
   }

   var form = `<form action="${baseUrl }Trans_paket/add_update_bus_paket" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_id" value="${paket_id}">
                  ${id_area}
                  <div class="row px-0 mx-0">
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Nomor Bus</label>
                           <input type="text" required="" name="nomor_bus" placeholder="Nomor Bus" class="form-control form-control-sm" id="nomor_bus" value="${val_bus_number}">
                        </div>
                     </div>
                     <div class="col-2" >
                        <div class="form-group">
                           <label>Kapasitas Bus</label>
                           <input type="number" required="" name="kapasitas_bus" placeholder="Kapasitas Bus" class="form-control form-control-sm" id="kapasitas_bus" value="${val_bus_capacity}">
                        </div>
                     </div>
                     <div class="col-6" >
                        <div class="form-group">
                           <label>Bus Leader</label>
                           <input type="text" name="bus_leader" placeholder="Bus Leader" class="form-control form-control-sm" id="bus_leader" value="${val_bus_leader}" required>
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Nama Kota</label>
                           <select class="form-control form-control-sm" name="kota_singgah" >
                              <option value="0">Pilih Kota Singgah</option>`;
                  for( c in data['city'] ) {
                     form += `<option value="${c}" ${ c == val_city_id ? 'selected' : '' }>${data['city'][c]}</option>`;
                  }
               form +=    `</select>
                        </div>
                     </div>
                     <div class="col-8">
                        <input type="hidden" id="daftarJamaahJSON" value='${JSON.stringify(data['jamaah'])}'>
                        <div class="form-group form-group-input">
                           <label>Daftar Jamaah</label>
                           <div id="listJamaah">
                              ${listJamaah}
                           </div>
                           <div>
                              <div class="col-sm-12 pt-2 pr-0">
                                 <button type="button" class="btn btn-default btn-action" title="Delete" onclick="add_jamaah_bus_paket()" style="width:100%;">
                                    <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Jamaah
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $("#nama_hotel").select2({
                     dropdownParent: $(".jconfirm")
                  });
               </script>`;
   return form;
}


function add_bus(paket_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/info_add_bus", function(e) {
         $.confirm({
            title: 'Form Tambah Bus',
            theme: 'material',
            columnClass: 'col-8',
            content: formAddUpdateBus(paket_id, JSON.stringify(e)),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        navBtnParam(this, 'bus_paket',  paket_id, 'Bus Paket', 'true');
                     });
                  }
               }
            }
         });
      },[{paket_id:paket_id}]
   );
}


function bus_paket(paket_id){
   var status_paket = $('#status_paket').val();
   var html   =  `<div class="col-8 col-lg-8 my-3 px-0">`;

         if( status_paket == 'buka' ){
            html +=    `<button class="btn btn-default" type="button" onclick="add_bus(${paket_id})">
                           <i class="fas fa-bus-alt"></i> Tambah Bus
                        </button>`;
         }

            html += `<label class="float-right py-2 my-0 mx-2">Filter :</label>
                  </div>
                  <div class="col-4 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_bus_paket(${paket_id}, 20)" id="searchDaftarBusPaket" name="searchDaftarBusPaket" placeholder="Nomor Bus" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_bus_paket(${paket_id}, 20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:10%;">Nomor Bus</th>
                              <th style="width:10%;">Kapasitas Bus</th>
                              <th style="width:20%;">Pemimpin Bus</th>
                              <th style="width:35%;">Jamaah</th>
                              <th style="width:15%;">Nama Kota</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_bus_paket">
                           <tr>
                              <td colspan="6">Daftar bus tidak ditemukan.</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 mb-4" >
                     <div class="row" id="pagination_daftar_bus_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);

   get_daftar_bus_paket(paket_id, 20);
}

function get_daftar_bus_paket(paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_bus_paket',
               pagination_id: 'pagination_daftar_bus_paket',
               bodyTable_id: 'bodyTable_daftar_bus_paket',
               fn: 'ListDaftarBusPaket',
               warning_text: '<td colspan="6">Daftar bus tidak ditemukan.</td>',
               param : { search : $('#searchDaftarBusPaket').val(), paket_id:paket_id } } );
}

function ListDaftarBusPaket(JSONData){
   var json = JSON.parse(JSONData);
   var jamaah =  ``;
   var status_paket = $('#status_paket').val();
   if( json["jamaah"] != undefined ){
      jamaah +=  `<table class="table table-hover my-1">
                        <tbody>`;
      for( x in json["jamaah"] ) {
         jamaah +=  `<tr>
                        <td class="text-left py-0 " rowspan="2" style="vertical-align: middle;">
                           <i class="fas fa-user"></i> ${json["jamaah"][x]['name']}
                        </td>
                        <td class="text-left py-0 ">
                           <i>No Identity : ${json["jamaah"][x]['identity_number']}</i>
                        </td>
                     </tr>
                     <tr>
                        <td class="text-left py-0">
                           <i>Tipe Paket : Normal</i>
                        </td>
                     </tr>`;
      }
      jamaah +=  `</tbody>
               </table>`;
   }else{
      jamaah += `Data jamaah bus tidak ditemukan`;
   }

   var html =  `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
                  <td><b>${json["bus_number"]}</b></td>
                  <td>${json["bus_capacity"]} Orang</td>
                  <td>${json["bus_leader"]}</td>
                  <td>${jamaah}</td>
                  <td>${json["city_name"]}</td>
                  <td>`;
         if( status_paket == 'buka' ) {
            html += `<button type="button" class="btn btn-default btn-action" title="Edit Bus"
                        onClick="edit_bus_paket('${json["paket_id"]}', '${json["id"]}')">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Bus"
                        onClick="delete_bus_paket('${json["paket_id"]}', '${json["id"]}')">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>`;
         }else{
            html += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
         }
         html += `</td>
               </tr>`;
   return html;
}

function cetak_daftar_kamar_jamaah(paket_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/cetak_daftar_kamar_jamaah", function(e) {
         if ( e['error'] == true ) {
            return false;
         } else {
             window.open(baseUrl + "Download/", "_blank");
         }
      },[{paket_id:paket_id}]
   );
}

function edit_kamar_paket(paket_id, id){
   ajax_x_t2(
      baseUrl + "Trans_paket/get_info_kamar_paket", function(e) {
         $.confirm({
            title: 'Form Edit Kamar',
            theme: 'material',
            columnClass: 'col-8',
            content: formAddUpdateRoom(paket_id, JSON.stringify(e), JSON.stringify(e['data'])),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        navBtnParam(this, 'kamar_paket', paket_id, 'Kamar Paket', 'true');
                     });
                  }
               }
            }
         });
      },[{paket_id:paket_id, id:id}]
   );
}

function add_jamaah_room_paket(){
   var jsonDataJamaah = JSON.parse($('#daftarJamaahJSON').val());

   var jamaah = new Array();
   if( $('.jamaah').length > 0 ){
       $('.jamaah').each(function(index){
          if( $(this).val() != 0 ){
             jamaah.push($(this).val());
          }
       });
   }
   var html = '';
      html += `<div class="col-12">
                  <div class="row" >
                     <div class="col-sm-11 pt-0 pb-2">
                        <select class="form-control form-control-sm jamaah" name="jamaah[]">`;
               for( x in jsonDataJamaah ){
                  if( !jamaah.includes(x) ){
                     html +=  `<option value="${x}">${jsonDataJamaah[x]}</option>`;
                  }
             
               }
            html +=       `</select>
                     </div>
                     <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                        <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_room_paket(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>
               </div>`;
   $('#listJamaah').append(html);
}

// form add update room
function formAddUpdateRoom(paket_id, JSONData, JSONValue){
   var e = JSON.parse(JSONData);
   var nama_penginapan = '';
   var type_kamar = '';
   var kapasitas_kamar = '';
   var kota_singgah = '';
   var id = '';
   var listJamaah = `<div class="col-12">
                        <div class="row">
                           <div class="col-sm-11 pt-0 pb-2">
                              <select class="form-control form-control-sm jamaah" name="jamaah[]" >
                                 <option value="0">Pilih Jamaah</option>`;
                     for( x in e['jamaah']) {
                        listJamaah += `<option value="${x}">${e['jamaah'][x]}</option>`;
                     }
            listJamaah +=    `</select>
                           </div>
                           <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                              <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_room_paket(this)">
                                 <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </div>
                     </div>`;

   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" id="id" value="${value.id}">`;
      nama_penginapan = value.hotel_id;
      type_kamar = value.room_type;
      kapasitas_kamar = value.room_capacity;
      var jamaah = value.jamaah;
      if( jamaah.length > 0 ) {
         listJamaah = ``;
         for( y in jamaah) {
            listJamaah += `<div class="col-12">
                              <div class="row">
                                 <div class="col-sm-11 pt-0 pb-2">
                                    <select class="form-control form-control-sm jamaah" name="jamaah[]" >
                                       <option value="0">Pilih Jamaah</option>`;
                  for( x in e['jamaah'] ) {
                     listJamaah += `<option value="${x}" ${ jamaah[y] == x ? 'selected' : '' }>${e['jamaah'][x]}</option>`;
                  }
                  listJamaah +=    `</select>
                                 </div>
                                 <div class="col-sm-1 pt-0 pb-2 px-0 text-right">
                                    <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_room_paket(this)">
                                       <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>`;
         }
      }
   }

   var form = `<form action="${baseUrl }Trans_paket/add_update_kamar_paket" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_id" value="${paket_id}">
                  ${id}
                  <div class="row px-0 mx-0">
                     <div class="col-9 pl-0" >
                        <div class="form-group">
                           <label>Nama Hotel</label>
                           <select id="nama_hotel" name="nama_hotel" class="js-example-basic-single" type="text" style="width:90%">`;
                  for ( x in e['hotel']) {
                     form +=  `<option value="${e['hotel'][x]['id']}">${e['hotel'][x]['nama_hotel']}</option>`;
                  }
               form +=    `</select>
                        </div>
                     </div>
                     <div class="col-3 pr-0" >
                        <div class="form-group">
                           <label>Tipe Kamar</label>
                           <select id="type_kamar" name="type_kamar" class="form-control form-control-sm" >
                              <option value="laki_laki" ${type_kamar == 'laki_laki' ? 'selected' : '' }>Laki-laki</option>
                              <option value="perempuan" ${type_kamar == 'perempuan' ? 'selected' : '' }>Perempuan</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-2 pl-0" >
                        <div class="form-group">
                           <label>Kapasitas Kamar</label>
                           <input type="number" required="" name="kapasitas_kamar" placeholder="Kapasitas" class="form-control form-control-sm" id="kapasitas_kamar" value="${kapasitas_kamar}">
                        </div>
                     </div>
                     <div class="col-10 pr-0">
                        <input type="hidden" id="daftarJamaahJSON" value='${JSON.stringify(e['jamaah'])}'>
                        <div class="form-group form-group-input">
                           <label>Daftar Jamaah</label>
                           <div id="listJamaah">
                              ${listJamaah}
                           </div>
                           <div>
                              <div class="col-sm-12 pt-2 pr-0">
                                 <button type="button" class="btn btn-default btn-action" title="Delete" onclick="add_jamaah_room_paket()" style="width:100%;">
                                    <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Jamaah
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $("#nama_hotel").select2({
                     dropdownParent: $(".jconfirm")
                  });
               </script>`;
   return form;
}

function delete_jamaah_room_paket(e){
   if($('.jamaah').length > 1 ){
      $(e).parent().parent().remove();
   }else{
      frown_alert('Anda tidak dapat menghapus semua jamaah!!!.');
   }
}

function add_rooms(paket_id){
   ajax_x_t2(
      baseUrl + "Trans_paket/info_add_kamar", function(e) {
         $.confirm({
            title: 'Form Tambah Kamar',
            theme: 'material',
            columnClass: 'col-8',
            content: formAddUpdateRoom(paket_id, JSON.stringify(e)),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        navBtnParam(this, 'kamar_paket', paket_id, 'Kamar Paket', 'true');
                     });
                  }
               }
            }
         });
      },[{paket_id:paket_id}]
   );
}

function kamar_paket(paket_id){
   var status_paket = $('#status_paket').val();
   var html   =  `<div class="col-6 col-lg-6 my-3 px-0">`;
         if( status_paket == 'buka'){
            html += `<button class="btn btn-default" type="button" onclick="add_rooms(${paket_id})">
                        <i class="fas fa-bed"></i> Tambah Kamar
                     </button>`;
         }
         html +=    `<button class="btn btn-default mx-2" type="button" onclick="cetak_daftar_kamar_jamaah(${paket_id})">
                        <i class="fas fa-print"></i> Download Daftar Kamar
                     </button>
                  </div>
                  <div class="col-2 col-lg-2 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-4 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_kamar_paket(${paket_id}, 20)" id="searchDaftarKamarPaket" name="searchDaftarKamarPaket" placeholder="Nomor Kamar" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_kamar_paket(${paket_id}, 20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:15%;">Tipe Kamar</th>
                              <th style="width:15%;">Kapasitas Kamar</th>
                              <th style="width:40%;">Daftar Jamaah</th>
                              <th style="width:20%;">Nama Kota</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_kamar_paket">
                           <tr>
                              <td colspan="5">Daftar kamar paket tidak ditemukan.</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 mb-4" >
                     <div class="row" id="pagination_daftar_kamar_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);

   get_daftar_kamar_paket(paket_id, 20);
}

function get_daftar_kamar_paket(paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_kamar_paket',
               pagination_id: 'pagination_daftar_kamar_paket',
               bodyTable_id: 'bodyTable_daftar_kamar_paket',
               fn: 'ListDaftarKamarPaket',
               warning_text: '<td colspan="5">Daftar kamar paket tidak ditemukan.</td>',
               param : { search : $('#searchDaftarKamarPaket').val(), paket_id:paket_id } } );
}

function ListDaftarKamarPaket(JSONData){
   var status_paket = $('#status_paket').val();
   var json = JSON.parse(JSONData);
   var jamaah =  `<table class="table table-hover my-1">
                     <tbody>`;
         for( x in json["jamaah"] ) {
            jamaah +=  `<tr>
                           <td class="text-left py-0 "  rowspan="2" style="vertical-align: middle;width:50%;">
                              <i class="fas fa-user" style="margin-right:10px;"></i> ${json["jamaah"][x]['name']}
                           </td>
                           <td class="text-left py-2" style="border-bottom: 1px dashed #dee2e6;">
                              <i>No Identity : ${json["jamaah"][x]['identity_number']}</i>
                           </td>
                        </tr>
                        <tr>
                           <td class="text-left py-2" style="height:20px;">
                              <i>Tipe Paket : Normal</i>
                           </td>
                        </tr>`;
         }
         jamaah +=  `</tbody>
                  </table>`;
   html =  `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
               <td><b>${json["room_type"]} <br> (Hotel : ${json["hotel_name"]} )</b></td>
               <td>${json["room_capacity"]} Orang</td>
               <td>${jamaah}</td>
               <td>${json["city_name"]}</td>
               <td>`;
      if( status_paket == 'buka'){
         html += `<button type="button" class="btn btn-default btn-action" title="Edit Kamar"
                     onClick="edit_kamar_paket('${json["paket_id"]}', '${json["id"]}')">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Kamar"
                     onClick="delete_kamar_paket('${json["paket_id"]}', '${json["id"]}')">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>`;
      }else{
         html += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
      }
      html += `</td>
            </tr>`;

   return html;
}


function delete_kamar_paket(paket_id, id){
   ajax_x(
      baseUrl + "Trans_paket/delete_kamar_paket", function(e) {
         get_daftar_kamar_paket(paket_id, 20);
      },[{paket_id:paket_id, id:id}]
   );
}

function syarat_paket(paket_id){
   var html   =  `<div class="col-5 col-lg-6 my-3"></div>
                  <div class="col-3 col-lg-2 my-3">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-4 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_syarat_paket(${paket_id}, 20)" id="searchDaftarSyaratPaket" name="searchDaftarSyaratPaket" placeholder="Nomor Identitas/Nama Jamaah" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_syarat_paket(${paket_id}, 20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:20%;">Jamaah</th>
                              <th style="width:10%;">Jenis Kelamin</th>
                              <th style="width:70%;">Syarat-syarat</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_syarat_paket">
                           <tr>
                              <td colspan="3">Data syarat paket tidak ditemukan.</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 mb-4" >
                     <div class="row" id="pagination_daftar_syarat_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);

   get_daftar_syarat_paket(paket_id, 20);
}

function get_daftar_syarat_paket(paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_syarat_paket',
               pagination_id: 'pagination_daftar_syarat_paket',
               bodyTable_id: 'bodyTable_daftar_syarat_paket',
               fn: 'ListDaftarSyaratPaket',
               warning_text: '<td colspan="3">Data syarat paket tidak ditemukan</td>',
               param : { search : $('#searchDaftarSyaratPaket').val(), paket_id:paket_id } } );
}

function ListDaftarSyaratPaket(JSONData){
   var json = JSON.parse(JSONData);
   var status_paket = $('#status_paket').val();
   var syarat_prasyarat = '';
   for( x in json["syarat_prasyarat"] ){
      syarat_prasyarat += `<div class="col-3 text-left">${ json["syarat_prasyarat"][x] == true ? '<i class="fas fa-check" style="color:#72c0c6;width:15px;"></i>' : '<i class="fas fa-times" style="color:#ec8888;width:15px;"></i>'} ${x.replace(/_/g, " ")} </div>`;
   }
   return  `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
               <td>${json["fullname"]}<br>(${json["nomor_identitas"]})</td>
               <td>${json["gender"]}</td>
               <td><div class="row">${syarat_prasyarat}</div></td>
            </tr>`;
}

function downloadManifest(paket_id){
 ajax_x_t2(
      baseUrl + "Trans_paket/download_manifest",
      function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
      },
      [{ paket_id:paket_id}]
   );
}

function manifes_paket(paket_id){
    var html   =  `<div class="col-5 col-lg-6 my-3">
                 <button class="btn btn-default" type="button" onclick="downloadManifest(${paket_id})">
                    <i class="fas fa-print"></i> Download Manifest
                 </button>
              </div>
              <div class="col-3 col-lg-2 my-3">
                 <label class="float-right py-2 my-0">Filter :</label>
              </div>
                  <div class="col-4 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_manifes_paket(${paket_id}, 20)" id="searchDaftarManifesPaket" name="searchDaftarManifesPaket" placeholder="Nomor Identitas/Nama Jamaah" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_manifes_paket(${paket_id}, 20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:20%;">Jamaah</th>
                              <th style="width:10%;">Status</th>
                              <th style="width:17%;">Umur</th>
                              <th style="width:15%;">Nomor Whatsapp</th>
                              <th style="width:30%;">Daftar Item Yang Belum Lengkap</th>
                              <th style="width:8%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_manifes_paket">
                           <tr>
                              <td colspan="6">Data manisfes tidak ditemukan.</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 mb-4" >
                     <div class="row" id="pagination_daftar_manifes_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);
   get_daftar_manifes_paket(paket_id, 20);
}

function get_daftar_manifes_paket(paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_manifes_paket',
               pagination_id: 'pagination_daftar_manifes_paket',
               bodyTable_id: 'bodyTable_daftar_manifes_paket',
               fn: 'ListDaftarManifesPaket',
               warning_text: '<td colspan="6">Data manisfes tidak ditemukan</td>',
               param : { search : $('#searchDaftarManifesPaket').val(), paket_id:paket_id } } );
}


function ListDaftarManifesPaket(JSONData){
   var status_paket = $('#status_paket').val();
   var json = JSON.parse(JSONData);
   var html = `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
                  <td>${json["fullname"]}<br>(${json["nomor_identitas"]})</td>
                  <td>${json["status"]}</td>
                  <td>${json['tgl_lahir']}<br><b>(${json["umur"]} Tahun)</b></td>
                  <td>${json["nomor_whatsapp"] != '' ? json["nomor_whatsapp"] : '-' }</td>
                  <td>${json["item_uncomplate"]}</td>
                  <td>`;
         if( status_paket == 'buka'){
            html +=    `<button type="button" class="btn btn-default btn-action" title="Edit Jamaah"
                           onClick="edit_jamaah_trans_paket('${json["id"]}', 'manifes', '${json['paket_id']}')">
                            <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                        </button>`;
         }else{
            html += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
         }

         html += `</td>
               </tr>`;
   return html;
}

function downloadAbsensi(paket_id){
      ajax_x_t2(
         baseUrl + "Trans_paket/infoTandaTangan", function(e) {
            var form = `<form action="${baseUrl }Trans_paket/download_absensi" id="form_utama" class="formName ">
                           <input type="hidden" name="paket_id" value="${paket_id}">
                           <div class="row px-0 mx-0">
                              <div class="col-12" >
                                 <div class="form-group">
                                    <label>Tanda tangan petugas</label>
                                    <select class="form-control form-control-sm" name="tanda_tangan" id="tanda_tangan" >
                                       <option value="pilih_petugas">Pilih petugas yang tanda tangan</option>`;
                                 for(  x in e['tanda_tangan'] ){
                                    form += `<option value="${e['tanda_tangan'][x]['id']}">${e['tanda_tangan'][x]['fullname']} (${e['tanda_tangan'][x]['jabatan']})</option>`;
                                 }
                           form += `</select>
                                 </div>
                              </div>
                           </div>
                        </form>`;
            $.confirm({
               title: 'Form Tanda Tangan',
               theme: 'material',
               columnClass: 'col-4',
               content: form,
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  formSubmit: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {

                        var tanda_tangan =  $('#tanda_tangan :selected').val();
                        var error = 0;
                        var error_msg = '';

                        if( tanda_tangan == 'pilih_petugas' ) {
                           error = 1;
                           error_msg += 'Untuk Melanjutkan, Anda wajib memilih salah satu petugas yang menandatangan<br>'
                        }

                        if( error == 1 ) {
                           frown_alert(error_msg);
                           return false;
                        }else{
                           ajax_submit_t1("#form_utama", function(e) {
                              if ( e['error'] == true ) {
                                 return false;
                              } else {
                                 window.open(baseUrl + "Kwitansi/", "_blank");
                              }
                           });
                        }
                     }
                  }
               }
            });
         },[]
      );
}

function trans_paket_Pages(){
   return  `<div class="col-12 pt-0 pb-3">
               <div class="btn-group">
                  ${navigationButton('Daftar Paket', ' onclick="navBtn(this, \'trans_paket_getData\', \'Daftar Paket\')" ', 'fas fa-box-open', 'active' )}
                  ${navigationButton('Daftar Jamaah', ' onclick="navBtn(this, \'trans_paket_daftar_jamaah\', \'Daftar Jamaah\')" ', 'fas fa-users', '')}
                  ${navigationButton('Pembayaran Paket', ' onclick="navBtn(this, \'landing_daftarAllTransactionPaket\', \'Pembayaran Paket\')" ', 'fas fa-money-bill-wave', '')}
                  ${navigationButton('Pembayaran Paket Agen', ' onclick="navBtn(this, \'landing_daftarAllTransactionPaketAgen\', \'Pembayaran Paket Agen\')" ', 'fas fa-user-check', '')}
               </div>
            </div>
            <div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarPaket"></div>
            </div>`;
}

function landing_daftarAllTransactionPaketAgen(){

   var html = `<div class="col-6 col-lg-8">
                  <label class="float-right py-2 my-3">Filter :</label>
               </div>
               <div class="col-6 col-lg-4 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="get_all_daftar_transaksi_paket_agen( 20)" id="searchAllDaftarTransaksiPaketAgen" name="searchAllDaftarTransaksiPaketAgen" placeholder="Nomor Identitas Agen" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_all_daftar_transaksi_paket_agen( 20 )">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:37%;">Info Member</th>
                           <th style="width:16%;">Nomor HP</th>
                           <th style="width:42%;">Info Akun</th>
                           <th style="width:5%;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable_all_daftar_transaksi_paket_agen">
                        <tr>
                           <td colspan="4">Daftar transaksi paket agen tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3 mb-4" >
                  <div class="row" id="pagination_all_daftar_transaksi_paket_agen"></div>
               </div>`;
            $('#contentDaftarPaket').html(html);
   get_all_daftar_transaksi_paket_agen(20);
}

function get_all_daftar_transaksi_paket_agen(perpage){
   get_data( perpage,
             { url : 'Trans_paket/all_daftar_transaksi_paket_agen',
               pagination_id: 'pagination_all_daftar_transaksi_paket_agen',
               bodyTable_id: 'bodyTable_all_daftar_transaksi_paket_agen',
               fn: 'ListAllTransPaketAgen',
               warning_text: '<td colspan="7">Daftar transaksi paket agen tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarTransaksiPaketAgen').val() } } );
}

function ListAllTransPaketAgen(JSONData){
   var json = JSON.parse( JSONData );

   var info_member =`<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:40%;border:none;">NAMA</td>
                              <td class="text-left py-0 pt-1" style="width:60%;border:none;font-weight:bold;">${json.fullname}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:40%;border:none;">NOMOR IDENTITAS</td>
                              <td class="text-left py-0 pt-1" style="width:60%;border:none;font-weight:bold;">${json.identity_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:40%;border:none;">JENIS KELAMIN</td>
                              <td class="text-left py-0 pt-1" style="width:60%;border:none;font-weight:bold;">${json.gender == 0 ? 'Laki-laki' : 'Perempuan'}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:40%;border:none;">ALAMAT</td>
                              <td class="text-left py-0 pt-1" style="width:60%;border:none;font-weight:bold;">${json.address}</td>
                           </tr>
                        </tbody>
                     </table>`;

   var  info_akun = `<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;">LEVEL AGEN</td>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;font-weight:bold;text-transform:uppercase;">${json.level_agen}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;">FEE BELUM DIBAYAR</td>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;font-weight:bold;">${kurs} ${numberFormat(json.unpaid_fee)}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;">FEE YANG SUDAH DIBAYAR</td>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;font-weight:bold;">${kurs} ${numberFormat(json.paid_fee)}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;">JUMLAH TRANSAKSI</td>
                              <td class="text-left py-0 pt-1" style="width:50%;border:none;font-weight:bold;">${json.total_transaksi} Transaksi</td>
                           </tr>
                        </tbody>
                     </table>`;

   return  `<tr>
               <td>${info_member}</td>
               <td>${json.nomor_whatsapp}</td>
               <td>${info_akun}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Bayar Fee Keagenan"
                     onClick="bayar_fee_agen('${json["id"]}', 'jamaah')">
                      <i class="fas fa-money-bill-alt" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function formPembayaranFeeAgen(JSONData){
   var json = JSON.parse(JSONData);

   var html = `<form action="${baseUrl }Trans_paket/proses_pembayaran_fee_agen" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <input type="hidden" name="invoice" value="${json.invoice}" >
                                 <label>Invoice</label>
                                 <input type="text" value="${json.invoice}" class="form-control form-control-sm" placeholder="Invoice" readonly/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <input type="hidden" name="id" value="${json.id}" >
                                 <label>Nama Agen</label>
                                 <input type="text" value="${json.fullname}" class="form-control form-control-sm" placeholder="Nama Agen" readonly/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nomor Identitas Agen</label>
                                 <input type="text" value="${json.identity_number}" class="form-control form-control-sm" placeholder="Nomor Identitas Agen" readonly/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Fee Keagenan yang Belum Dibayar</label>
                                 <input type="text" id="fee_unpaid" value="${kurs} ${numberFormat(json.unpaid)}" class="form-control form-control-sm" placeholder="Fee agen yang belum dibayar" readonly/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nama Pemohon</label>
                                 <input type="text" id="applicant_name" name="applicant_name" class="form-control form-control-sm" placeholder="Nama Pemohon" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nomor Identitas Pemohon</label>
                                 <input type="text" id="applicant_identity" name="applicant_identity" class="form-control form-control-sm" placeholder="Nomor Identitas Pemohon" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Fee yang akan dibayar</label>
                                 <input type="text" id="payment" name="payment" class="form-control form-control-sm currency" placeholder="Fee agen yang akan dibayar" onkeyup="checkFee()" />
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
               </script>
               `;
   return html;
}


function checkFee(){
   var fee_unpaid = hide_currency($('#fee_unpaid').val());
   var payment = hide_currency($('#payment').val());
   if( fee_unpaid < payment ) {
      var leng = payment.toString().length;
      $('#payment').val(payment.toString().substring((leng-1),-2));
      frown_alert('Pembayaran tidak boleh lebih besar dari nilai Fee yang belum dibayarkan');
   }
}

function bayar_fee_agen(id){
   ajax_x(
      baseUrl + "Trans_paket/get_info_fee_keagenan", function(e) {

         if(e['error'] == false){
            $.confirm({
               columnClass: 'col-4',
               title: 'Form Pembayaran Fee Agen',
               theme: 'material',
               content: formPembayaranFeeAgen(JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  lanjutkan: {
                     text: 'Bayar',
                     btnClass: 'btn-green',
                     action: function () {
                        var error = 0;
                        var error_msg = '';
                        var fee_unpaid = hide_currency($('#fee_unpaid').val());
                        var payment = hide_currency($('#payment').val());
                        if( fee_unpaid < payment ) {
                           error = 1;
                           error_msg = 'Pembayaran tidak boleh lebih besar dari nilai Fee yang belum dibayarkan';
                        }
                        if( error == 0 ) {
                           ajax_submit_t1("#form_utama", function(e) {
                              get_all_daftar_transaksi_paket_agen(20);
                              e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                        	});
                        } else {
                           frown_alert(error_msg);
                        }
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

function landing_daftarAllTransactionPaket(){
   var html = `<div class="col-6 col-lg-8">
                  <label class="float-right py-2 my-3">Filter :</label>
               </div>
               <div class="col-6 col-lg-4 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="get_all_daftar_transaksi_paket( 20)" id="searchAllDaftarTransaksiPaket" name="searchAllDaftarTransaksiPaket" placeholder="Nomor Registrasi" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_all_daftar_transaksi_paket( 20 )">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:11%;">Nomor Register</th>
                           <th style="width:22%;">Paket</th>
                           <th style="width:12%;">Tipe Paket</th>
                           <th style="width:16%;">Jamaah / Visa</th>
                           <th style="width:12%;">Total Harga</th>
                           <th style="width:12%;">Status Pembayaran</th>
                           <th style="width:15%;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable_all_daftar_transaksi_paket">
                        <tr>
                           <td colspan="7">Daftar transaksi paket tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3 mb-4" >
                  <div class="row" id="pagination_all_daftar_transaksi_paket"></div>
               </div>`;
            $('#contentDaftarPaket').html(html);
   get_all_daftar_transaksi_paket(20);
}

function get_all_daftar_transaksi_paket(perpage){
   get_data( perpage,
             { url : 'Trans_paket/all_daftar_transaksi_paket',
               pagination_id: 'pagination_all_daftar_transaksi_paket',
               bodyTable_id: 'bodyTable_all_daftar_transaksi_paket',
               fn: 'ListAllTransPaket',
               warning_text: '<td colspan="7">Daftar transaksi paket tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarTransaksiPaket').val() } } );
}

function ListAllTransPaket(JSONData){
   var json = JSON.parse(JSONData);
   var status_paket = $('#status_paket').val();
   var html = `<tr>
                  <td>${json.nomor_register}</td>
                  <td>${json.paket_name}<br>( Tgl Keberangkatan : ${json.departure_date})</td>
                  <td>${json.paket_type_name}<br>( ${json.harga} )</td>
                  <td>${json.jamaah}<br>
                      No Visa : ${json.no_visa}<br>
                      Tanggal Berlaku Visa : ${json.tgl_berlaku_visa}<br>
                      Tanggal Akhir Visa : ${json.tgl_akhir_visa}<br>
                     </td>
                  <td>${json.total_paket_price}</td>
                  <td>
                     <ul class="pl-3 list">
                        <li><b>Diskon : </b><br>${json.diskon} </li>
                        <li><b>Biaya Mahram : </b><br>${json.fee_mahram} </li>
                        <li><b>Sudah Bayar : </b><br>${json.sudah_dibayar} </li>
                        <li><b>Sisa : </b><br>${json.sisa}</li>
                     </ul>
                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Refund Paket"
                        onClick="formRefundTransaksiPaket('${json.paket_id}','${json.id}', 'allpaket')">
                         <i class="fas fa-undo-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Pembayaran Paket"
                        onClick="formPembayaranCash('${json.paket_id}','${json.id}', 'allpaket')">
                         <i class="fas fa-money-bill-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Hapus Transaksi Paket"
                        onClick="deleteTransaksiPaket('${json.paket_id}', '${json.id}', 'allpaket')">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function trans_paket_daftar_jamaah(){
   var html   =  `<div class="col-6 col-lg-8 my-3">
                     <button class="btn btn-default" type="button" onclick="add_jamaah_trans_paket()">
                        <i class="fas fa-user-plus"></i> Tambah Jamaah
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_data_daftar_jamaah( 20 )"
                           id="searchTransaksiJamaahTransPaket" name="searchTransaksiJamaahTransPaket" placeholder="Nomor Identitas/Nama Jamaah"
                           style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_data_daftar_jamaah( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:12%;">Nomor Identitas</th>
                              <th style="width:23%;">Nama Jamaah</th>
                              <th style="width:15%;">Tempat/Tanggal Lahir</th>
                              <th style="width:15%;">Nomor Passport</th>
                              <th style="width:15%;">Jumlah Pembelian Paket</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_jamaah">
                           <tr>
                              <td colspan="6">Daftar jamaah tidak ditemukan</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3 mb-4" >
                     <div class="row" id="pagination_daftar_jamaah"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);
   // get data trans paket daftar jamaah
   get_data_daftar_jamaah(20);
}

function get_data_daftar_jamaah(perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_jamaah_trans_paket',
               pagination_id: 'pagination_daftar_jamaah',
               bodyTable_id: 'bodyTable_daftar_jamaah',
               fn: 'ListJamaahTransJamaah',
               warning_text: '<td colspan="6">Daftar jamaah tidak ditemukan</td>',
               param : { search : $('#searchTransaksiJamaahTransPaket').val() } } );
}

function ListJamaahTransJamaah(JSONData){
   var json = JSON.parse(JSONData);
   return  `<tr>
               <td>${json["nomor_identitas"]}</td>
               <td>${json["fullname"]}</td>
               <td>${json["tempat_lahir"]} / ${json["tanggal_lahir"]}</td>
               <td>${json["nomor_passport"]}</td>
               <td>${json["total_pembelian"]}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Edit Jamaah"
                     onClick="edit_jamaah_trans_paket('${json["id"]}', 'jamaah')">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Jamaah"
                     onClick="delete_jamaah('${json["id"]}')">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function add_jamaah_trans_paket(){
   ajax_x(
      baseUrl + "Trans_paket/info_jamaah", function(e) {
         html = form_add_update_trans_paket(JSON.stringify(e['data']),JSON.stringify([]), 'jamaah');
         $('#contentDaftarPaket').html(html);
      },[]
   );
}

function form_add_update_trans_paket(JSONData, JSONValue, state, paket_id){
   var value = JSON.parse(JSONValue);
   console.log(value)
   var e = JSON.parse(JSONData);
  
   var personal_id = '';
   var address = '';

   var alamat_keluarga = '';
   var birth_date = '';
   var birth_place = '';
   var blood_type = '';

   var departing_from = '';
   var desease = '';
   var email = '';
   var father_name = '';

   var fullname = '';
   var gender = '';
   var hajj_experience = '';
   var hajj_year = '';
   var identity_number = '';
   var jamaah_id = '';
   var keterangan = '';
   var last_education = '';
   var mahramStatus = '';
   var nama_keluarga = '';
   var nomor_whatsapp = '';
   var passport_dateissue = '';
   var passport_number = '';
   var passport_place = '';
   var photo = '';

   var pos_code = '';
   var profession_intance_address = '';
   var profession_intance_name = '';
   var profession_intance_telephone = '';
   var pekerjaan = '';
   var agen = '';
   var status_nikah = '';
   var tanggal_nikah = '';
   var telephone = '';
   var telephone_keluarga = '';
   var umrah_experience = '';
   var umrah_year = '';
   var validity_period = '';
   var nomor_whatsapp = '';

   var checkBoxKelengkapanVal = {};

   if( Object.keys(value).length > 0 ){
      address = value.address;
      alamat_keluarga = value.alamat_keluarga;
      birth_date = value.birth_date;
      birth_place = value.birth_place;
      blood_type = value.blood_type;
      departing_from = value.departing_from;
      desease = value.desease;
      email = value.email;
      father_name = value.father_name;
      fullname = value.fullname;
      gender = value.gender;
      hajj_experience = value.hajj_experience;
      hajj_year = value.hajj_year;
      identity_number = value.identity_number;
      jamaah_id = value.jamaah_id;
      keterangan = value.keterangan;
      last_education = value.last_education;
      mahramStatus = value.mahramStatus;
      nama_keluarga = value.nama_keluarga;
      nomor_whatsapp = value.nomor_whatsapp;
      passport_dateissue = value.passport_dateissue;
      passport_number = value.passport_number;
      passport_place = value.passport_place;
      photo = value.photo;
      pos_code = value.pos_code;
      profession_intance_address = value.profession_instantion_address;
      profession_intance_name = value.profession_instantion_name;
      profession_intance_telephone = value.profession_instantion_telephone;
      pekerjaan = value.pekerjaan;
      status_nikah = value.status_nikah;
      tanggal_nikah = value.tanggal_nikah;
      telephone = value.telephone;
      telephone_keluarga = value.telephone_keluarga;
      umrah_experience = value.umrah_experience;
      umrah_year = value.umrah_year;
      validity_period = value.validity_period;
      nomor_whatsapp = value.nomor_whatsapp;
      personal_id = value.personal_id;
      agen = value.agen_id;
      checkBoxKelengkapanVal = { akte_lahir: value.akte_lahir,
                                 photo_3_4: value.photo_3_4,
                                 photo_4_6: value.photo_4_6,
                                 fc_kk: value.fc_kk,
                                 fc_ktp:value.fc_ktp,
                                 fc_passport: value.fc_passport,
                                 buku_kuning: value.buku_kuning,
                                 buku_nikah: value.buku_nikah};
   }

   var nav = ``;
   if ( state == 'jamaah' ) {
      nav = `navBtn(this, 'trans_paket_daftar_jamaah', 'Daftar Jamaah', '')`;
   } else if ( state == 'manifes' ) {
      nav = `navBtnParam(this, 'manifes_paket',  ${paket_id},'Manifes Paket')`;
   }

   // navBtnParam(this, 'manifes_paket',  3,'Manifes Paket')

   var html   =  `<div class="col-lg-12 mt-0 pt-0" >
                     <form class="py-2" action="${baseUrl }Trans_paket/add_update_jamaah" id="form_utama" onsubmit="add_update_jamaah(event, '${paket_id}')">
                        <div class="row mb-3">
                           <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                              <div class="row">
                                 <div class="col-12">
                                    <span class="float-left mt-2 alern_add_jamaah" style="color:red;font-style:italic;font-size: 11px;" ></span>
                                    <button type="button" class="btn btn-default" onclick="${nav}">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row" id="formAreas">
                           <div class="col-12 col-lg-3">`;
                     if( jamaah_id != ''){
                        html += `<input type="hidden" name="jamaah_id" id="jamaah_id" value="${jamaah_id}">`;
                     }
                     if(personal_id != ''){
                        html += `<input type="hidden" name="personal_id" id="personal_id" value="${personal_id}">`;
                     }
                  html +=    `${inputTextForm('Nama Jamaah', 'nama_jamaah', fullname, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Nomor Identitas', 'no_identitas', identity_number, 'onKeyup="checkingPersonal(this)"', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Jenis Kelamin', 'jenis_kelamin', JSON.stringify(e['gender']), '', gender, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Golongan Darah', 'golongan_darah', JSON.stringify(e['golongan_darah']), '', blood_type, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Tempat Lahir', 'tempat_lahir', birth_place, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputDateForm('Tanggal Lahir', 'tanggal_lahir', birth_date, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Alamat', 'alamat', address, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-1">
                              ${inputTextForm('Kode Post', 'kode_pos', pos_code, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Telephone', 'telephone', telephone, '', '')}
                           </div>
                           <div class="col-12 col-lg-4">
                              ${inputTextForm('Email', 'email', email, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Nomor Passport', 'nomor_passport', passport_number, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Tempat Dikeluarkan', 'tempat_dikeluarkan', passport_place, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputDateForm('Tanggal Dikeluarkan Passport', 'tanggal_dikeluarkan', passport_dateissue, 'col-12 col-lg-3', '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputDateForm('Masa Berlaku', 'masa_berlaku', validity_period, 'col-12 col-lg-2', '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Nama Ayah Kandung', 'nama_ayah', father_name, '', '')}
                           </div>
                           ${formAddMahramPaket(JSON.stringify(e['jamaah']), JSON.stringify(e['status_mahram']), JSON.stringify(mahramStatus))}
                           <div class="col-12 col-lg-6">
                              <div class="row">
                                 <div class="col-12 col-lg-6">
                                    ${inputTextForm('Nama Keluarga', 'nama_keluarga', nama_keluarga, '', '')}
                                 </div>
                                 <div class="col-12 col-lg-6">
                                    ${inputTextForm('Telephone Keluarga', 'telephone_keluarga', telephone_keluarga, '', '')}
                                 </div>
                                 <div class="col-12 col-lg-12">
                                    ${inputTextForm('Alamat Keluarga', 'alamat_keluarga', alamat_keluarga, 'col-12 col-lg-12', '', '')}
                                 </div>
                              </div>
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Status Nikah', 'status_nikah', JSON.stringify(e['status_nikah']), '', status_nikah, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputDateForm('Tanggal Nikah', 'tanggal_nikah', tanggal_nikah, 'col-12 col-lg-2', '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Pengalaman Haji', 'pengalaman_haji', JSON.stringify(e['pengalaman_haji_umrah']), '', hajj_experience, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputYearForm('Tahun Haji', 'tahun_haji', hajj_year, 'col-12 col-lg-3', '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Pengalaman Umrah', 'pengalaman_umrah', JSON.stringify(e['pengalaman_haji_umrah']), '', umrah_experience, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputYearForm('Tahun Umrah', 'tahun_umrah', umrah_year, 'col-12 col-lg-3', '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Berangkat Dari', 'berangkat_dari', departing_from, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Pekerjaan', 'pekerjaan', JSON.stringify(e['pekerjaan']), '', pekerjaan, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-6">
                              ${inputTextForm('Alamat Instansi Pekerjaan', 'alamat_instansi', profession_intance_address, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Nama Instansi Pekerjaan', 'nama_instansi', profession_intance_name, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Telephone  Pekerjaan', 'telephone_instansi', profession_intance_telephone, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Pendidikan Terakhir', 'pendidikan_terakhir', JSON.stringify(e['pendidikan']), '', last_education, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Penyakit', 'penyakit', desease, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Agen', 'agen', JSON.stringify(e['list_agen']), '', agen, 'py-1')}
                           </div>
                           <div class="col-6" >
                              <div class="row" >
                                 <label for="staticEmail" class="col-sm-12 col-form-label pb-0">Ambil Photo Jamaah</label>
                                 <div class="col-6" id="my_camera"></div>
                                 <div class="col-6" id="results" style="padding-top: 1.65rem!important;height: 206px;">`;
                        if( photo != '' ) {
                           html +=    `<img src="${baseUrl + 'image/' + photo}" class="img-fluid" alt="Responsive image">`;
                        } else {
                           html +=    `<div class="py-5" style="width:100%;height:100%;background-color: #e6e6e6;" >
                                          <div class="mx-auto my-4 py-2 text-center" style="font-weight: 700;color: #909090;">HASIL PHOTO</div>
                                       </div>`;
                        }
                        html += `</div>
                                 <div class="col-6" >
                                    <button type="button" class="btn btn-primary" onclick="take_snapshot()" style="width:100%">Ambil Gambar</button>
                                 </div>
                                 <div class="col-6" >
                                    <button type="button" class="btn btn-danger" onclick="delete_snapshot()" style="width:100%">Hapus Gambar</button>
                                 </div>
                              </div>
                           </div>
                           <div class="col-6" >
                              <div class="row" >
                                 ${checkBoxKelengkapan('Kelengkapan','col-lg-6', checkBoxKelengkapanVal )}
                                 <div class="col-lg-6">
                                    ${textAreaFrom('Keterangan', 'keterangan',keterangan, 'Keterangan')}
                                 </div>
                                 <div class="col-12 col-lg-8">
                                    ${inputTextForm('Nomor Whatsapp', 'nomor_whatsapp', nomor_whatsapp, 'onKeyup="checkNomorWA()"', '')}
                                 </div>
                                 <div class="col-lg-4" id="warning">
                                 </div>
                                 <div class="col-6" >
                                    ${inputPasswordForm('Password Aplikasi', 'password', 'Password Aplikasi', '')}
                                 </div>
                                 <div class="col-6" >
                                    ${inputPasswordForm('Password Konfirmasi Aplikasi', 'confirm_password', 'Password Konfirmasi', '')}
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row mt-3">
                           <div class="col-12 col-12 p-2 text-right" style="background-color: #e9ecef;">
                              <div class="row">
                                 <div class="col-12">
                                    <span class="float-left mt-2 alern_add_jamaah" style="color:red;font-style:italic;font-size: 11px;"></span>
                                    <button type="button" class="btn btn-default" onclick="${nav}">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
                  <script>
                     Webcam.set({ width: 308, height: 233, image_format: 'jpeg', jpeg_quality: 90});
                     Webcam.attach( '#my_camera' );
                     $('#my_camera').children( "div" ).addClass( "doting" );
                  </script>`;
   return html;
}

function checkingPersonal(e){
   var nomor_identitas = $(e).val();
   if( nomor_identitas != '' ){
      ajax_x_t2(
         baseUrl + "Trans_paket/checkPersonalInfo", function(data) {
            // $('#personal_id').remove();
            if( data['error'] === true ) {
               console.log(data['error_msg']);
               $('.alern_add_jamaah').html(data['error_msg']);
            }
         },[{nomor_identitas: nomor_identitas }]
      );
   }
}

function formAddMahramPaket(JSONdataMahram, JSONdataStatusMahram, JSONvalue){
   var html = '';
   var data = JSON.parse(JSONdataMahram);
   var dataStatus = JSON.parse(JSONdataStatusMahram);
   html += `<div class="col-lg-6 px-2 " >
               <input type="hidden" name="daftarMahramJSON" id="daftarMahramJSON" value='${JSONdataMahram}'>
               <input type="hidden" name="daftarStatusMahramJSON" id="daftarStatusMahramJSON" value='${JSONdataStatusMahram}'>
               <div class="form-group form-group-input row">
                  <label for="exampleSelect1" class="col-sm-12 col-form-label">Daftar Mahram</label>
                  <div class="col-sm-12 px-2" id="listMahramJamaah">`;
      if( JSONvalue != undefined ) {
         var value = JSON.parse(JSONvalue);
         if( Object.keys(value).length > 0 ){
            for ( y in value ) {
               html += `<div class="row" >
                           <div class="col-sm-6 pt-0 pb-2">
                              <select class="form-control form-control-sm jamaah" name="mahram[]">`;
                     for( x in data ){
                        html +=  `<option value="${x}" ${ x == value[y]['jamaah_id'] ? 'selected' : '' }>${data[x]}</option>`;
                     }
                  html +=    `</select>
                           </div>
                           <div class="col-sm-5 pt-0 pb-2">
                              <select class="form-control form-control-sm jamaah" name="statusMahram[]" >`;
                     for( z in dataStatus ){
                        html +=  `<option value="${z}" ${ z == value[y]['status'] ? 'selected' : '' }>${dataStatus[z]}</option>`;
                     }
                  html +=    `</select>
                           </div>
                           <div class="col-sm-1 pt-0 pb-2 px-0 pr-2 text-right">
                              <button class="btn btn-default btn-action" title="Delete" onclick="deleteMahramJamaah(this)">
                                 <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </div>`;
            }
         }else{
            html += `<div class="row" >
                        <div class="col-sm-6 pt-0 pb-2">
                           <select class="form-control form-control-sm jamaah" name="mahram[]" >`;
                  for( x in data ) {
                     html +=  `<option value="${x}">${data[x]}</option>`;
                  }
               html +=   `</select>
                        </div>
                        <div class="col-sm-5 pt-0 pb-2">
                           <select class="form-control form-control-sm jamaah" name="statusMahram[]" >`;
                  for( y in dataStatus ) {
                     html +=  `<option value="${y}">${dataStatus[y]}</option>`;
                  }
               html +=   `</select>
                        </div>
                        <div class="col-sm-1 pt-0 pb-2 px-0 pr-2 text-right">
                           <button class="btn btn-default btn-action" title="Delete" onclick="deleteMahramJamaah(this)">
                              <i class="fas fa-times" style="font-size: 11px;"></i>
                           </button>
                        </div>
                     </div>`;
         }
      } else {
         html += `<div class="row" >
                     <div class="col-sm-6 pt-0 pb-2">
                        <select class="form-control form-control-sm jamaah" name="mahram[]" >`;
               for( x in data ) {
                  html +=  `<option value="${x}">${data[x]}</option>`;
               }
            html +=   `</select>
                     </div>
                     <div class="col-sm-5 pt-0 pb-2">
                        <select class="form-control form-control-sm jamaah" name="statusMahram[]" >`;
               for( y in dataStatus ) {
                  html +=  `<option value="${y}">${dataStatus[y]}</option>`;
               }
            html +=   `</select>
                     </div>
                     <div class="col-sm-1 pt-0 pb-2 px-0 pr-2 text-right">
                        <button class="btn btn-default btn-action" title="Delete" onclick="deleteMahramJamaah(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>`;
      }
   html +=    `</div>
               <div class="col-sm-12 px-2 pt-2" >
                  <button type="button" class="btn btn-default btn-action" title="Delete" onclick="addMahramJamaah()" style="width:100%;">
                     <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Mahram
                  </button>
               </div>
            </div>
         </div>`;
   return html;
}

function addMahramJamaah(){
   var jsonDataMahramJamaah = JSON.parse($('#daftarMahramJSON').val());
   var jsonDataStatusMahram = JSON.parse($('#daftarStatusMahramJSON').val());
   var html = `<div class="row" >
                  <div class="col-sm-6 pt-0 pb-2">
                     <select class="form-control form-control-sm jamaah" name="mahram[]" >`;
            for( x in jsonDataMahramJamaah ) {
               html +=  `<option value="${x}">${jsonDataMahramJamaah[x]}</option>`;
            }
      html +=       `</select>
                  </div>
                  <div class="col-sm-5 pt-0 pb-2">
                     <select class="form-control form-control-sm jamaah" name="statusMahram[]" >`;
            for( y in jsonDataStatusMahram ) {
               html +=  `<option value="${y}">${jsonDataStatusMahram[y]}</option>`;
            }
      html +=       `</select>
                  </div>
                  <div class="col-sm-1 pt-0 pb-2 px-0 pr-2 text-right">
                     <button class="btn btn-default btn-action" title="Delete" onclick="deleteMahramJamaah(this)">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </div>
               </div>`;
   $('#listMahramJamaah').append(html);
}

function deleteMahramJamaah(e){
   $(e).parent().parent().remove();
}

function take_snapshot() {
    // take snapshot and get image data
    Webcam.snap( function(data_uri) {
        // display results in page
        document.getElementById('results').innerHTML =
            '<img id="base64image" src="'+data_uri+'" style="vertical-align: middle;"/>';
    } );
}

function delete_snapshot(){
   if($("#personal_id").length > 0) {
      // console.log("ada personal id");
      ajax_x_t2(
         baseUrl + "Trans_paket/delete_photo", function(e) {

         },[{personal_id:$("#personal_id").val()}]
      );
   }
   
   document.getElementById('results').innerHTML = `<div class="py-5" style="width: 100%;height: 231px;background-color: #e6e6e6;" >
                                                      <div class="mx-auto my-5 py-2 text-center" style="font-weight: 700;color: #909090;">HASIL PHOTO</div>
                                                   </div>`;
                                                   //  `<div class="py-5" style="width:100%;height:100%;background-color: #e6e6e6;">
                                                   //    <div class="mx-auto my-4 py-2 text-center" style="font-weight: 700;color: #909090;">HASIL PHOTO</div>
                                                   // </div>`;
}

function checkBoxKelengkapan(label, className, valJSON){

   if( className == undefined || className == '' ){
      className = 'col-lg-3';
   }
   var photo_4_6 = '';
   var photo_3_4 = '';
   var fc_passport = '';
   var fc_kk = '';
   var fc_ktp = '';
   var buku_nikah = '';
   var akte_lahir = '';
   var buku_kuning = '';
   if( valJSON != undefined ){
      photo_4_6 = valJSON.photo_4_6 == 1 ? 'checked' : '';
      photo_3_4 = valJSON.photo_3_4 == 1 ? 'checked' : '';
      fc_passport = valJSON.fc_passport == 1 ? 'checked' : '';
      fc_kk = valJSON.fc_kk == 1 ? 'checked' : '';
      fc_ktp = valJSON.fc_ktp == 1 ? 'checked' : '';
      buku_nikah = valJSON.buku_nikah == 1 ? 'checked' : '';
      akte_lahir = valJSON.akte_lahir == 1 ? 'checked' : '';
      buku_kuning = valJSON.buku_kuning == 1 ? 'checked' : '';
   }

   return  `<div class="${className}">
               <fieldset class="form-group row">
                  <label for="staticEmail" class="col-sm-12 col-form-label">${label}</label>
                  <div class="col-lg-6">
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="photo_4_6" type="checkbox" value="1" ${photo_4_6}>
                           Pas Photo 4x6
                        </label>
                     </div>
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="photo_3_4" type="checkbox" value="1" ${photo_3_4}>
                           Pas Photo 3x4
                        </label>
                     </div>
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="fc_passport" type="checkbox" value="1" ${fc_passport}>
                           FC Passport
                        </label>
                     </div>
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="fc_kk" type="checkbox" value="1" ${fc_kk}>
                           FC KK
                        </label>
                     </div>
                  </div>
                  <div class="col-lg-6">
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="fc_ktp" type="checkbox" value="1" ${fc_ktp}>
                           FC KTP
                        </label>
                     </div>
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="buku_nikah" type="checkbox" value="1" ${buku_nikah}>
                           Buku Nikah Asli
                        </label>
                     </div>
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="akte_lahir" type="checkbox" value="1" ${akte_lahir}>
                           Akte Kelahiran
                        </label>
                     </div>
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="buku_kuning" type="checkbox" value="1" ${buku_kuning}>
                           Buku Kuning
                        </label>
                     </div>
                  </div>
               </fieldset>
            </div>`;
}


function trans_paket_getData(){
   ajax_x(
      baseUrl + "Trans_paket/Daftar_paket_transaksi", function(e) {
         var data = e['data'];
         var html = '';
         if( data != undefined ) {
             html += `<div class="col-12 owl-carousel owl-theme">`;
            for( x in data ) {
               html += cardItemPaket(JSON.stringify( data[x] ));
            }
            html += `</div>`;
         } else {
            html += `<div class=" pt-5 mt-5 mx-auto">${e['error_msg']}</div>`
         }
         html += `<script>
                     $(document).ready(function(){
                       $('.owl-carousel').owlCarousel({
                           loop:false,
                           margin:20,
                           nav:true,
                           navText: ["<img src='${baseUrl}image/previous.png'>","<img src='${baseUrl}image/next.png'>"],
                           responsive:{
                              0:{ items:2 },
                              600:{ items:4 },
                              1000:{ items:5 }
                           }
                        })
                     });
                  </script>`;
         $('#contentDaftarPaket').html(html);
      },[]
   );
}

// nomor whatsapp
function checkNomorWA(){
   if( $('#nomor_whatsapp').val() != ''){
      if( $('#personal_id').length > 0  ){
         ajax_x(
            baseUrl + "Trans_paket/checkNomorWA", function(e) {
               if( e['error'] == true ){
                  $('#warning').html(`<small id="emailHelp" class="form-text text-muted mt-4 pt-2" style="color:red !important;">${e['error_msg']}</small>`);
               }else{
                  $('#warning').html(`<small id="emailHelp" class="form-text text-muted mt-4 pt-2" style="color:green !important;">${e['error_msg']}</small>`);
               }
            },[{nomor_whatsapp:$('#nomor_whatsapp').val()}]
         );
      }else{
         ajax_x(
            baseUrl + "Trans_paket/checkNomorWA", function(e) {
               if( e['error'] == true ){
                  $('#warning').html(`<small id="emailHelp" class="form-text text-muted mt-4 pt-2" style="color:red !important;">${e['error_msg']}</small>`);
               }else{
                  $('#warning').html(`<small id="emailHelp" class="form-text text-muted mt-4 pt-2" style="color:green !important;">${e['error_msg']}</small>`);
               }
            },[{nomor_whatsapp:$('#nomor_whatsapp').val(), personal_id:$('#persona_id').val()}]
         );
      }
   }else{
      $('#warning').html(``);
   }
}

// delete jamaah
function delete_jamaah( id ){
   $.confirm({
      columnClass: 'col-7',
      title: 'Peringatan penghapus data jamaah',
      theme: 'material',
      type: 'green',
      content: `Jika anda menghapus jamaah, berarti anda juga akan:
               <ul class="mb-0">
                  <li><b>Menghapus</b> jamaah dari daftar paket yang terdaftar.</li>
                  <li><b>Menghapus</b> jamaah dari daftar mahram.</li>
                  <li><b>Menghapus</b> riwayat handover barang jamaah dari database.</li>
                  <li><b>Menghapus</b> riwayat handover fasilitas jamaah dari database.</li>
               </ul>
               Apakah anda ingin melanjutkan proses penghapusan?`,
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         ya: {
            text: 'Iya',
            btnClass: 'btn-red',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/delete_jamaah", function(e) {

                     e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);

                     if( e['error'] != true ){
                       navBtn(this, 'trans_paket_daftar_jamaah', 'Daftar Jamaah', '')
                     }
                  },[{id:id}]
               );
            }
         }
      }
   });
}

function add_update_jamaah(e, paket_id){
   console.log('PaketID');
   console.log(paket_id);
   console.log('PaketID');

   e.preventDefault();
   var nomor_whatsapp = $('#nomor_whatsapp').val();
   var password = $('#password').val();
   if( ('#id').length == 0 ){
      if( (nomor_whatsapp != '' && password == '') ||  (nomor_whatsapp == '' && password != '') ){
         if( username == '' ){
            var msg = 'Untuk menambahkan akun, nomor_whatsapp tidak boleh kosong';
         }else{
            var msg = 'Untuk menambahkan akun, password tidak boleh kosong';
         }

         frown_alert(msg);

      }else{
         ajax_submit_base64(e, "#form_utama", function(e) {
            if( e['error'] != true ){
               if( paket_id != 'undefined' ){
                  navBtnParam(this, 'manifes_paket',  paket_id, 'Manifes Paket');
               }else{
                  navBtn(this, 'trans_paket_daftar_jamaah', 'Daftar Jamaah', '');
               }
            }
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         });
      }
   }else{
      if( nomor_whatsapp == '' && password != '' ){
         frown_alert('Untuk menambahkan akun, nomor_whatsapp tidak boleh kosong');
      }else{
         ajax_submit_base64(e, "#form_utama", function(e) {
            if( e['error'] != true ){
               if( paket_id != 'undefined' ){
                  navBtnParam(this, 'manifes_paket',  paket_id, 'Manifes Paket');
               }else{
                  navBtn(this, 'trans_paket_daftar_jamaah', 'Daftar Jamaah', '');
               }
            }
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         });
      }
   }
}

function edit_jamaah_trans_paket(id, state, paket_id){
   ajax_x(
      baseUrl + "Trans_paket/edit_jamaah", function(e) {
         html = form_add_update_trans_paket(JSON.stringify(e['data']),JSON.stringify(e['value']), state, paket_id);
         $('#contentDaftarPaket').html(html);
      },[{jamaah_id:id}]
   );
}

function beli_paket(id, state){
   if ( state == 'daftar_paket') {
      fn = `menu( this, 'daftar_paket', 'Paket & Paket LA', 'fas fa-box-open', '', 'submodul')`;
   } else {
      fn = 'back_to_daftar_paket()';
   }
   ajax_x(
      baseUrl + "Trans_paket/get_paket_name", function(e) {
         var html = `<div class="col-9 col-lg-9 pt-0 pb-3">
                        <input type="hidden" id="status_paket" value="${e['status_paket']}">
                        <div class="btn-group">
                           <button type="button" class="btn btn-default btn-sm navbtn" onClick="${fn}">
                              <i class="fas fa-arrow-left" style="font-size: 11px;"></i>
                           </button>
                           ${navigationButton('Transaksi', ' onclick="navBtnParam(this, \'transaksi_paket\', '+id+', \'Transaksi Paket\')" ', 'fas fa-box-open', 'btn-same active' )}
                           ${navigationButton('Jamaah', ' onclick="navBtnParam(this, \'daftar_jamaah_paket\',  '+id+', \'Daftar Jamaah Paket\')" ', 'fas fa-users', 'btn-same' )}
                           ${navigationButton('Manifes', ' onclick="navBtnParam(this, \'manifes_paket\',  '+id+',\'Manifes Paket\')" ', 'far fa-clipboard', 'btn-same' )}
                           ${navigationButton('Syarat', ' onclick="navBtnParam(this, \'syarat_paket\', '+id+', \'Syarat Paket\')" ', 'fas fa-tasks', 'btn-same' )}
                           ${navigationButton('Kamar', ' onclick="navBtnParam(this, \'kamar_paket\',  '+id+',\'Kamar Paket\')" ', 'fas fa-bed', 'btn-same' )}
                           ${navigationButton('Bus', ' onclick="navBtnParam(this, \'bus_paket\',  '+id+',\'Bus Paket\')" ', 'fas fa-bus-alt', 'btn-same' )}
                           ${navigationButton('Agen', ' onclick="navBtnParam(this, \'agen_paket\',  '+id+',\'Daftar Agen Paket\')" ', 'fas fa-user-tag', 'btn-same' )}
                           ${navigationButton('K&T', ' onclick="navBtnParam(this, \'k_t\',  '+id+',\'Kas dan Transaksi\')" ', 'fas fa-chart-line', 'btn-same' )}
                        </div>
                     </div>
                     <div class="d-none d-sm-block col-3 col-lg-3 p-3 text-right"></div>
                     <div class="col-12 col-lg-12 px-3 pb-0 pt-0" ><div class="row" id="contentDaftarPaket"></div></div>`;
         if( state == 'daftar_paket') {
            $('#content_daftar_paket').html(html);
         } else {
            $('#content_trans_paket').html(html);
         }
         navBtnParam(this, 'transaksi_paket', id, 'Transaksi Paket', 'true');
      },[{paket_id: id}]
   );
}

function back_to_daftar_paket(){
   // get template
   $('#content_trans_paket').html( trans_paket_Pages() );
   // get dta
   trans_paket_getData();
}

function transaksi_paket(paket_id){
   var status_paket = $('#status_paket').val();
   var html   =  `<div class="col-6 col-lg-8 my-3 px-0">`;
      if( status_paket == 'buka') {
         html +=    `<button class="btn btn-default" type="button" onclick="start_transaction_paket(${paket_id})">
                        <i class="fas fa-money-bill-wave"></i> Memulai Transaksi
                     </button>`;
      }
      html +=       `<label class="float-right py-2 my-0 mx-2">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_transaksi_paket_by_id(${paket_id}, 20)" id="searchTransaksiPaket" name="searchTransaksiPaket" placeholder="Nomor Registrasi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_transaksi_paket_by_id( ${paket_id}, 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:11%;">Nomor Register</th>
                              <th style="width:22%;">Paket</th>
                              <th style="width:12%;">Tipe Paket</th>
                              <th style="width:16%;">Jamaah</th>
                              <th style="width:12%;">Total Harga</th>
                              <th style="width:12%;">Status Pembayaran</th>
                              <th style="width:15%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_transaksi_paket">
                           <tr>
                              <td colspan="7">Daftar transaksi paket tidak ditemukan</td>
                           </tr>
                        </tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3 mb-4" >
                     <div class="row" id="pagination_daftar_transaksi_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);
   get_daftar_transaksi_paket_by_id(paket_id, 20);
}

function get_daftar_transaksi_paket_by_id(paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_transaksi_paket_by_paket_id',
               pagination_id: 'pagination_daftar_transaksi_paket',
               bodyTable_id: 'bodyTable_daftar_transaksi_paket',
               fn: 'ListDaftarTransaksiPaket',
               warning_text: '<td colspan="7">Daftar transaksi paket tidak ditemukan</td>',
               param : { search : $('#searchTransaksiPaket').val(), paket_id:paket_id } } );
}

function ListDaftarTransaksiPaket(JSONData){
   var json = JSON.parse(JSONData);
   var status_paket = $('#status_paket').val();
   var html =  `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
                  <td>${json.nomor_register}</td>
                  <td>${json.paket_name}<br>( Tgl Keberangkatan : ${json.departure_date})</td>
                  <td>${json.paket_type_name}<br>( ${json.harga} )</td>
                  <td>${json.jamaah}
                     <ul class="pl-3 list">
                        <li><b>No Visa : </b> ${json.no_visa} </li>
                        <li><b>Tanggal Berlaku Visa : </b> ${json.tgl_berlaku_visa} </li>
                        <li><b>Tanggal Akhir Visa : </b> ${json.tgl_akhir_visa} </li>
                     </ul>
                  </td>
                  <td>${json.total_paket_price}</td>
                  <td>
                     <ul class="pl-3 list">
                        <li><b>Biaya Mahram : </b><br>${json.fee_mahram} </li>
                        <li><b>Sudah Bayar : </b><br>${json.sudah_dibayar} </li>
                        <li><b>Sisa : </b><br>${json.sisa}</li>
                     </ul>
                  </td>
                  <td>`;
               if( status_paket == 'buka'){
                  if( json.agen == true ) {
                     html += `<button type="button" class="btn btn-default btn-action" title="Update Fee Agen"
                                onClick="updateFeeAgen('${json.paket_id}','${json.id}', 'perpaket')">
                                 <i class="fas fa-user-tie" style="font-size: 11px;"></i>
                              </button>`;
                  }
                  html += `<button type="button" class="btn btn-default btn-action" title="Update Info Visa"
                             onClick="updateInfoVisa('${json.paket_id}','${json.id}', 'perpaket')">
                              <i class="fas fa-list-ol" style="font-size: 11px;"></i>
                           </button>
                           <button type="button" class="btn btn-default btn-action" title="Hapus Transaksi Paket"
                             onClick="deleteTransaksiPaket('${json.paket_id}', '${json.id}', 'perpaket')">
                              <i class="fas fa-times" style="font-size: 11px;"></i>
                           </button>`;
               } else {
                  html += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
               }
               html += `</td>
               </tr>`;
   return html;
}


function updateFeeAgen( paket_id, paket_transaction_id, state ){
   ajax_x(
      baseUrl + "Trans_paket/getInfoFeeAgen2", function(e) {
         if( e.error == false ) {
            $.confirm({
               columnClass: 'col-5',
               title: 'Update Fee Agen',
               theme: 'material',
               content:formUpdateFeeAgen(JSON.stringify(e['data']), paket_transaction_id),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  refund: {
                     text: 'Simpan Perubahan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_transaksi_paket_by_id(paket_id, 20);
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e.error_msg);
         }
      },[{paket_transaction_id: paket_transaction_id}]
   );
}


function formUpdateFeeAgen(JSONData, paket_transaction_id){
   var json = JSON.parse(JSONData);
   var form = `<form action="${baseUrl }Trans_paket/prosesUpdateFeeAgen" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_transaction_id" id="paket_transaction_id" value="${paket_transaction_id}">
                  <div class="row px-0 mx-0">
                     <div class="col-12">`;
            for (x in json) {
               form += `<div class="form-group">
                           <label>Fee ${json[x].level} (${json[x].nama_agen})</label>
                           <input type="text" name="fee_agen[${json[x].id}]" value="${kurs} ${numberFormat(json[x].fee)}" class="form-control form-control-sm currency" placeholder="Fee Agen"/>
                        </div>`;
            }
        form +=     `</div>
                  </div>
               </form>`;
   return form;            
}

function formInfoVisa(JSONValue, paket_transaction_id){
   var json = JSON.parse(JSONValue);
   var form = `<form action="${baseUrl }Trans_paket/prosesUpdateInfoVisa" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_transaction_id" id="paket_transaction_id" value="${paket_transaction_id}">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="form-group">
                           <label>Nomor Visa</label>
                           <input type="text" id="nomor_visa" name="nomor_visa" placeholder="Nomor Visa" class="form-control form-control-sm" value="${json.no_visa}"  />
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label>Tanggal Berlaku Visa</label>
                           <input type="date" id="tgl_berlaku_visa" name="tgl_berlaku_visa" placeholder="Tanggal Berlaku Visa" class="form-control form-control-sm" value="${json.tgl_berlaku_visa}"  />
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label>Tanggal Akhir Visa</label>
                           <input type="date" id="tgl_akhir_visa" name="tgl_akhir_visa" placeholder="Tanggal Akhir Visa" class="form-control form-control-sm" value="${json.tgl_akhir_visa}"  />
                        </div>
                     </div>
                  </div>
               </form>`;
   return form;            
}

function updateInfoVisa(paket_id, paket_transaction_id, state){
   ajax_x(
      baseUrl + "Trans_paket/updateInfoVisa", function(e) {
         $.confirm({
            columnClass: 'col-6',
            title: 'Edit Info Visa',
            theme: 'material',
            content:formInfoVisa(JSON.stringify(e['data']), paket_transaction_id),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               refund: {
                  text: 'Simpan Perubahan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           if( state == 'perpaket' ) {
                              navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket');
                              //window.open(baseUrl + "Kwitansi/", "_blank");
                           }else if ( state == 'allpaket' ) {
                              navBtn(this, 'landing_daftarAllTransactionPaket', 'Pembayaran Paket', '')
                              //window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        }
                     });
                  }
               }
            }
         });
      },[{paket_transaction_id: paket_transaction_id}]
   );
}

function daftar_jamaah_paket(paket_id){
   var html   =  `<div class="col-5 col-lg-6 my-3">
                     <button class="btn btn-default" type="button" onclick="downloadAbsensi(${paket_id})">
                        <i class="fas fa-print"></i> Download Absensi
                     </button>
                  </div>
                  <div class="col-3 col-lg-2 my-3">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-4 col-lg-4 my-3 px-0">
                     <div class="input-group">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_jamaah_paket_by_paket_id(${paket_id}, 20)" id="searchDaftarJamaahPaket" name="searchDaftarJamaahPaket" placeholder="Nomor Identitas/Nama Jamaah" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_jamaah_paket_by_paket_id(${paket_id}, 20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 px-0">
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:20%;">Jamaah</th>
                              <th style="width:15%;">Mahram</th>
                              <th style="width:20%;">Paket</th>
                              <th style="width:25%;">Info</th>
                              <th style="width:20%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_jamaah_paket"></tbody>
                      </table>
                  </div>
                  <div class="col-lg-12 px-3 mb-4" >
                     <div class="row" id="pagination_daftar_jamaah_paket"></div>
                  </div>`;
   $('#contentDaftarPaket').html(html);
   get_daftar_jamaah_paket_by_paket_id(paket_id, 20);
}

function get_daftar_jamaah_paket_by_paket_id( paket_id, perpage){
   get_data( perpage,
             { url : 'Trans_paket/get_daftar_jamaah_paket_by_paket_id',
               pagination_id: 'pagination_daftar_jamaah_paket',
               bodyTable_id: 'bodyTable_daftar_jamaah_paket',
               fn: 'ListDaftarJamaahPaket',
               warning_text: '<td colspan="5">Daftar jamaah tidak ditemukan</td>',
               param : { search : $('#searchDaftarJamaahPaket').val(), paket_id:paket_id } } );
}

function ListDaftarJamaahPaket(JSONData){
   var json = JSON.parse(JSONData);
   var status_paket = $('#status_paket').val();
   var html = `<tr ${ status_paket == 'tutup' ? 'class="tabletutup"': '' }>
                  <td>${json.fullname}<br>(No Identitas : ${json.identity_number})</td>
                  <td>${json.mahram}</td>
                  <td>
                     ${json.paket_name}<br>
                     (No Register: ${json.no_register})<br>
                     (Tipe Paket: ${json.paket_type_name})<br>
                     (Harga: ${json.harga})
                  </td>
                  <td class="text-left">
                     <label class="mb-0">Barang Jamaah Yang Diambil :</label>
                     <ul class="mt-0 mb-2">${json.handover_item}</ul>
                     <label class="mb-0">Fasilitas Jamaah Yang Sudah Diberikan :</label>
                     <ul class="mt-0 mb-2">${json.handover_facility}</ul>
                  </td>
                  <td>`;
         if( status_paket == 'buka' ) {
            html += `<button type="button" class="btn btn-default btn-action" title="Handover Barang"
                        onClick="handoverBarang( '${json.paket_id}', '${json.jamaah_id}','${json.paket_transaction_id}')">
                        <i class="fas fa-handshake" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Handover Fasilitas"
                        onClick="handoverFasilitas('${json.paket_id}','${json.jamaah_id}', '${json.paket_transaction_id}')">
                        <i class="far fa-handshake" style="font-size: 11px;"></i>
                     </button>
                     `;

                     
                     // <button type="button" class="btn btn-default btn-action" title="Pindah Paket"
                     //    onClick="pindahPaket('${json.paket_id}','${json.jamaah_id}', '${json.paket_transaction_id}')">
                     //    <i class="fas fa-exchange-alt" style="font-size: 11px;"></i>
                     // </button>
            // if( json.metode_pembayaran == 0 ) {
            //    html += `<button type="button" class="btn btn-default btn-action" title="Pindah Paket"
            //                onClick="pindahPaket('${json.paket_id}','${json.jamaah_id}', '${json.paket_transaction_id}')">
            //                <i class="fas fa-exchange-alt" style="font-size: 11px;"></i>
            //             </button> `;
            // } else {
            //    html += `<button type="button" disabled class="btn btn-default btn-action" title="Pindah Paket"
            //                onClick="$.alert({title: 'Peringatan',
            //                                  content: 'Untuk transaksi dengan metode pembayaran cicilan, tidak dapat dilakukan pindah paket',
            //                                  type: 'red'})">
            //                <i class="fas fa-exchange-alt" style="font-size: 11px;"></i>
            //             </button> `;
            // }
            html += `<button type="button" class="btn btn-default btn-action" title="Cetak Data Jamaah"
                        onClick="cetakDatajamaah('${json.jamaah_id}', '${json.paket_transaction_id}' , '${json.paket_id}')">
                        <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>`;
         }else{
            html += `<span style="color: #8c8c8c;">Paket ditutup</span>`;
         }
      html +=    `</td>
               </tr>`;
   return html;
}

// get fee
function get_fee(paket_id){
   if( $('#agen').val() != 0 ){
      ajax_x(
         baseUrl + "Trans_paket/get_info_fee", function(e) {
            if( e['error'] == false ) {
               var html = ``;
               for( x in e['data'] ) {
                  html += `<div class="col-12" >
                              <div class="form-group">
                                 <label>Fee ${ e['data'][x]['level_agen'] } : ${e['data'][x]['fullname'] }</label>
                                 <input type="hidden" name="agen_id[${e['data'][x]['id']}]" value="${e['data'][x]['id']}">
                                 <input type="hidden" name="level[${e['data'][x]['id']}]" value="${x}">
                                 <input type="text" required="" name="fee_agen[${e['data'][x]['id']}]" placeholder="Fee Agen"
                                    class="form-control form-control-sm currency" value="${kurs} ${numberFormat(e['data'][x]['fee'])}">
                              </div>
                           </div>`;
               }
               $('#fee_agen').html(html);
            }
         },[{paket_id: paket_id, agen_id:$('#agen').val()}]
      );
   }else{
      $('#fee_agen').html('');
   }
}

// start transaction paket
function start_transaction_paket( paket_id ){
   ajax_x(
      baseUrl + "Trans_paket/get_info_transaction", function(e) {
         var list = ['select1', 'select2'];
         var html   =  `<div class="col-lg-12 mt-0 pt-0" >
                           <form class="py-2" action="${baseUrl }Trans_paket/transaction_paket_process" id="form_utama_big" class="formName" onsubmit="transactionProcess(event)" >
                              <div class="row mb-3">
                                 <div class="col-12 col-lg-4" style="background-color: #e9ecef;"></div>
                                 <div class="col-12 col-lg-4 pt-3" style="background-color: #e9ecef;font-weight: 700;">
                                    <center><span>${e['data']['paket_name']}</span></center>
                                 </div>
                                 <div class="col-4 p-2 text-right" style="background-color: #e9ecef;">
                                    <div class="row">
                                       <div class="col-12">
                                          <button type="button" class="btn btn-default" onclick="navBtnParam(this, 'transaksi_paket', ${paket_id}, 'Transaksi Paket', true)">Batal</button>
                                          <button type="submit" class="btn btn-primary">Simpan</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-12 col-lg-1">
                                    ${hiddenForm('paket_id', paket_id )}
                                    ${hiddenForm('no_register', e['data']['no_register'] )}
                                    ${inputTextForm('Nomor Registrasi', 'no_registerView', e['data']['no_register'], 'readonly', '<span class="red">*</span>')}
                                 </div>
                                 <div class="col-12 col-lg-3">
                                    ${simpleSelectForm('Jenis Paket', 'jenis_paket', JSON.stringify(e['data']['paket_type']), ' onChange="get_price_transaksi_paket()"', '', 'py-1')}
                                 </div>
                                 <div class="col-12 col-lg-2">
                                    ${inputTextForm('Harga Paket Per Pax', 'harga_perpax', kurs + ' 0,-', 'readonly', '')}
                                 </div>
                                 <div class="col-12 col-lg-2">
                                    ${inputTextForm('Total Seluruh Paket', 'totalPaket', kurs + ' 0', 'readonly', '')}
                                 </div>
                                 <div class="col-12 col-lg-4">
                                    ${simpleSelectForm('Daftar Jamaah', 'jamaah', JSON.stringify(e['data']['jamaah']), ' onChange="get_agen()" ', '', 'py-1 mb-3 jamaah')}
                                 </div>
                                 <div class="col-12 col-lg-9 px-2 " >
                                    <div class="row">
                                        <div class="col-12 col-lg-3">
                                          ${inputTextForm('Biaya Mahram', 'biayaMahramView', kurs +  ' 0', 'readonly', '')}
                                          ${hiddenForm('biayaMahram', kurs + ' 0' )}
                                       </div>
                                       <div class="col-12 col-lg-3">
                                          ${inputTextForm('Nomor Visa', 'nomor_visa', '', '', '')}
                                       </div>
                                       <div class="col-12 col-lg-3">
                                          ${inputDateForm('Tanggal Berlaku Visa', 'tanggal_berlaku_visa', '', '', '')}
                                       </div>
                                       <div class="col-12 col-lg-3">
                                          ${inputDateForm('Tanggal Akhir Visa', 'tanggal_akhir_visa', '', '', '')}
                                       </div>
                                       <div class="col-12 col-lg-3">
                                          ${inputTextForm('Invoice', 'inv', e['data']['invoice'], 'readonly', '')}
                                          ${hiddenForm('invoiceID', e['data']['invoice'] )}
                                       </div>
                                       <div class="col-12 col-lg-3">
                                          ${currencyForm('Pembayaran', 'pembayaran', '', 'onKeyup="get_price_transaksi_paket()"')}
                                       </div>
                                       <div class="col-12 col-lg-3">
                                          ${currencyForm('Sisa', 'sisa', '')}
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-12 col-lg-3 px-2 " >
                                    <div class="form-group form-group-input">
                                       <label class="col-sm-12 col-form-label">Agen</label>
                                       <input type="text" readonly="" name="agen" placeholder="Agen" class="form-control form-control-sm" id="agen" value="Agen Tidak Ditemukan">
                                    </div>
                                    <div id="fee_agen"></div>
                                 </div>
                                 
                              </div>
                              <div class="row mt-3">
                                 <div class="col-12 col-12 p-2 text-right" style="background-color: #e9ecef;">
                                    <div class="row">
                                       <div class="col-12">
                                          <button type="button" class="btn btn-default" onclick="navBtnParam(this, 'transaksi_paket', ${paket_id}, 'Transaksi Paket')">Batal</button>
                                          <button type="submit" class="btn btn-primary">Simpan</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </form>
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
         $('#contentDaftarPaket').html(html);
      },[{paket_id:paket_id}]
   );
}


function transactionProcess(e){
   e.preventDefault();
   var error = 0;
   var error_msg = '';
   var paket_id = $('#paket_id').val();

   if($("#paket_id").length != 0) {
      var jenis_paket = $('#jenis_paket :selected').val();
      var jamaah = $('#jamaah').val();
      if( jamaah == 0 ) {
         error = 1;
         error_msg += error_msg != '' ? '<br>' : '';
         error_msg += 'Silahkan pilih jamaah terlebih dahulu sebelum melanjutkan proses.';
      }
      if( jenis_paket == 0 ){
         error = 1;
         error_msg += error_msg != '' ? '<br>' : '';
         error_msg += 'Silahkan pilih jenis paket jika ingin melanjutkan proses.';
      }
      // error filter
      if( error == 0 ){
         ajax_submit(e, "#form_utama_big", function(e) {
            if (e["error"] == false)
            {
               navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket', true);
               // printing process
               window.open(baseUrl + "Kwitansi/", "_blank");
            }
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         });
      }else{
         frown_alert(error_msg);
      }
   }else{
      frown_alert('Paket ID tidak ditemukan.');
   }
}

function formAddJamaahPaket(JSONdata, JSONvalue){
   var html = '';
   var data = JSON.parse(JSONdata);
   html += `
               <input type="hidden" name="daftarJamaahJSON" id="daftarJamaahJSON" value='${JSONdata}'>
               <div class="form-group form-group-input row">
                  <label for="exampleSelect1" class="col-sm-12 col-form-label">Daftar Jamaah</label>
                  <div class="col-sm-12 px-2" id="listJamaah">`;
      if( JSONvalue != undefined ) {
         var value = JSON.parse(JSONvalue);
         for ( y in value ) {
            html += `<div class="row" >
                        <div class="col-sm-10 pt-0 pb-2">
                           <select class="form-control form-control-sm jamaah" name="jamaah[]" onChange="get_price_transaksi_paket()">`;
                  for( x in data ){
                     html +=  `<option value="${x}" ${ x == value[y] ? 'selected' : '' }>${data[x]}</option>`;
                  }
               html +=    `</select>
                        </div>
                        <div class="col-sm-2 pt-0 pb-2 px-0 pr-2 text-right">
                           <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_transaksi_paket(this)">
                              <i class="fas fa-times" style="font-size: 11px;"></i>
                           </button>
                        </div>
                     </div>`;
         }
      } else {
         html += `<div class="row" >
                     <div class="col-sm-10 pt-0 pb-2">
                        <select class="form-control form-control-sm jamaah" name="jamaah[]" onChange="get_price_transaksi_paket()">`;
               for( x in data ) {
                  html +=  `<option value="${x}">${data[x]}</option>`;
               }
            html +=   `</select>
                     </div>
                     <div class="col-sm-2 pt-0 pb-2 px-0 pr-2 text-right">
                        <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_transaksi_paket(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>`;
      }
   html +=    `</div>
               <div class="col-sm-12 px-2 pt-2" >
                  <button type="button" class="btn btn-default btn-action" title="Delete" onclick="add_jamaah_transaksi_paket()" style="width:100%;">
                     <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Jamaah
                  </button>
               </div>
            </div>`;
   return html;
}

function add_jamaah_transaksi_paket(){
   var jsonDataJamaah = JSON.parse($('#daftarJamaahJSON').val());
   var html = '';
      html += `<div class="row" >
                  <div class="col-sm-10 pt-0 pb-2">
                     <select class="form-control form-control-sm jamaah" name="jamaah[]" onChange="get_price_transaksi_paket()">`;
            for( x in jsonDataJamaah ){
               html +=  `<option value="${x}">${jsonDataJamaah[x]}</option>`;
            }
      html +=       `</select>
                  </div>
                  <div class="col-sm-2 pt-0 pb-2 px-0 pr-2 text-right">
                     <button class="btn btn-default btn-action" title="Delete" onclick="delete_jamaah_transaksi_paket(this)">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </div>
               </div>`;
   $('#listJamaah').append(html);
}

function delete_jamaah_transaksi_paket(e){
   if($('.jamaah').length > 1 ){
      $(e).parent().parent().remove();
      get_price_transaksi_paket();
   }else{
      frown_alert('Anda tidak dapat menghapus semua jamaah!!!.');
   }
}

function get_price_transaksi_paket(){
   var paket_id = $('#paket_id').val();
   var paket_type_id = $('#jenis_paket :selected').val();
   var diskon = $('#diskon').val();
   var pembayaran = 0;
   if( $('#pembayaran').val() != '' ){
      pembayaran = $('#pembayaran').val();
   }
   var jamaah = $('#jamaah :selected').val();
   if( paket_type_id != 0 ){
      ajax_x_t2(
         baseUrl + "Trans_paket/get_price_transaksi_paket", function(e) {
            $('#harga_perpax').val(e['harga_per_pax']);
            $('#totalPaket').val(e['harga_total']);
            $('#biayaMahram').val(e['biaya_mahram']);
            $('#biayaMahramView').val(e['biaya_mahram']);
            $('#sisa').val(e['sisa']);
         },[{paket_id:paket_id,
             paket_type_id:paket_type_id,
             jamaah:jamaah,
             pembayaran:pembayaran,
             diskon:diskon}]
      );
   }
}

// function filterLeaderTeam(){
//    var jamaah = new Array();
//    if( $('.jamaah').length > 0 ){
//        $('.jamaah').each(function(index){
//           if( $(this).val() != 0 ){
//              jamaah.push($(this).val());
//           }
//        });
//    }
//    if( jamaah.length > 0 ){
//       var leader = $('#leader_tim :selected').val();
//       var n = jamaah.includes(leader);
//       if( n === false ){
//          alertRed('Leader tim harus termasuk jamaah yang dipilih');
//       }
//    }else{
//       alertRed('Silahkan pilih jamaah terlebih dahulu sebelum memilih leader.');
//    }
// }

function metodePembayaran(){
   var sumber_biaya =  $('#sumber_biaya :selected').val();

   var html = '';
   ajax_x_t2(
     baseUrl + "Trans_paket/getInvoice", function(e) {

            html += `<div class="col-12 col-lg-3">
                        ${ hiddenForm('invoiceID', e['invoice'] )}
                        ${inputTextForm('Invoice', 'inv', e['invoice'], 'readonly', '')}
                     </div>
                     <div class="col-12 col-lg-6">`;

            if( sumber_biaya == 0 ) {
               html += currencyForm('Pembayaran', 'pembayaran', '', 'onKeyup="get_price_transaksi_paket()"');
            }else{
               html += currencyForm('Pembayaran', 'pembayaran', '9', 'onKeyup="get_price_transaksi_paket()" readonly');
            }

            html += `</div>
                     <div class="col-12 col-lg-3">
                        ${currencyForm('Sisa Pembayaran', 'sisa', '', 'onKeyup="get_price_transaksi_paket()"')}
                     </div>`;

            $('#tempTransaksiPaket').html(html);
            get_price_transaksi_paket();
     },[]
   );
}

function get_agen(){
   var jamaah = $('#jamaah :selected').val();
   var sumber_biaya = $('#sumber_biaya :selected').val();
   if( jamaah != 0 ) {
      if( sumber_biaya == 0 ) { // tunai
         ajax_x(
           baseUrl + "Trans_paket/getInfoFeeAgen", function(e) {
             if(e.error == false ) {
                console.log('123');
               var html = '';
               var fee_keagenan = e.agen_fee;
               for( x in fee_keagenan ){
                  html += `<div class="form-group form-group-input pt-0">
                               <label class="col-sm-12 col-form-label">
                                  Fee ${fee_keagenan[x].level} (${fee_keagenan[x].nama_agen})
                               </label>
                               <input type="hidden" name="lavel_agen_id[${fee_keagenan[x].id}]" value="${fee_keagenan[x].level_agen_id}">
                               <input type="text" name="fee_agen[${fee_keagenan[x].id}]" class="form-control form-control-sm" value="${kurs} ${numberFormat(fee_keagenan[x].fee)}">
                            </div>`;
               }
               // console.log('1111===');
               // console.log(html);
               // $('#fee_agen').html(html);
               $('#agen').parent().replaceWith(`<div class="form-group form-group-input">
                                                   <label class="col-sm-12 col-form-label">Agen</label>
                                                   <input type="text" readonly="" name="agen" placeholder="Agen"
                                                      class="form-control form-control-sm" id="agen" value="${e.nama_agen}">
                                                </div>`);

             }else{
               $('#fee_agen').html('');
               $('#agen').parent().replaceWith(`<div class="form-group form-group-input">
                                                    <label class="col-sm-12 col-form-label">Agen</label>
                                                    <input type="text" readonly="" name="agen" placeholder="Agen"
                                                       class="form-control form-control-sm" id="agen" value="Agen Tidak Ditemukan">
                                                 </div>`);
             }
           },[{jamaah_id:jamaah}]
         );
      }else{
         ajax_x(
            baseUrl + "Trans_paket/getInfoDeposit", function(e) {
               if(e.error == false){
                  if( e.data.agen_selected == 0 ){
                     $('#agen').parent().replaceWith(simpleSelectForm('Agen', 'agen', JSON.stringify(e['list_agen']), `onChange="get_fee(${paket_id})"`,'', 'py-1'));
                     $('#fee_agen').html('');
                  }else{
                     $('#agen').parent().replaceWith(`<div class="form-group form-group-input">
                                                         <label class="col-sm-12 col-form-label">Agen</label>
                                                         <input type="text" readonly="" name="agen" placeholder="Agen" class="form-control form-control-sm" id="agen" value="${e.data.agen_selected}">
                                                      </div>`);
                     var html = '';
                     var fee_keagenan = e.data.fee_agen;
                     for( x in fee_keagenan ){
                        html += `<div class="form-group form-group-input pt-0">
                                    <label class="col-sm-12 col-form-label">
                                       Fee ${fee_keagenan[x].nama} (${fee_keagenan[x].fullname})
                                    </label>
                                    <input type="text" readonly="" class="form-control form-control-sm" value="${kurs} ${numberFormat(fee_keagenan[x].fee)}">
                                 </div>`;
                     }
                     $('#fee_agen').html(html);
                  }
                  // console.log("$$$$$$$$$$$$$$");
                  get_price_transaksi_paket();
               }else{
                  $('#agen').parent().replaceWith(`<div class="form-group form-group-input">
                                                      <label class="col-sm-12 col-form-label">Agen</label>
                                                      <input type="text" readonly="" name="agen" placeholder="Agen" class="form-control form-control-sm" id="agen" value="Agen Tidak Ditemukan">
                                                   </div>`);
                  $('#fee_agen').html('');
               }
            },[{jamaah_id:jamaah}]
         );

         ajax_x(
            baseUrl + "Trans_paket/infoDepositPembayaranJamaah", function(e) {
               if( e.error == false ){
                  $('#pembayaran').parent().replaceWith(currencyForm('Pembayaran', 'pembayaran', kurs + " " + numberFormat(e['pembayaran']), 'onKeyup="get_price_transaksi_paket()" readonly'));
                  $('#tempSumberPembiayaan').html('');
                  get_price_transaksi_paket();
               }
            },[{jamaah_id:jamaah}]
         );
      }
   }else{
      $('#agen').parent().replaceWith(`<div class="form-group form-group-input">
                                          <label class="col-sm-12 col-form-label">Agen</label>
                                          <input type="text" readonly="" name="agen" placeholder="Agen" class="form-control form-control-sm" id="agen" value="Agen Tidak Ditemukan">
                                       </div>`);
      $('#fee_agen').html('');
   }
}

function sumberBiaya(paket_id){
   var jamaah = $('#jamaah :selected').val();
   var sumber_biaya = $('#sumber_biaya :selected').val();
   var html = '';
   if ( sumber_biaya == 1 ) {
      if ( jamaah != 0 ) {
         ajax_x(
            baseUrl + "Trans_paket/infoDepositPembayaranJamaah", function(e) {
               if ( e.error == false ) {
                  $('#pembayaran').parent().replaceWith(currencyForm('Pembayaran', 'pembayaran', kurs + " " + numberFormat(e['pembayaran']), 'onKeyup="get_price_transaksi_paket()" readonly'));
                  $('#tempSumberPembiayaan').html('');
               } else {
                  $("#sumber_biaya").select2("val", "0");
                  frown_alert(e.error_msg);
               }
               get_price_transaksi_paket();
            },[{jamaah_id:jamaah}]
         );
      } else {
         $("#sumber_biaya").select2("val", "0");
         frown_alert('Untuk sumber deposit, jamaah wajib dipilih terlebih dahulu.');
      }
   } else {
      html += `<div class="col-12 col-lg-4">${inputTextForm('Penyetor', 'penyetor', '', '', '')}</div>
               <div class="col-12 col-lg-4">${inputTextForm('No HP Penyetor', 'hp_penyetor', '', 'required', '')}</div>
               <div class="col-12 col-lg-4">${inputTextForm('Alamat Penyetor', 'alamat_penyetor', '', ' required ', '')}</div>`;
      $('#tempSumberPembiayaan').html(html);
      $('#pembayaran').parent().replaceWith(currencyForm('Pembayaran', 'pembayaran', '', 'onKeyup="get_price_transaksi_paket()"'));
      get_price_transaksi_paket();
   }
   get_agen();
}


function deleteTransaksiPaket( paket_id, paket_transaction_id, state ){
   $.confirm({
      // columnClass: 'col-8',
      title: 'Peringatan Hapus Transaksi',
      theme: 'material',
      content:"Anda akan menghapus seluruh informasi transaksi dari transaksi paket ini. Apakah anda ingin melanjutkan?.",
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         Ya: {
            text: 'Hapus Sekarang',
            btnClass: 'btn-red',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/deleteTransaksiPaket", function(e) {
                     if( state == 'perpaket' ) {
                        navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket', true);
                     }else if ( state == 'allpaket' ) {
                        navBtn(this, 'landing_daftarAllTransactionPaket', 'Pembayaran Paket', '')
                     }
                  },[{paket_id:paket_id, paket_transaction_id: paket_transaction_id}]
               );
            }
         }
      }
   });
}

function formRefundTransaksiPaket(paket_id, paket_transaction_id, state){
   ajax_x(
      baseUrl + "Trans_paket/getInfoRefund", function(e) {
         var form = `<form action="${baseUrl }Trans_paket/refundProcess" id="form_utama" class="formName ">
                        <input type="hidden" name="paket_transaction_id" id="paket_transaction_id" value="${paket_transaction_id}">
                        <div class="row px-0 mx-0">
                           <div class="col-6" >
                              <label>Riwayat Pembayaran</label>
                           </div>
                           <div class="col-6 text-right" >
                              <label style="color: red;">Invoice : #${e['invoiceID']}</label>
                              <input type="hidden" name="invoiceID" value="${e['invoiceID']}">
                           </div>
                           <div class="col-12" >
                              <table class="table table-hover">
                                 <thead>
                                    <tr>
                                       <th style="width:20%;">Invoice</th>
                                       <th style="width:20%;">Bayar</th>
                                       <th style="width:20%;">Penyetor/<br>Penerima</th>
                                       <th style="width:20%;">Ket</th>
                                       <th style="width:20%;">Tanggal</th>
                                    </tr>
                                 </thead>
                                 <tbody >`;

                     if( e['listRiwayat'] != undefined  &&  e['listRiwayat'].length > 0  )
                     {
                        for ( x in e['listRiwayat'] )
                        {
                           form += `<tr>
                                       <td>${e['listRiwayat'][x]['invoice']}</td>
                                       <td>${e['listRiwayat'][x]['paid']}</td>
                                       <td class="text-left">Penyetor :<br><span class="float-right font-weight-bold">${e['listRiwayat'][x]['penyetor']}</span><br>Penerima : <br><span class="float-right font-weight-bold">${e['listRiwayat'][x]['penerima']}</span></td>
                                       <td class="text-capitalize">${e['listRiwayat'][x]['ket']}</td>
                                       <td>${e['listRiwayat'][x]['tanggal_transaksi']}</td>
                                    </tr>`;
                        }
                     }else{
                        form += `<tr>
                                    <td colspan="5"> Riwayat pembayaran tidak ditemukan. </td>
                                 </tr>`;
                     }

                     form +=    `</tbody>
                              </table>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Total Pembayaran Paket</label>
                                 <input type="text" class="username form-control form-control-sm" value="${kurs + ' ' + numberFormat(e['totalPembayaran'])}" readonly />
                                 <input type="hidden" name="total_pembayaran_paket" id="total_pembayaran_paket" value="${e['totalPembayaran']}" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Yang Tidak di Refund</label>
                                 <input type="text" id="tidakrefund" placeholder="Yang Tidak di Refund" class="currency username form-control form-control-sm" value="${kurs + ' ' + numberFormat(e['totalPembayaran'])}" readonly  />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Yang di Refund</label>
                                 <input type="text" name="refund" id="refund" placeholder="Refund" class="currency username form-control form-control-sm" onKeyup="getNotRefund()"/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Status Berangkat</label>
                                 <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" value="1" name="batalBerangkat">
                                    <label class="form-check-label">
                                       Batal berangkat
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nama Penyetor</label>
                                 <input type="text" name="deposit_name" placeholder="Nama Penyetor" class="nama_penyetor form-control form-control-sm" required />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>HP Penyetor</label>
                                 <input type="text" name="hp_deposit" placeholder="Nomor HP Penyetor" class="hp_penyetor form-control form-control-sm" required />
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Alamat Penyetor</label>
                                 <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3" style="resize: none;"></textarea>
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

                     $.confirm({
                        columnClass: 'col-8',
                        title: 'Form Refund Transaksi',
                        theme: 'material',
                        content:form,
                        closeIcon: false,
                        buttons: {
                           cancel: function () {
                                return true;
                           },
                           refund: {
                              text: 'Refund',
                              btnClass: 'btn-blue',
                              action: function () {
                                 ajax_submit_t1("#form_utama", function(e) {
                                    e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                                 	if ( e['error'] == true ) {
                                 		return false;
                                 	} else {
                                       if( state == 'perpaket' ) {
                                          navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket');
                                          window.open(baseUrl + "Kwitansi/", "_blank");
                                       }else if ( state == 'allpaket' ) {
                                          navBtn(this, 'landing_daftarAllTransactionPaket', 'Pembayaran Paket', '')
                                          window.open(baseUrl + "Kwitansi/", "_blank");
                                       }
                                 	}
                                 });
                              }
                           }
                        }
                     });
      },[{paket_transaction_id: paket_transaction_id}]
   );
}

function getNotRefund(){
   var total_pembayaran = $('#total_pembayaran_paket').val();
   var refund = hide_currency($('#refund').val());
   var tidakRefund = total_pembayaran - refund;
   if( total_pembayaran >= refund ){
      $('#tidakrefund').val(kurs + ' ' + numberFormat(tidakRefund));
   }
}

function formPembayaranCash(paket_id, paket_transaction_id, state){
   ajax_x(
      baseUrl + "Trans_paket/getInfoPembayaranCash", function(e) {
         var form = `<form action="${baseUrl }Trans_paket/pembayaranCash" id="form_utama" class="formName ">
                        <input type="hidden" name="paket_transaction_id" id="paket_transaction_id" value="${paket_transaction_id}">
                        <div class="row px-0 mx-0">
                           <label>Riwayat Pembayaran</label>
                           <div class="col-12" >
                              <table class="table table-hover">
                                 <thead>
                                    <tr>
                                       <th style="width:20%;">Invoice</th>
                                       <th style="width:20%;">Bayar</th>
                                       <th style="width:20%;">Penyetor/<br>Penerima</th>
                                       <th style="width:20%;">Ket</th>
                                       <th style="width:20%;">Tanggal</th>
                                    </tr>
                                 </thead>
                                 <tbody >`;

                     if( e['data'] != undefined  &&  e['data'].length > 0  )
                     {
                        for ( x in e['data'] )
                        {
                           form += `<tr>
                                       <td>${e['data'][x]['invoice']}</td>
                                       <td>${e['data'][x]['paid']}</td>
                                       <td class="text-left">
                                          Penyetor :<br>
                                             <span class="float-right font-weight-bold">${e['data'][x]['penyetor']}</span><br>
                                          Penerima : <br><span class="float-right font-weight-bold">${e['data'][x]['penerima']}</span>
                                       </td>
                                       <td class="text-capitalize text-left">
                                          Metode Pembayaran : <br>
                                             <span class="float-right font-weight-bold">${e['data'][x]['ket']}</span><br>
                                          Sumber Biaya :<br><span class="float-right font-weight-bold">${e['data'][x]['sumber_biaya']}</span>
                                       </td>
                                       <td>${e['data'][x]['tanggal_transaksi']}</td>
                                    </tr>`;
                        }
                     }else{
                        form += `<tr>
                                    <td colspan="5"> Riwayat pembayaran tidak ditemukan. </td>
                                 </tr>`;
                     }
                     form +=    `</tbody>
                              </table>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Total Harga Paket</label>
                                 <input type="text" name="total_harga_paket" id="total_harga_paket" placeholder="Total Harga Paket" class="username form-control form-control-sm" value="${e['total_harga']}" readonly />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Sudah Dibayar</label>
                                 <input type="text" name="sudah_dibayar" id="sudah_dibayar" placeholder="Sudah Dibayar" class="username form-control form-control-sm" value="${e['total_bayar'] }" readonly />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Sisa</label>
                                 <input type="text" name="sisa" placeholder="Sisa" id="sisaPembayaran" class="username form-control form-control-sm" value="${e['sisa']}" readonly />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Invoice</label>
                                 <input type="text" name="invoiceView" placeholder="invoice" id="invoiceView" class="username form-control form-control-sm" value="${e['invoice']}" readonly />
                                 <input type="hidden" name="invoice" id="invoice" value="${e['invoice']}" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group  ">
                                 <label >Sumber Biaya</label>
                                 <input type="text" name="sumber_biaya" placeholder="sumber biaya" id="sumber_biaya" class="sumber_biaya form-control form-control-sm" value="Tunai" readonly />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Bayar</label>
                                 <input type="text" name="bayar" placeholder="Bayar" class="currency fullname form-control form-control-sm" required />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="row" id="infoPenyetor">
                                 <div class="col-4">
                                    <div class="form-group">
                                       <label>Nama Penyetor</label>
                                       <input type="text" name="deposit_name" placeholder="Nama Penyetor" class="nama_penyetor form-control form-control-sm" required />
                                    </div>
                                 </div>
                                 <div class="col-3">
                                    <div class="form-group">
                                       <label>HP Penyetor</label>
                                       <input type="text" name="hp_deposit" placeholder="Nomor HP Penyetor" class="hp_penyetor form-control form-control-sm" required />
                                    </div>
                                 </div>
                                 <div class="col-5">
                                    <div class="form-group">
                                       <label>Alamat Penyetor</label>
                                       <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3" style="resize: none;"></textarea>
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
            $.confirm({
               columnClass: 'col-8',
               title: 'Form Pembayaran Cash',
               theme: 'material',
               content:form,
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  cetakKwitansi: {
                     text: 'Cetak Kwitansi Terakhir',
                     btnClass: 'btn-green',
                     action: function () {
                        ajax_x(
                           baseUrl + "Trans_paket/lastKwitansiCash", function(e) {
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           },[{paket_transaction_id: paket_transaction_id}]
                        );
                     }
                  },
                  formSubmit: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var sisa_pembayaran = hide_currency($('#sisaPembayaran').val());
                        if( sisa_pembayaran == 0 ){
                           frown_alert('Biaya sudah dilunasi, anda tidak dapat melakukan pembayaran melebihi dari biaya paket!!!.');
                           return false;
                        }else{
                           ajax_submit_t1("#form_utama", function(e) {
                              e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           	if ( e['error'] == true ) {
                           		return false;
                           	} else {
                                 if( state == 'perpaket' ) {
                                    navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket');
                                    window.open(baseUrl + "Kwitansi/", "_blank");
                                 }else if ( state == 'allpaket' ) {
                                    navBtn(this, 'landing_daftarAllTransactionPaket', 'Pembayaran Paket', '')
                                    window.open(baseUrl + "Kwitansi/", "_blank");
                                 }
                           	}
                           });
                        }
                     }
                  }
               }
            });
      },[{paket_transaction_id: paket_transaction_id}]
   );
}

function getInfoDeposit(){
   var sumberBiaya = $('#sumber_biaya').val();
   var html = '';
   if( sumberBiaya == '1'){
      ajax_x_t2(
         baseUrl + "Trans_paket/getPersonalInfoPaket", function(e) {
            if( e.error == false ) {
               html += `<div class="col-12 col-lg-12">
                           <div class="form-group  ">
                              <label >Penyetor</label>
                              <select class="form-control form-control-sm" id="penyetorDeposit" name="penyetorDeposit">`;
                     for( x in e['data']['listpersonal'] ){
                        html +=   `<option value="${e['data']['listpersonal'][x]['id']}">${e['data']['listpersonal'][x]['name']}</option>`;
                     }
                     html += `</select>
                           </div>
                        </div>`;
               $('#infoPenyetor').html(html);
            }
         },[]
      );
   }else{
      html += `<div class="col-4">
                  <div class="form-group">
                     <label>Nama Penyetor</label>
                     <input type="text" name="deposit_name" placeholder="Nama Penyetor" class="nama_penyetor form-control form-control-sm" required />
                  </div>
               </div>
               <div class="col-3">
                  <div class="form-group">
                     <label>HP Penyetor</label>
                     <input type="text" name="hp_deposit" placeholder="Nomor HP Penyetor" class="hp_penyetor form-control form-control-sm" required />
                  </div>
               </div>
               <div class="col-5">
                  <div class="form-group">
                     <label>Alamat Penyetor</label>
                     <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3" style="resize: none;"></textarea>
                  </div>
               </div>`;
      $('#infoPenyetor').html(html);
   }
}

function formHandoverBarang(paket_transaction_id, jamaah_id, JSONdata){
   var e = JSON.parse(JSONdata);
   var form = `<form action="${baseUrl }Trans_paket/prosesHandOverBarang" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_transaction_id" value="${paket_transaction_id}">
                  <input type="hidden" name="jamaah_id" value="${jamaah_id}">
                  <input type="hidden" name="invoice" value="${e['invoice']}">
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <label>Kode invoice: #${e['invoice']}</label>
                     </div>
                     <div class="col-12 px-0" >
                        <div class="row px-0 mx-0" >
                           <div class="col-12 " >
                              <div class="row" >
                                 <div class="col-5 pt-2 px-2">
                                    <div class="form-group">
                                       <input class="form-control form-control-sm" type="text" id="nama_pemberi_barang" name="nama_pemberi_barang" placeholder="Nama Pemberi Barang" >
                                    </div>
                                 </div>
                                 <div class="col-4 pt-2 px-0">
                                    <div class="form-group">
                                       <input class="form-control form-control-sm" type="text" id="no_identitas_pemberi_barang" name="no_identitas_pemberi_barang" placeholder="No Identitas Pemberi Barang" >
                                    </div>
                                 </div>
                                 <div class="col-3 pt-2 px-2">
                                    <div class="form-group">
                                       <input class="form-control form-control-sm" type="text" id="no_hp_pemberi_barang" name="no_hp_pemberi_barang" placeholder="No HP Pemberi Barang" >
                                    </div>
                                 </div>
                                 <div class="col-12 pt-2 px-2">
                                    <div class="form-group">
                                       <textarea class="form-control form-control-sm" name="alamat_pemberi_barang" id="alamat_pemberi_barang" placeholder="Alamat Pemberi Barang" rows="3" style="resize: none;"></textarea>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-12" id="areaInput">
                              <div class="row">
                                 <div class="col-11 py-1">
                                    <input class="form-control form-control-sm item" type="text" name="item[]" placeholder="Nama Barang" required>
                                 </div>
                                 <div class="col-1 py-1 text-right">
                                    <button class="btn btn-default btn-action" title="Delete" onclick="deleteItem(this)">
                                       <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row px-0 mx-0">
                           <div class="col-sm-8 pt-2" ></div>
                           <div class="col-sm-4 pt-2 " >
                              <button type="button" class="btn btn-default btn-action" title="Tambah Barang" onclick="addHandOverItem()" style="width:100%;">
                                 <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Barang
                              </button>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;

   return form;
}

function formInfoHandoverBarang(paket_transaction_id, jamaah_id, JSONData){
   var e = JSON.parse(JSONData);
   var form = `<form action="${baseUrl }Trans_paket/returnHandOverItem" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_transaction_id" value="${paket_transaction_id}">
                  <input type="hidden" name="jamaah_id" value="${jamaah_id}">
                  <input type="hidden" name="invoice" value="${e['invoice']}">
                  <div class="row px-0 mx-0">
                     <div class="col-6 px-0" >
                        <label>History Penerimaan Barang</label>
                     </div>
                     <div class="col-6 px-0 text-right" >
                        <label>Kode invoice: #${e['invoice']}</label>
                     </div>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:29%;" scope="col">Nama Item</th>
                                 <th style="width:13%;" scope="col">Serah</th>
                                 <th style="width:13%;" scope="col">Terima</th>
                                 <th style="width:15%;" scope="col">Tgl. Terima</th>
                                 <th style="width:15%;" scope="col">Tgl. Dikembali</th>
                                 <th style="width:10%;" scope="col">Status</th>
                                 <th style="width:5%;" scope="col">Menu</th>
                              </tr>
                           </thead>
                           <tbody id="list_item">`;
               if( e['error'] == true ) {
                     form += `<tr >
                                 <td colspan="7"><center>Data History Transaksi Tidak Ditemukan</center></td>
                              <tr>`;
               }else {
                  for( x in e['data'] ) {
                     form += `<tr>
                                 <td>${e['data'][x]['item_name']}</td>
                                 <td class="text-left">
                                    Penyerah :<br>
                                    <span class="float-right font-weight-bold text-right">${e['data'][x]['giver_handover']}</span>
                                    <br>
                                    Penerima :<br>
                                    <span class="float-right font-weight-bold text-right">${e['data'][x]['receiver_handover']}</span>
                                 </td>
                                 <td class="text-left">
                                    Penyerah :<br>
                                    <span class="float-right font-weight-bold text-right">${e['data'][x]['giver_returned']}</span>
                                    <br>
                                    Penerima :<br>
                                    <span class="float-right font-weight-bold text-right">${e['data'][x]['receiver_returned']}</span>
                                 </td>
                                 <td>${e['data'][x]['date_taken']}</td>
                                 <td>${e['data'][x]['date_returned']}</td>
                                 <td>${e['data'][x]['status']}</td>
                                 <td class="py-0">`;
                        if( e['data'][x]['status'] == 'diambil' ) {
                           form += `<center>
                                       <div class="form-check pl-4">
                                         <label class="form-check-label">
                                           <input type="checkbox" class="form-check-input idItem" name="item[]" value="${e['data'][x]['id']}">
                                         </label>
                                       </div>
                                    </center>`;
                        } else {
                           form += `<center>
                                       <div class="pt-3">
                                          <i class="fas fa-check-double" style="color: #27ae60;"></i>
                                       </div>
                                    </center>`;
                        }
                     form +=    `</td>
                              <tr>`;
                  }
               }
               form +=    `</tbody>
                        </table>
                     </div>
                     <div class="col-12 px-0" >
                        <label>Form Pengembalian Barang</label>
                     </div>
                     <div class="col-5 pt-2 px-2">
                        <div class="form-group">
                           <input class="form-control form-control-sm" type="text" id="nama_penerima_barang" name="nama_penerima_barang" placeholder="Nama Penerima Barang" required >
                        </div>
                     </div>
                     <div class="col-4 pt-2 px-0">
                        <div class="form-group">
                           <input class="form-control form-control-sm" type="text" id="no_identitas" name="no_identitas_penerima_barang" placeholder="No Identitas Penerima Barang" required>
                        </div>
                     </div>
                     <div class="col-3 pt-2 px-2">
                        <div class="form-group">
                           <input class="form-control form-control-sm" type="text" id="no_hp_penerima_barang" name="no_hp_penerima_barang" placeholder="No HP Penerima Barang" required>
                        </div>
                     </div>
                     <div class="col-12 pt-2 px-2">
                        <div class="form-group">
                           <textarea class="form-control form-control-sm" name="alamat_penerima_barang" id="alamat_penerima_barang" placeholder="Alamat Penerima Barang" rows="3" style="resize: none;" required></textarea>
                        </div>
                     </div>
                  </div>
               </form>`;

   return form;
}

// form handover barang
function handoverBarang(paket_id, jamaah_id, paket_transaction_id){
   $.confirm({
      columnClass: 'col-6',
      title: 'Form Handover Dan Pengembalian Barang Jamaah',
      theme: 'material',
      content: 'Silahkan pilih jenis transaksi yang akan dilakukan.',
      closeIcon: false,
      buttons: {
         batal: function (){
            return true;
         },
         terimabarang: {
            text: 'Handover Barang Jamaah',
            btnClass: 'btn-green',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/getInvoiceHandoverBarang", function(e) {
                     $.confirm({
                        columnClass: 'col-8',
                        title: 'Form Handover Barang Jamaah',
                        theme: 'material',
                        content: formHandoverBarang( paket_transaction_id, jamaah_id, JSON.stringify(e) ),
                        closeIcon: false,
                        buttons: {
                           batal: function (){
                              return true;
                           },
                           terimabarang: {
                              text: 'Terima Barang Dari Jamaah ',
                              btnClass: 'btn-blue',
                              action: function () {
                                 if( $('.item').length > 0 ) {
                                    var error = 0;
                                    var error_msg = '';
                                    if( $('#nama_pemberi_barang').val() == '') {
                                       error = 1;
                                       error_msg += 'Nama Pemberi Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( $('#no_identitas_pemberi_barang').val() == '') {
                                       error = 1;
                                       error_msg += 'Nomor Identitas Pemberi Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( $('#no_hp_pemberi_barang').val() == '') {
                                       error = 1;
                                       error_msg += 'Nomor HP Pemberi Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( $('#alamat_pemberi_barang').val() == '') {
                                       error = 1;
                                       error_msg += 'Alamat Pemberi Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( error == 1 ) {
                                       frown_alert(error_msg);
                                       return false;
                                    } else {
                                       ajax_submit_t1("#form_utama", function(e) {
                                          e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                                          if ( e['error'] == true ) {
                                             return false;
                                          } else {
                                             navBtnParam(this, 'daftar_jamaah_paket',  paket_id, 'Daftar Jamaah Paket', true);
                                             window.open(baseUrl + "Kwitansi/", "_blank");
                                          }
                                       });
                                    }
                                 } else {
                                    frown_alert('Untuk melanjutkan, nama barang tidak boleh kosong.');
                                    return false;
                                 }
                              }
                           }
                        }
                     });
                  },[]
               );
            }
         },
         kembalibarang: {
            text: 'Pengembalian Barang Jamaah',
            btnClass: 'btn-blue',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/infoHandOverBarang", function(e) {
                     $.confirm({
                        columnClass: 'col-8',
                        title: 'Form Pengembalian Barang Jamaah',
                        theme: 'material',
                        content: formInfoHandoverBarang(paket_transaction_id, jamaah_id, JSON.stringify(e)),
                        closeIcon: false,
                        buttons: {
                           batal: function (){
                              return true;
                           },
                           kembaliBarang: {
                              text: 'Kembalikan Barang Jamaah ',
                              btnClass: 'btn-blue',
                              action: function () {
                                 var c_checkbox = $('.idItem:checkbox:checked');
                                 if( c_checkbox.length > 0 ){
                                    var error = 0;
                                    var error_msg = '';
                                    if( $('#nama_penerima_barang').val() == ''){
                                       error = 1;
                                       error_msg += 'Nama Penerima Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( $('#no_identitas').val() == ''){
                                       error = 1;
                                       error_msg += 'Nomor Identitas Penerima Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( $('#no_hp_penerima_barang').val() == ''){
                                       error = 1;
                                       error_msg += 'Nomor HP Penerima Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( $('#alamat_penerima_barang').val() == ''){
                                       error = 1;
                                       error_msg += 'Alamat Penerima Barang <b>Tidak Boleh Kosong.</b><br>';
                                    }
                                    if( error == 1 ) {
                                       frown_alert(error_msg);
                                       return false;
                                    }else{
                                       ajax_submit_t1("#form_utama", function(e) {
                                          e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                                          if ( e['error'] == true ) {
                                             return false;
                                          } else {
                                             navBtnParam(this, 'daftar_jamaah_paket',  paket_id, 'Daftar Jamaah Paket', true);
                                             window.open(baseUrl + "Kwitansi/", "_blank");
                                          }
                                       });
                                    }
                                 }else{
                                    frown_alert('Untuk mengembalikan barang, anda wajib memilih salah satu barang yang akan dikembalikan.');
                                    return false;
                                 }
                              }
                           },
                        }
                     });
                  },[{jamaah_id:jamaah_id, paket_transaction_id:paket_transaction_id}]
               );
            }
         }
      }
   });
}

function formHandoverFasilitas( paket_transaction_id, jamaah_id, JSONData ){

   var e = JSON.parse(JSONData);
   var form = `<form action="${baseUrl }Trans_paket/handoverFasilitas" id="form_utama" class="formName ">
               <input type="hidden" name="paket_transaction_id" value="${paket_transaction_id}">
               <input type="hidden" name="jamaah_id" value="${jamaah_id}">
               <input type="hidden" name="invoice" value="${e['invoice']}">
               <div class="row px-0 mx-0">
                  <div class="col-6" >
                     <label>History Penerimaan Fasilitas</label>
                  </div>
                  <div class="col-6 float-right text-right" >
                     <label>Kode Invoice: #${e['invoice']}</label>
                  </div>
                  <div class="col-12" >
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:15%;" scope="col">Invoice</th>
                              <th style="width:20%;" scope="col">Nama Item</th>
                              <th style="width:20%;" scope="col">Penerima</th>
                              <th style="width:20%;" scope="col">Tgl. Terima</th>
                              <th style="width:20%;" scope="col">Petugas</th>
                              <th style="width:5%;" scope="col">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="list_facilities">`;
            if( e['list_barang'].length > 0 ){
               for( x in e['list_barang'] ){
                  form += `<tr>
                              <td>${ e['list_barang'][x]['invoice'] }</td>
                              <td>${ e['list_barang'][x]['facilities_name'] }</td>
                              <td>${ e['list_barang'][x]['receiver_name'] }<br>(${ e['list_barang'][x]['receiver_identity'] })</td>
                              <td>${ e['list_barang'][x]['date_transaction'] }</td>
                              <td>${ e['list_barang'][x]['petugas'] }</td>
                              <td>
                                 <button type="button" class="btn btn-default btn-action" title="Hapus Handover Fasilitas" onclick="deleteHandoverFasilitas(${ e['list_barang'][x]['id'] })">
                                    <i class="fas fa-times" style="font-size: 11px;"></i>
                                 </button>
                              </td>
                           <tr>`;
               }
            }else{
               form +=    `<tr>
                              <td colspan="5"><center>Data History Transaksi Tidak Ditemukan</center></td>
                           <tr>`;
            }
            form +=    `</tbody>
                     </table>
                  </div>
                  <div class="col-12" >
                     <div class="row" >
                        <div class="col-4" >
                           <div class="form-group">
                              <label class="col-form-label col-form-label-sm">Nama Penerima Fasilitas</label>
                              <input class="form-control form-control-sm" type="text" placeholder="Nama Penerima Fasilitas" id="nama_penerima" name="nama_penerima">
                           </div>
                           <div class="form-group">
                              <label class="col-form-label col-form-label-sm">No Identitas Penerima Fasilitas</label>
                              <input class="form-control form-control-sm" type="text" placeholder="No Identitas Penerima Fasilitas" id="no_identitas" name="no_identitas">
                           </div>
                        </div>
                        <div class="col-8" >
                           <label>Fasilitas paket yang diterima</label>
                           <div class="row" >`;
                  if( e['list_fasilitas'].length > 0 ){
                     for( y in e['list_fasilitas'] ){
                        form += `<div class="col-4">
                                    <div class="form-check">
                                       <label class="form-check-label">
                                          <input class="form-check-input fasilitas" type="checkbox" value="${e['list_fasilitas'][y]['id']}" name="fasilitas[]">
                                          ${e['list_fasilitas'][y]['name']}
                                       </label>
                                    </div>
                                 </div>`;
                     }
                  }else{
                     form += `<div class="col-12 pt-4">
                                 <center><span>Daftar Fasilitas Tidak Ditemukan.</span></center>
                              </div>`;
                  }
               form +=    `</div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>`;

   return form;
}

function handoverFasilitas(paket_id, jamaah_id, paket_transaction_id){
   ajax_x(
      baseUrl + "Trans_paket/infoHandOverFasilitas", function(e) {

         $.confirm({
            columnClass: 'col-8',
            title: 'Form Handover Fasilitas Jamaah',
            theme: 'material',
            content: formHandoverFasilitas(paket_transaction_id, jamaah_id, JSON.stringify(e)),
            closeIcon: false,
            buttons: {
               batal: function (){
                  return true;
               },
               fasilitasdiserahkan: {
                  text: 'Fasilitas yang diserahkan',
                  btnClass: 'btn-blue',
                  action: function () {

                     var error = 0;
                     var error_msg = '';
                     if( $('#nama_penerima').val() == ''){
                        error = 1;
                        error_msg += 'Nama Penerima Fasilitas <b>Tidak Boleh Kosong.</b><br>';
                     }

                     if( $('#no_identitas').val() == ''){
                        error = 1;
                        error_msg += 'Nomor Identitas Penerima Fasilitas <b>Tidak Boleh Kosong.</b><br>';
                     }

                     var c_checkbox = $('.fasilitas:checkbox:checked');
                     if( c_checkbox.length <= 0 ){
                        error = 1;
                        error_msg += 'Untuk Melanjutkan, <b>Anda Wajib Memilih Minimal Satu Fasilitas.</b><br>';
                     }

                     if( error == 1 ){
                        frown_alert(error_msg);
                        return false;
                     }else{
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              navBtnParam(this, 'daftar_jamaah_paket',  paket_id, 'Daftar Jamaah Paket', true);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            }
         });
      },[{paket_transaction_id:paket_transaction_id, jamaah_id:jamaah_id}]
   );
}

// delete handover facilities
function deleteHandoverFasilitas( id ){
   ajax_x(
      baseUrl + "Trans_paket/delete_handover_fasilitas", function(e) {
         var html = '';
         // list_facilities
         if(e['list_handover_facilities'].length > 0 ){
            for( x in e['list_handover_facilities'] ){
               html += `<tr>
                           <td>${ e['list_handover_facilities'][x]['invoice'] }</td>
                           <td>${ e['list_handover_facilities'][x]['facilities_name'] }</td>
                           <td>${ e['list_handover_facilities'][x]['receiver_name'] }<br>(${ e['list_handover_facilities'][x]['receiver_identity'] })</td>
                           <td>${ e['list_handover_facilities'][x]['date_transaction'] }</td>
                           <td>${ e['list_handover_facilities'][x]['petugas'] }</td>
                           <td>
                              <button type="button" class="btn btn-default btn-action" title="Hapus Handover Fasilitas" onclick="deleteHandoverFasilitas(${ e['list_handover_facilities'][x]['id'] })">
                                 <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </td>
                        <tr>`;
            }
         }
         $('#list_facilities').html(html);
      },[{handover_facilities_id:id}]
   );
}

function formPindahPaket(paket_id, paket_transaction_id, jamaah_id, JSONData){
   var e = JSON.parse( JSONData );
   var form = `<form action="${baseUrl }Trans_paket/pindahPaket" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_id" value="${paket_id}">
                  <input type="hidden" name="jamaah_id" value="${jamaah_id}">
                  <input type="hidden" name="paket_transaction_id" value="${paket_transaction_id}">
                  <div class="row px-0 mx-0">
                     <div class="col-7" >
                        <div class="form-group">
                           <label>Paket Sekarang</label>
                           <input type="text" class="form-control form-control-sm" value="${e['paket_sekarang']}" readonly />
                        </div>
                     </div>
                     <div class="col-sm-5" >
                        <div class="form-group">
                           <label>Total Harga Paket Sekarang</label>
                           <input type="text" class="form-control form-control-sm" value="${e['total_harga_paket_sekarang']}" readonly />
                        </div>
                     </div>
                     <div class="col-sm-4" >
                        <div class="form-group">
                           <label>Harga Per Paket Sekarang</label>
                           <input type="text" class="form-control form-control-sm" value="${e['harga_per_paket_sekarang']}" readonly />
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Biaya Yang Sudah Dibayar Sekarang</label>
                           <input type="text" class="form-control form-control-sm" value="${e['biaya_yang_sudah_dibayar_sekarang']}" readonly />
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Sisa Pembayaran Sekarang</label>
                           <input type="text" class="form-control form-control-sm" value="${e['sisa_pembayaran_sekarang']}" readonly />
                        </div>
                     </div>
                     <div class="col-7" >
                        <div class="form-group">
                           <label>Paket Tujuan</label>
                           <select class="form-control form-control-sm" name="paket_tujuan" id="paket_tujuan" onChange="getTipeHargaPaket('${paket_id}')">
                              <option value="0">Pilih Paket Tujuan</option>`;
                  for( x in e['list_paket'] ) {
                     form += `<option value="${e['list_paket'][x]['id']}">${e['list_paket'][x]['paket_name']}</option>`;
                  }
               form +=    `</select>
                        </div>
                     </div>
                     <div class="col-5" >
                        <div class="form-group">
                           <label>Tipe Aksi Pindah Paket</label>
                           <select class="form-control form-control-sm" name="tipe_aksi" id="tipe_aksi" onChange="getTipeHargaPaket('${paket_id}')">
                              <option value="0">Buat Transaksi Baru</option>
                              <option value="1">Masuk Ke Transaksi Sudah Ada</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group" id="formInputTipePaket">
                           <label>Tipe Paket Tujuan</label>
                           <select class="form-control form-control-sm" name="tipe_paket_no_reg_tujuan" id="tipe_paket_no_reg_tujuan" onChange="getHargaPaketByTipePaket('${paket_id}', '${paket_transaction_id}', '${jamaah_id}')">
                              <option value="0">Pilih Tipe Paket Tujuan</option>`;
                  for( x in e['list_paket'] ){
                     form += `<option value="${e['list_paket'][x]['id']}">${e['list_paket'][x]['paket_name']}</option>`;
                  }
               form +=    `</select>
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Harga Paket Tujuan</label>
                           <input type="text" name="harga_paket_tujuan" id="harga_paket_tujuan" value="${kurs} 0" class="form-control form-control-sm" readonly />
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Biaya Yang Dipindahkan</label>
                           <input type="text" name="biaya_yang_dipindah" id="biaya_yang_dipindah" value="${kurs} 0" onKeyup="getHargaPaketByTipePaket('${paket_id}','${paket_transaction_id}', '${jamaah_id}')" class="currency form-control form-control-sm" />
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Sisa Pembayaran</label>
                           <input type="text" name="sisa_pembayaran" id="sisa_pembayaran" placeholder="Sisa Pembayaran" value="${kurs} 0" class="form-control form-control-sm" readonly/>
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Pembayaran Berlebih</label>
                           <input type="text" name="pembayaran_berlebih" id="pembayaran_berlebih" placeholder="Pembayaran Berlebih" value="${kurs} 0" class="form-control form-control-sm" readonly />
                           <input type="hidden" name="pembayaran_berlebih_val" id="pembayaran_berlebih_val" />
                        </div>
                     </div>
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Biaya Yang Direfund</label>
                           <input type="text" name="refund" id="refund" placeholder="Biaya Yang Direfund" value="${kurs} 0" class="form-control form-control-sm currency" onKeyup="getHargaPaketByTipePaket('${paket_id}','${paket_transaction_id}', '${jamaah_id}')"  />
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
   return form;
}

function pindahPaket(paket_id, jamaah_id, paket_transaction_id){
      ajax_x(
         baseUrl + "Trans_paket/infoPindahPaketJamaah", function(e) {
            $.confirm({
               title: 'Form Pindah Paket Jamaah',
               theme: 'material',
               columnClass: 'col-6',
               content: formPindahPaket(paket_id, paket_transaction_id, jamaah_id, JSON.stringify(e)),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  formSubmit: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var error = 0;
                        var error_msg = '';
                        var pembayaranBerlebihanVal = $('#pembayaran_berlebih_val').val();
                        console.log(pembayaranBerlebihanVal);
                        if( pembayaranBerlebihanVal != undefined ){
                           var pembayaran_berlebih = hide_currency(pembayaranBerlebihanVal);
                           if( pembayaran_berlebih != 0 ){
                              error = 1;
                              error_msg = 'Anda wajib <b>meRefund</b> setiap pembayaran berlebih';
                           }
                        }
                        if ( error == 0 ){
                           ajax_submit_t1("#form_utama", function(e) {
                              e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                              if ( e['error'] == true ) {
                                 return false;
                              } else {
                                 navBtnParam(this, 'daftar_jamaah_paket',  paket_id, 'Daftar Jamaah Paket', true);
                                 window.open(baseUrl + "Kwitansi/", "_blank");
                              }
                           });
                        } else{
                           frown_alert(error_msg);
                           return false;
                        }
                     }
                  }
               }
            });
         },[{paket_id:paket_id, jamaah_id:jamaah_id, paket_transaction_id:paket_transaction_id}]
      );

}

function getTipeHargaPaket( paket_id_now ){
   var paket_tujuan = $('#paket_tujuan :selected').val();
   var tipe_aksi = $('#tipe_aksi :selected').val();
   var html = '';
   if( paket_tujuan != 0 ) {
      if( tipe_aksi == 0 ) {
         // get tipe paket
         ajax_x_t2(
            baseUrl + "Trans_paket/getTipePaket", function(e) {
               html = '<option value="0">Pilih Tipe Paket Tujuan</option>';
               for( x in e['data'] ) {
                  html += `<option value="${e['data'][x]['id']}">${e['data'][x]['paket_type_name']} -- ${': ' + kurs + ' ' + numberFormat( e['data'][x]['price'] )}</option>`;
               }
               $('#tipe_paket_no_reg_tujuan').html(html);
            },[{paket_id_now: paket_id_now, paket_id: paket_tujuan}]
         );
      }else if ( tipe_aksi == 1 ) {
         // get register id
         ajax_x_t2(
           baseUrl + "Trans_paket/getPaketNoRegister", function(e) {

             html = '<option value="0">Pilih No Registrasi Paket Tujuan</option>';
             for( x in e['data'] ) {
                html += `<option value="${e['data'][x]['no_register']}">${e['data'][x]['no_register']} -- ${': ' + kurs + ' ' + numberFormat( e['data'][x]['price'] )}</option>`;
             }
             $('#tipe_paket_no_reg_tujuan').html(html);

           },[{paket_id_now: paket_id_now, paket_id: paket_tujuan}]
        );
      }
   }else{
      if( tipe_aksi == 0  ){
         html = '<option value="0">Pilih Tipe Paket Tujuan</option>';
         $('#tipe_paket_no_reg_tujuan').html(html);
      }else if ( tipe_aksi == 1  ) {
         html = '<option value="0">Pilih No Register Paket Tujuan</option>';
         $('#tipe_paket_no_reg_tujuan').html(html);
      }
   }
}

function getHargaPaketByTipePaket( paket_id_now, paket_transaction_id_now, jamaah_id ){
   console.log('masuk tipe harga paket By Tipe Paket');
   var tipe_aksi = $('#tipe_aksi').val();
   var biaya_yang_dipindah = $('#biaya_yang_dipindah').val();
   var paket_tujuan = $('#paket_tujuan :selected').val();
   var refund = $('#refund').val();
   var tipe_paket_no_reg_tujuan =  $('#tipe_paket_no_reg_tujuan :selected').val();
   var no_register = '';
   var error = 0;
   var error_msg = '';
   // console.log( '1');
   if( paket_tujuan == 0 ) {
      error = 1;
      error_msg += 'Untuk Melanjutkan, Anda wajib memilih paket tujuan<br>'
   }
   // console.log( '2');
   if( tipe_aksi == 0 ) {
      if( tipe_paket_no_reg_tujuan == 0 ) {
         error = 1;
         error_msg = 'Untuk melanjutkan, Anda wajib memilih tipe paket tujuan';
      }
      // console.log( '3');
   } else {
      if( tipe_paket_no_reg_tujuan == 0 ) {
         error = 1;
         error_msg = 'Untuk melanjutkan, Anda wajib memilih salah satu nomor register tujuan';
      }
      // console.log( '4');
   }
   // console.log( 'error');
   // console.log( error );
   // console.log( error_msg );
   if( error == 0 )
   {
      ajax_x_t2(
         baseUrl + "Trans_paket/getPriceByTipePaketNoReg", function(e) {
            if(e['error'] == false){
               $('#harga_paket_tujuan').val(kurs + ' ' + numberFormat( e['data']['harga_paket_tujuan'] ));
               $('#sisa_pembayaran').val( kurs + ' ' + numberFormat( e['data']['sisa_pembayaran'] ) );
               $('#pembayaran_berlebih').val( kurs + ' ' + numberFormat( e['data']['pembayaran_berlebih'] ) );
               $('#pembayaran_berlebih_val').val( kurs + ' ' + numberFormat( e['data']['pembayaran_berlebih'] ) );
            }else{
               frown_alert(e['error_msg']);
            }
         },[{paket_id_now: paket_id_now,
             paket_id: paket_tujuan,
             tipe_paket_no_reg_tujuan:tipe_paket_no_reg_tujuan,
             biaya_yang_dipindah:biaya_yang_dipindah,
             paket_transaction_id_now: paket_transaction_id_now,
             jamaah_id:jamaah_id,
             refund:refund,
             tipe_aksi:tipe_aksi}]);
   }else{
      frown_alert(error_msg);
   }
}

function deleteItem(e){
   $(e).parent().parent().remove();
}

function addHandOverItem(){
   var html = `<div class="row">
                  <div class="col-sm-11 py-1">
                     <input class="form-control form-control-sm item" type="text" name="item[]" placeholder="Nama Barang" required>
                  </div>
                  <div class="col-sm-1 py-1 text-right">
                     <button class="btn btn-default btn-action" title="Delete" onclick="deleteItem(this)">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </div>
               </div>`;
   $('#areaInput').append(html);
}

function PrintCicilanPaket(id){
   $.confirm({
      columnClass: 'col-5',
      title: 'Cetak Info Cicilan',
      theme: 'material',
      content:`Silahkan pilih item yang ingin dicetak ?.`,
      closeIcon: false,
      buttons: {
         cancel:{
            text: 'Batalkan',
            btnClass: 'btn-default',
            action:  function () {
              return true;
           }
         },
         riwayat:{
            text: 'Cetak Riwayat Cicilan',
            btnClass: 'btn-red',
            action:  function () {
               ajax_x(
                 baseUrl + "Trans_paket/cetakRiwayatCicilan", function(e) {
                    window.open(baseUrl + "Kwitansi/", "_blank");
                 },[{paket_transaction_id: id}]
              );
           }
         },
         skema:{
            text: 'Cetak Skema Cicilan',
            btnClass: 'btn-red',
            action:  function () {
               ajax_x(
                baseUrl + "Trans_paket/cetakSkemaCicilan", function(e) {
                  window.open(baseUrl + "Kwitansi/", "_blank");
                },[{paket_transaction_id: id}]
             );
           }
         },
      }
   });
}

function formPembayaranCicilan( paket_transaction_id, JSONData ){
   var e = JSON.parse(JSONData);
   var form = `<form action="${baseUrl }Trans_paket/pembayaranCicilan" id="form_utama" class="formName ">
                  <input type="hidden" name="paket_transaction_id" id="paket_transaction_id" value="${paket_transaction_id}">
                  <div class="row px-0 mx-0">
                     <label>Riwayat Pembayaran</label>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:20%;">Invoice</th>
                                 <th style="width:20%;">Bayar</th>
                                 <th style="width:20%;">Penyetor/<br>Penerima</th>
                                 <th style="width:20%;">Ket</th>
                                 <th style="width:20%;">Tanggal</th>
                              </tr>
                           </thead>
                           <tbody >`;
                  if( e['data'] != undefined  &&  e['data'].length > 0  ) {
                     for ( x in e['data'] ) {
                        form += `<tr><td>${e['data'][x]['invoice']}</td>
                                     <td>${e['data'][x]['paid']}</td>
                                     <td class="text-left">Penyetor :<br><span class="float-right font-weight-bold">${e['data'][x]['penyetor']}</span><br>Penerima : <br><span class="float-right font-weight-bold">${e['data'][x]['penerima']}</span></td>
                                     <td class="text-capitalize">${e['data'][x]['ket']}</td>
                                     <td>${e['data'][x]['tanggal_transaksi']}</td></tr>`;
                     }
                  } else {
                     form += `<tr><td colspan="5"> Riwayat pembayaran tidak ditemukan. </td></tr>`;
                  }
               form +=    `</tbody>
                        </table>
                     </div>
                     <label>Riwayat Angsuran</label>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:10%;">Term</th>
                                 <th style="width:40%;">Ket</th>
                                 <th style="width:25%;">Bayar</th>
                                 <th style="width:25%;">Sisa</th>
                              </tr>
                           </thead>
                           <tbody >`;
                  if( e['riwayat_angsuran'] != undefined  && e['riwayat_angsuran'].length > 0 ) {
                     for ( y in e['riwayat_angsuran'] ) {
                        form += `<tr><td>#${e['riwayat_angsuran'][y]['term']}</td>
                                     <td class="text-left">${e['riwayat_angsuran'][y]['ket']}</td>
                                     <td>${e['riwayat_angsuran'][y]['bayar']}</td>
                                     <td>${e['riwayat_angsuran'][y]['sisa']}</td></tr>`;
                     }
                  } else {
                     form += `<tr><td colspan="4"> Riwayat Angsuran tidak ditemukan. </td></tr>`;
                  }
               form +=    `</tbody >
                        </table>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Total Harga Paket</label>
                           <input type="text" name="total_harga_paket" id="total_harga_paket" placeholder="Total Harga Paket" class="username form-control form-control-sm" value="${e['total_harga']}" readonly />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Sudah Dibayar</label>
                           <input type="text" name="sudah_dibayar" id="sudah_dibayar" placeholder="Sudah Dibayar" class="username form-control form-control-sm" value="${e['total_bayar'] }" readonly />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Sisa</label>
                           <input type="text" name="sisa" placeholder="Sisa" id="sisaPembayaran" class="username form-control form-control-sm" value="${e['sisa']}" readonly />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Invoice</label>
                           <input type="text" name="invoiceView" placeholder="invoice" id="invoiceView" class="username form-control form-control-sm" value="${e['invoice']}" readonly />
                           <input type="hidden" name="invoice" id="invoice" value="${e['invoice']}" />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Bayar</label>
                           <input type="text" name="bayar" placeholder="Bayar" class="currency fullname form-control form-control-sm" required />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Nama Penyetor</label>
                           <input type="text" name="deposit_name" placeholder="Nama Penyetor" class="nama_penyetor form-control form-control-sm" required />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>HP Penyetor</label>
                           <input type="text" name="hp_deposit" placeholder="Nomor HP Penyetor" class="hp_penyetor form-control form-control-sm" required />
                        </div>
                     </div>
                     <div class="col-8">
                        <div class="form-group">
                           <label>Alamat Penyetor</label>
                           <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3" style="resize: none;"></textarea>
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
   return form;
}

function formSkemaCicilan(paket_transaction_id, JSONData){
   var es = JSON.parse(JSONData);
   var formSkema =  `<form action="${baseUrl }Trans_paket/updateSkemaCicilan" id="form_utama" class="formName ">
                        <input type="hidden" name="paket_transaction_id" id="paket_transaction_id" value="${paket_transaction_id}">
                        <div class="row px-0 mx-0">
                           <div class="col-12" >
                              <div class="row">
                                 <div class="col-3" >
                                    <label>Total Cicilan </label>
                                 </div>
                                 <div class="col-9" >
                                    <label>: ${es['totalCicilanView']}</label>
                                    <input type="hidden" id="totalCicilan" value="${es['totalCicilan']}">
                                 </div>
                              </div>
                           </div>
                           <div class="col-12" >
                              <div class="row">
                                 <div class="col-3" >
                                    <label>Total Amount </label>
                                 </div>
                                 <div class="col-9" >
                                    <label id="totalAmountView">: ${es['totalAmountView']}</label>
                                    <input type="hidden" id="totalAmount" value="${es['totalAmount']}">
                                 </div>
                              </div>
                           </div>
                           <div class="col-12" >
                              <div class="row">
                                 <div class="col-3" >
                                    <label>Tenor Cicilan :</label>
                                 </div>
                                 <div class="col-9" >
                                    <label>: 12 Bulan</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-12" >
                              <table class="table table-hover">
                                 <thead>
                                    <tr>
                                       <th style="width:10%;">Term</th>
                                       <th style="width:40%;">Amount</th>
                                       <th style="width:25%;">Due Date</th>
                                    </tr>
                                 </thead>
                                 <tbody >`;
                     if( es['listSkemaCicilan'] != undefined  ) {
                        for ( y in es['listSkemaCicilan'] ) {
                           formSkema +=  `<tr><td>#${es['listSkemaCicilan'][y]['term']}
                                                <input type="hidden" name="term[]" value="${es['listSkemaCicilan'][y]['term']}">
                                              </td>
                                              <td >
                                                <input type="text" name="amount[]" class="amount form-control form-control-sm currency" value="${es['listSkemaCicilan'][y]['amount']}" onKeyup="sumTotalAmount()">
                                              </td>
                                              <td>
                                                <input type="date" name="duedate[]" class="form-control form-control-sm " value="${es['listSkemaCicilan'][y]['duedate']}" >
                                              </td></tr>`;
                        }
                     } else {
                        formSkema += `<tr><td colspan="4">Skema pembayaran angsuran tidak ditemukan.</td></tr>`;
                     }
                     formSkema +=    `</tbody >
                              </table>
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
   return formSkema;
}

function PembayaranCicilan(paket_id, paket_transaction_id, state){
   ajax_x(
      baseUrl + "Trans_paket/getInfoPembayaranCicilan", function(e) {
            $.confirm({
               columnClass: 'col-8',
               title: 'Form Pembayaran Cicilan',
               theme: 'material',
               content: formPembayaranCicilan(paket_transaction_id, JSON.stringify(e)),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  modifikasiSkema:{
                     text: 'Modifikasi Skema Cicilan',
                     btnClass: 'btn-red',
                     action: function () {
                        ajax_x(
                           baseUrl + "Trans_paket/getInfoSkemaCicilan", function(es) {
                              $.confirm({
                                 columnClass: 'col-5',
                                 title: 'Modifikasi Skema Pembayaran Cicilan',
                                 theme: 'material',
                                 content: formSkemaCicilan(paket_transaction_id, JSON.stringify(es)),
                                 closeIcon: false,
                                 buttons: {
                                    cancel: function () { return true; },
                                    simpan_modifikasi: {
                                       text: 'Simpan Modifikasi',
                                       btnClass: 'btn-blue',
                                       action: function () {
                                          var totalCicilan = $('#totalCicilan').val();
                                          var totalAmount = $('#totalAmount').val();
                                          if( totalAmount ==  totalCicilan ){
                                             ajax_submit_t1("#form_utama", function(e) {
                                                e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                                                if ( e['error'] == true ) {
                                                   return false;
                                                } else {
                                                   if( state == 'perpaket' ){
                                                      navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket');
                                                   }else if ( state == 'allpaket' ) {
                                                      navBtn(this, 'landing_daftarAllTransactionPaket', 'Pembayaran Paket', '')
                                                   }
                                                }
                                             });
                                          }else {
                                             frown_alert('Total amount harus sama besar dengan total cicilan');
                                             return false;
                                          }
                                       }
                                    }
                                 }
                              });
                           },[{paket_transaction_id: paket_transaction_id}]
                        );
                     }
                  },
                  cetakKwitansi: {
                     text: 'Cetak Kwitansi Terakhir',
                     btnClass: 'btn-green',
                     action: function () {
                        ajax_x(
                           baseUrl + "Trans_paket/lastKwitansiCicilan", function(e) {
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           },[{paket_transaction_id: paket_transaction_id}]
                        );
                     }
                  },
                  formSubmit: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              if( state == 'perpaket' ){
                                 navBtnParam(this, 'transaksi_paket', paket_id, 'Transaksi Paket');
                              }else if ( state == 'allpaket' ) {
                                 navBtn(this, 'landing_daftarAllTransactionPaket', 'Pembayaran Paket', '')
                              }
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            });
      },[{paket_transaction_id: paket_transaction_id}]
   );
}

function sumTotalAmount(){
   var amount = new Array();
   var totalAmount = 0;
   if( $('.amount').length > 0 ){
       $('.amount').each(function(index){
           totalAmount = totalAmount + hide_currency($(this).val());
       });
   }
   $('#totalAmountView').html(': ' + kurs + ' ' + numberFormat(totalAmount));
   $('#totalAmount').val(totalAmount);
}


function cetakDatajamaah( jamaah_id , paket_transaction_id, paket_id ){
   ajax_x_t2(
      baseUrl + "Trans_paket/infoTandaTangan", function(e) {
         var form = `<form action="${baseUrl }Trans_paket/cetakDataJamaah" id="form_utama" class="formName ">
                        <input type="hidden" name="jamaah_id" value="${jamaah_id}">
                        <input type="hidden" name="paket_transaction_id" value="${paket_transaction_id}">
                        <div class="row px-0 mx-0">
                           <div class="col-12" >
                              <div class="form-group">
                                 <label>Tanda tangan petugas</label>
                                 <select class="form-control form-control-sm" name="tanda_tangan" id="tanda_tangan" >
                                    <option value="pilih_petugas">Pilih petugas yang tanda tangan</option>`;
                              for(  x in e['tanda_tangan'] ){
                                 form += `<option value="${e['tanda_tangan'][x]['id']}">${e['tanda_tangan'][x]['fullname']} (${e['tanda_tangan'][x]['jabatan']})</option>`;
                              }
                        form += `</select>
                              </div>
                           </div>
                        </div>
                     </form>`;
         $.confirm({
            title: 'Form Tanda Tangan',
            theme: 'material',
            columnClass: 'col-4',
            content: form,
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {

                     var tanda_tangan =  $('#tanda_tangan :selected').val();
                     var error = 0;
                     var error_msg = '';

                     if( tanda_tangan == 'pilih_petugas' ) {
                        error = 1;
                        error_msg += 'Untuk Melanjutkan, Anda wajib memilih salah satu petugas yang menandatangan<br>'
                     }

                     if( error == 1 ) {
                        frown_alert(error_msg);
                        return false;
                     }else{
                        ajax_submit_t1("#form_utama", function(e) {
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              navBtnParam(this, 'daftar_jamaah_paket',  paket_id, 'Daftar Jamaah Paket', true);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            }
         });
      },[]
   );
}

let trTable = ( label, value, icon ) => {
   return  `<tr class="py-2">
               <td style="width:10%"><i class="${icon}"></i></td>
               <td style="width:55%">${label}</td>
               <td style="width:35%"><b>${value}</b></td>
            </tr>`;
}

let cardItemPaket = ( JSONdata ) => {
   data = JSON.parse(JSONdata);
   var price = '<ul class="pl-0 list" style="list-style-type: none;">';
   for ( x in data['price'] ) {
      price += '<li>' + kurs + ' ' + data['price'][x] + '</li>';
   }
   price += '</ul>';
   return  `<div class="item item-slider">
               <div class="card" style="width: width:12rem;min-height:23rem;">
                  <img class="card-img-top" src="${baseUrl}image/paket/${data['photo']}" alt="Card image cap">
                  <div class="card-body px-2 py-1">
                     <h5 class="card-title mb-2 mt-1" style="text-transform: uppercase;">${data['paket_name']}<br>${ data['status_paket'] == 'tutup' ? '<span style="color: #ff8c8c;font-size: 10px;font-weight: bold;">(Paket ditutup)</span>' : '' }</h5>
                     <table class=" mt-1 mb-2 details table table-borderless">
                        <tbody>
                           ${trTable('Kode Paket', data['kode'], 'fa fa-qrcode')}
                           ${trTable('Jdwl. Berangkat', data['departure_date'], 'fa fa-calendar')}
                           ${trTable('Durasi Perjalanan', data['duration_trip'] + ' Hari', 'far fa-clock')}
                           ${trTable('Total Jamaah', data['totalJamaah'] + ' Orang', 'far fa-user')}
                           ${trTable('Harga', price, 'fas fa-money-bill-wave')}
                        </tbody>
                     </table>
                     <a href="javascript:void(0)" onClick="beli_paket(${data['id']})" class="btn btn-default gobtn">Beli Paket</a>
                  </div>
               </div>
            </div>`;
}

function navigationButton(label, att, icon, classes){
   // Webcam.reset();
   return  `<button type="button" class="btn btn-default btn-sm navbtn  ${classes}" ${att}>
               <i class="${icon}" style="font-size: 11px;"></i>
					<span class="d-none d-sm-none d-md-none d-lg-inline-block d-md-none">${label}</span>
            </button>`;
}

function navBtnParam(e, menu, param, titleMenu, state){
	if( state == undefined ){
		$('.navbtn').removeClass( "active" );
		$(e).addClass("active");
	}
	$('#showPosition').hide().html(titleMenu).fadeIn(500);
	window[menu](param);
	Webcam.reset();
}
