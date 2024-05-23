function rekapitulasi_tiket_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarRekapitulasi">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="rekap_tiket()">
                        <i class="fas fa-clipboard-list"></i> Rekapitulasi Tiket
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_rekapitulasi( 20)" id="searchAllDaftarRekapitulasi" name="searchAllDaftarRekapitulasi" placeholder="Nomor Rekapitulasi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_rekapitulasi( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:15%;">Nomor Rekapitulasi</th>
                              <th style="width:45%;">Info Rekapitulasi</th>
                              <th style="width:15%;">Total</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_rekapitulasi">
                           <tr>
                              <td colspan="5">Daftar transaksi passport tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_rekapitulasi"></div>
                  </div>
               </div>
            </div>`;
}

function rekapitulasi_tiket_getData(){
   get_daftar_rekapitulasi(20);
}

function get_daftar_rekapitulasi(perpage){
   get_data( perpage,
             { url : 'Rekapitulasi/daftar_rekapitulasi',
               pagination_id: 'pagination_daftar_rekapitulasi',
               bodyTable_id: 'bodyTable_daftar_rekapitulasi',
               fn: 'ListDaftarRekapitulasi',
               warning_text: '<td colspan="6">Daftar rekapitulasi tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarRekapitulasi').val() } } );
}

function ListDaftarRekapitulasi(JSONData){
   var json = JSON.parse(JSONData);
   var detail =  `<table class="table table-hover">
                     <tbody>`;
         for( x in json.detail ) {
            detail +=  `<tr>
                           <td class="text-left py-0 pt-3" colspan="3" style="font-weight:bold;border:none;">${json.detail[x]['no_register']}</td>
                        </tr>`;
            var detail_transaksi_tiket = json.detail[x]['detail_transaksi_tiket'];
            for ( y in detail_transaksi_tiket){
               detail +=  `<tr>
                              <td class="text-right p-0 pt-1" style="width:5%;border:none;">-</td>
                              <td class="text-left py-0 pt-1" style="width:40%;border:none;">KODE BOOKING</td>
                              <td class="text-left py-0 pt-1" style="width:55%;border:none;font-weight:bold;">${detail_transaksi_tiket[y]['kode_booking']}</td>
                           </tr>
                           <tr>
                              <td class="text-right p-0" style="width:5%;border:none;">-</td>
                              <td class="text-left py-0" style="width:40%;border:none;">NAMA MASKAPAI</td>
                              <td class="text-left py-0" style="width:55%;border:none;">${detail_transaksi_tiket[y]['nama_maskapai']}</td>
                           </tr>
                           <tr>
                              <td class="text-right p-0" style="border:none;">-</td>
                              <td class="text-left py-0" style="border:none;">HARGA KOSTUMER</td>
                              <td class="text-left py-0" style="border:none;">Rp ${numberFormat(detail_transaksi_tiket[y]['harga_kostumer'])} (Pax : ${detail_transaksi_tiket[y]['pax']}X)</td>
                           </tr>
                           <tr>
                              <td class="text-right p-0 pb-1" style="border:none;">-</td>
                              <td class="text-left py-0 pb-1" >TOTAL HARGA</td>
                              <td class="text-left py-0 pb-1" >Rp ${numberFormat(detail_transaksi_tiket[y]['harga_kostumer'] * detail_transaksi_tiket[y]['pax'])}</td>
                           </tr>`;
            }
            detail +=  `<tr style="border: 1px solid #adadad;">
                           <td class="text-left py-1" colspan="2" style="font-weight:bold;border: none;background-color: #f7f7f7;">TOTAL</td>
                           <td class="text-left py-1" colspan="1" style="font-weight:bold;border:none;background-color: #e6e6e6;">Rp ${numberFormat(json.detail[x]['total_transaksi'])}</td>
                        </tr>`;
         }
            detail +=  `
                     <tbody>
                  </table>`;

   var html = `<tr>
                  <td>${json.recapitulation_number}</td>
                  <td>${detail}</td>
                  <td>Rp ${numberFormat(json.total)}</td>
                  <td>${json.tanggal_transaksi}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Rekapitulasi"
                        onclick="cetak_kwitansi_rekap('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Transaksi Rekapitulasi"
                        onclick="delete_trans_rekap('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function cetak_kwitansi_rekap(id){
   ajax_x(
      baseUrl + "Rekapitulasi/cetak_rekapitulasi", function(e) {
         if( e['error'] == false ){
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'green',
         });
      },[{id:id}]
   );
}

function delete_trans_rekap(id){
   ajax_x(
      baseUrl + "Rekapitulasi/delete_rekapitulasi", function(e) {
         if( e['error'] == false ){
             get_daftar_rekapitulasi(20);
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'green',
         });
      },[{id:id}]
   );
}

function rekap_tiket(){
   ajax_x(
      baseUrl + "Rekapitulasi/get_info_list_tiket", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-9',
               title: 'Rekap Tiket',
               theme: 'material',
               content: formaddupdate_rekapitulasi(e['invoice']),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Rekap Tiket',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           $.alert({
                              title: 'Peringatan',
                              content: e['error_msg'],
                              type: e['error'] == true ? 'red' :'green'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_rekapitulasi(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            });
            get_list_tiket(5);
         }else{
            $.alert({
               title: 'Peringatan',
               content: e['error_msg'],
               type: e['error'] == true ? 'red' :'green'
            });
         }
      },[]
   );
}

function get_list_tiket(perpage){
   $('#list_tiket_hidden').val('{}');
   get_data( perpage,
             { url : 'Rekapitulasi/daftar_tiket',
               pagination_id: 'pagination_daftar_tiket',
               bodyTable_id: 'bodyTable_daftar_tiket',
               fn: 'ListDaftarTiket',
               warning_text: '<td colspan="4">Daftar tiket tidak ditemukan</td>',
               param : { search : $('#searchListTiket').val(),
                         listRekap : $('#list_rekap').val() } } );
}

function ListDaftarTiket(JSONData) {

   var list_tiket_hidden = JSON.parse($('#list_tiket_hidden').val());
       list_tiket_hidden[Object.keys(list_tiket_hidden).length] = JSON.parse(JSONData);
       console.log(list_tiket_hidden);
       $('#list_tiket_hidden').val(JSON.stringify(list_tiket_hidden));

   var json = JSON.parse(JSONData);
   var detail =  `<table class="table table-hover">
                     <tbody>`;
      for( x in json.detail ) {
         detail +=  `<tr>
                        <td class="text-left py-0" colspan="3" style="font-weight:bold;border:none;">${json.detail[x]['code_booking']}</td>
                     </tr>
                     <tr>
                        <td class="text-right p-0" style="width:5%;border:none;">-</td>
                        <td class="text-left py-0" style="width:40%;border:none;">NAMA MASKAPAI</td>
                        <td class="text-left py-0" style="width:55%;border:none;">${json.detail[x]['airlines_name']}</td>
                     </tr>
                     <tr>
                        <td class="text-right p-0" style="border:none;">-</td>
                        <td class="text-left py-0" style="border:none;">HARGA KOSTUMER</td>
                        <td class="text-left py-0" style="border:none;">Rp ${numberFormat(json.detail[x]['costumer_price'])}</td>
                     </tr>`;
      }
         detail +=  `<tr style="border: 1px solid #adadad;">
                        <td class="text-left py-1" colspan="2" style="font-weight:bold;border: none;background-color: #f7f7f7;">TOTAL</td>
                        <td class="text-left py-1" colspan="1" style="font-weight:bold;border:none;background-color: #e6e6e6;">Rp ${numberFormat(json.total_transaksi)}</td>
                     </tr>
                     <tbody>
                  </table>`;

   var html = `<tr>
                  <td>${json.no_register}</td>
                  <td>${detail}</td>
                  <td>${json.tanggal_transaksi}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Tambahkan ke dalam daftar rekap"
                        onclick="add_list_rekap('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-plus" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function formaddupdate_rekapitulasi(invoice, JSONValue){
   var daftar_rekap = '';
   var nama_penerima = '';
   var alamat_penerima = '';
   var detail_rekap = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      nama_penerima = value.receiver;
      alamat_penerima = value.receiver_address;
      for( x in value.detail ) {
         detail_rekap +=  `<tr>
                              <td>${value.detail[x]['nomor_register']}</td>
                              <td>Rp ${numberFormat(value.detail[x]['total'])}</td>
                              <td>
                                 <button class="btn btn-default btn-action" title="Delete" onclick="delete_this(this)">
                                    <i class="fas fa-times" style="font-size: 11px;"></i>
                                 </button>
                              </td>
                           </tr>`;
      }
   }else{
      detail_rekap +=  `<tr>
                           <td colspan="3"> Data rekap tiket tidak ditemukan. </td>
                        </tr>`;
   }
   var html = `<form action="${baseUrl}Rekapitulasi/proses_addupdate_rekapitulasi" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row ">
                           <div class="col-6 text-left">
                              <label><span class="float-left" style="color:red">(*) Wajib diisi</span></label>
                           </div>
                           <div class="col-6 text-right">
                              <label class="float-right">INVOICE :<span style="color:red"> #${invoice}</span></label>
                              <input type="hidden" name="invoice" value="${invoice}">
                              <input type="hidden" id="list_rekap" value="{}">
                              <input type="hidden" id="list_tiket_hidden" value="{}">
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-8"></div>
                           <div class="col-4">
                              <div class="input-group">
                                 <input class="form-control form-control-sm" type="text" id="searchListTiket" name="searchListTiket" placeholder="Nomor Registrasi Tiket" style="font-size: 12px;">
                                 <div class="input-group-append">
                                    <button class="btn btn-default" type="button" onclick="get_list_tiket(5)">
                                       <i class="fas fa-search"></i> Cari
                                    </button>
                                 </div>
                              </div>
                           </div>
                           <div class="col-12 mt-3">
                              <table class="table table-hover tablebuka">
                                 <thead>
                                    <tr>
                                       <th style="width:20%;">Nomor Registrasi</th>
                                       <th style="width:50%;">Info Detail Tiket</th>
                                       <th style="width:20%;">Tanggal Transaksi</th>
                                       <th style="width:10%;">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody id="bodyTable_daftar_tiket">
                                    <tr>
                                       <td colspan="4">Daftar tiket tidak ditemukan.</td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                           <div class="col-lg-12 mb-4" >
                              <div class="row" id="pagination_daftar_tiket"></div>
                           </div>
                           <div class="col-6">
                              <label style="font-size: 12px;">Info Penerima</label>
                              <div class="row">
                                 <div class="col-8">
                                    <div class="form-group text-left mb-2">
                                       <input type="text" class="form-control form-control-sm" name="nama" value="" placeholder="Nama Penerima">
                                       ${text_helper('Nama Penerima. <span class="float-right" style="color:red">(*)</span>')}
                                    </div>
                                 </div>
                                 <div class="w-100"></div>
                                 <div class="col-12">
                                    <div class="form-group text-left mb-2">
                                       <textarea class="form-control" name="address" rows="3" style="resize:none;" placeholder="Alamat Penerima."></textarea>
                                       ${text_helper('Alamat Penerima. <span class="float-right" style="color:red">(*)</span>')}
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-6 text-right">
                              <label style="font-size: 12px;">Daftar Tiket Cetak Rekapitulasi</label>
                              <div class="row">
                                 <div class="col-12">
                                    <table class="table table-hover tablebuka">
                                       <thead>
                                          <tr>
                                             <th style="width:40%;">Nomor Registrasi</th>
                                             <th style="width:40%;">Total Harga</th>
                                             <th style="width:20%;">Aksi</th>
                                          </tr>
                                       </thead>
                                       <tbody id="bodyTable_daftar_tiket_rekap">
                                          ${detail_rekap}
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function add_list_rekap(id){
   var list_rekap = JSON.parse($('#list_rekap').val());
       list_rekap[Object.keys(list_rekap).length] = id;
       $('#list_rekap').val(JSON.stringify(list_rekap));

   var append = '';
   var list_tiket_hidden = JSON.parse($('#list_tiket_hidden').val());
   for( x in list_tiket_hidden ) {
      if( list_tiket_hidden[x]['id'] == id ) {
         append +=  `<tr class="DaftarRekap">
                        <td>
                           <input type="hidden" name="tiket_transaction_id[]" value="${id}">
                           ${list_tiket_hidden[x]['no_register']}
                        </td>
                        <td>Rp ${numberFormat(list_tiket_hidden[x]['total_transaksi'])}</td>
                        <td>
                           <button type="button" class="btn btn-default btn-action" title="Hapus dari daftar rekap" onclick="hapus_dari_rekap_this(this,${id})" style="margin:.15rem .1rem  !important">
                               <i class="fas fa-times" style="font-size: 11px;"></i>
                           </button>
                        </td>
                     </tr>`;
      }
   }

   if( $('.DaftarRekap').length > 0  ){
      $('#bodyTable_daftar_tiket_rekap').append(append);
   }else{
      $('#bodyTable_daftar_tiket_rekap').html(append);
   }

   get_list_tiket(5);
}

function hapus_dari_rekap_this(e, id){
   $(e).parent().parent().remove();
   var newListRekap = {};
   var list_rekap = JSON.parse($('#list_rekap').val());
   var deleted = '';
   for( x in  list_rekap ) {
      if( list_rekap[x] == id ) {
         deleted = x;
      }
   }
   delete list_rekap[deleted];
   if( Object.keys(list_rekap).length == 0 ) {
      $('#bodyTable_daftar_tiket_rekap').html(`<tr><td colspan="3">Data rekap tiket tidak ditemukan.</td></tr>`);
   }
   $('#list_rekap').val(JSON.stringify(list_rekap));
   get_list_tiket(5);
}
