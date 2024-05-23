function fee_agen_Pages(){
   return   `<div class="col-6 col-lg-8">
                  <label class="float-right py-2 my-3">Filter :</label>
              </div>
              <div class="col-6 col-lg-4 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="fee_agen_getData()" id="searchFeeAgen" name="searchFeeAgen" placeholder="Nomor Identitas Agen" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="fee_agen_getData()">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
              </div>
              <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:42%;">Info Member</th>
                           <th style="width:42%;">Info Keagenan</th>
                           <th style="width:16%;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable_fee_agen">
                        <tr>
                           <td colspan="4">Daftar agen tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
              </div>
              <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination_fee_agen"></div>
              </div>`;
}

function fee_agen_getData(){
   get_fee_agen(20);
}

function get_fee_agen(perpage){
   get_data( perpage,
             { url : 'Fee_agen/daftar_agen',
               pagination_id: 'pagination_fee_agen',
               bodyTable_id: 'bodyTable_fee_agen',
               fn: 'ListFeeAgen',
               warning_text: '<td colspan="7">Daftar agen tidak ditemukan</td>',
               param : { search : $('#searchFeeAgen').val() } } );
}

function ListFeeAgen(JSONData){
   var json = JSON.parse( JSONData );

   var info_member =`<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;">NAMA AGEN</td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;">${json.fullname}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >NOMOR IDENTITAS</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.identity_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >JENIS KELAMIN</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.gender == 0 ? 'Laki-laki' : 'Perempuan'}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >NOMOR WHATSAPP</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.nomor_whatsapp}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >ALAMAT</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.address}</td>
                           </tr>
                        </tbody>
                     </table>`;

   var  info_akun = `<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;">LEVEL AGEN</td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;">${json.level_agen}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >FEE BELUM DIBAYAR</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >Rp ${numberFormat(json.unpaid_fee)}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >FEE YANG SUDAH DIBAYAR</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >Rp ${numberFormat(json.paid_fee)}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >JUMLAH TRANSAKSI</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.total_transaksi}</td>
                           </tr>
                        </tbody>
                     </table>`;

   return  `<tr>
               <td>${info_member}</td>
               <td>${info_akun}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Tambah Komisi Keagenan"
                     onClick="tambah_komisi_agen('${json["id"]}')">
                      <i class="fas fa-money-bill-wave" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Riwayat Tambah Komisi Keagenan"
                     onClick="riwayat_tambah_komisi_agen('${json["id"]}')">
                      <i class="fas fa-list-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Bayar Fee Keagenan"
                     onClick="bayar_fee_agen('${json["id"]}')">
                      <i class="fas fa-money-bill-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Riwayat Pembayaran Fee Keagenan"
                     onClick="riwayat_pembayaran_fee_agen('${json["id"]}')">
                      <i class="fas fa-list" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}


function tambah_komisi_agen(id){
   ajax_x(
      baseUrl + "Fee_agen/get_info_tambah_komisi", function(e) {
         if( e['error'] == false ){

            $.confirm({
               columnClass: 'col-6',
               title: 'Form Komisi Agen',
               theme: 'material',
               content: formTambahKomisiAgen(JSON.stringify(e['data'])),
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
                              fee_agen_getData();
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

function formTambahKomisiAgen(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Fee_agen/tambah_fee_agen" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <input type="hidden" name="id" value="${json.id}" >
                                 <label>Nama Agen</label>
                                 <input type="text" value="${json.fullname}" class="form-control form-control-sm" placeholder="Nama Agen" readonly/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group mb-2">
                                 <label>Nomor Identitas Agen</label>
                                 <input type="text" value="${json.identity_number}" class="form-control form-control-sm" placeholder="Nomor Identitas Agen" readonly/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group mb-2">
                                 <label>Level Agen</label>
                                 <input type="text" value="${json.level_keagenan}" class="form-control form-control-sm" placeholder="Level Keagenan" readonly/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group mb-2">
                                 <label>Komisi</label>
                                 <input type="text" name="komisi" class="form-control currency form-control-sm" placeholder="Komisi Keagenan"/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group mb-2">
                                 <label>Info</label>
                                 <textarea class="form-control form-control-sm" name="info" rows="5" style="resize: none;"></textarea>
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


function riwayat_komisi_agen_Pages(id){
   return   `<div class="col-4 col-lg-4 my-3 py-1">
               <button type="button" class="btn btn-default" title="Daftar Fee Agen" onclick="fee_agen()">
                  <i class="fas fa-arrow-left"></i> Kembali ke Fee Agen
               </button>

             </div>
             <div class="col-4 col-lg-4 my-3 py-1 pt-2">
               <span><b>RIWAYAT PENAMBAHAN FEE / KOMISI AGEN</b></span>
               <label class="float-right">Filter :</label>
             </div>
             <div class="col-4 col-lg-4 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="riwayat_tambah_komisi_agen_getData(${id})" id="searchRiwayatKomisiAgen" name="searchRiwayatKomisiAgen" placeholder="Nomor Transaksi" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="riwayat_tambah_komisi_agen_getData(${id})">
                           <i class="fas fa-search"></i> Cari
                        </button>

                     </div>
                  </div>
             </div>
             <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:15%;">Nama Agen</th>
                           <th style="width:25%;">Info Komisi</th>
                           <th style="width:30%;">Info Paket</th>
                           <th style="width:10%;">Status</th>
                           <th style="width:15%;">Tanggal Transaksi</th>
                           <th style="width:5%;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable_riwayat_komisi_agen">
                        <tr>
                           <td colspan="6">Daftar riwayat komisi agen tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
             </div>
             <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination_riwayat_komisi_agen"></div>
             </div>`;
}

function riwayat_tambah_komisi_agen_getData(id){
   get_riwayat_tambah_komisi_agen(20, id);
}

function get_riwayat_tambah_komisi_agen(perpage, id){
   get_data( perpage,
             { url : 'Fee_agen/riwayat_komisi_agen',
               pagination_id: 'pagination_riwayat_komisi_agen',
               bodyTable_id: 'bodyTable_riwayat_komisi_agen',
               fn: 'ListRiwayatKomisiAgen',
               warning_text: '<td colspan="6">Daftar riwayat komisi agen tidak ditemukan</td>',
               param : { search : $('#searchRiwayatKomisiAgen').val(), id : id  } } );
}

function ListRiwayatKomisiAgen(JSONData){
   var json = JSON.parse(JSONData);
   var info_paket = ``;
   if( typeof( json.paket_info ) == 'object' && Object.keys(json.paket_info).length > 0 ){
      info_paket += `<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left" >NAMA PAKET</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.info}</td>
                           </tr>
                           <tr>
                              <td class="text-left" style="width:40%;">NO TRANSAKSI JAMAAH</td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;">${json.transaction_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >NAMA JAMAAH</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >Rp ${numberFormat(json.fee)}</td>
                           </tr>
                        </tbody>
                     </table>`;
   }else{
      info_paket += `Info paket tidak ditemukan.`;
   }
   var  info_komisi =  `<table class="table table-hover">
                           <tbody>
                              <tr>
                                 <td class="text-left" style="width:40%;">NO. TRANS</td>
                                 <td class="px-1" style="width:1%;">:</td>
                                 <td class="text-left" style="width:59%;"><b>${json.transaction_number}</b></td>
                              </tr>
                              <tr>
                                 <td class="text-left" >KOMISI</td>
                                 <td class="px-1">:</td>
                                 <td class="text-left" style="color:#dc3545!important;background-color: #c9ccd7;"><b>Rp ${numberFormat(json.fee)}</b></td>
                              </tr>
                              <tr>
                                 <td class="text-left" >INFO</td>
                                 <td class="px-1">:</td>
                                 <td class="text-left" >${json.info}</td>
                              </tr>
                           </tbody>
                        </table>`;
   return  `<tr>
               <td>${json.fullname}<br> ${json.identity_number}</td>
               <td>${info_komisi}</td>
               <td>${info_paket}</td>
               <td>${json.status_fee == 'lunas' ? `<span style="color:green"><b>Lunas</b></span>` : `<span style="color:orange"><b>Belum Lunas</b></span>`}</td>
               <td>${json.tanggal_transaksi}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Delete Riwayat Komisi Agen"
                     onClick="delete_riwayat_komisi_agen('${json["id"]}')">
                     <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function delete_riwayat_komisi_agen(id){
   ajax_x(
      baseUrl + "Fee_agen/delete_riwayat_komisi_agen", function(e) {
         if( e['error'] == false ){
            smile_alert(e['error_msg']);
            riwayat_tambah_komisi_agen(e['agen_id']);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function riwayat_tambah_komisi_agen(id){
   $(`#content_fee_agen`).html(riwayat_komisi_agen_Pages(id));
   riwayat_tambah_komisi_agen_getData(id);
}

function fee_agen(){
   $(`#content_fee_agen`).html(fee_agen_Pages());
   fee_agen_getData();
}

function HitungTotalBayar(){
   var total_bayar = 0;
   if( $('.bayar').length > 0 ){
      $('.bayar').each(function(index){
         total_bayar = total_bayar + hide_currency($(this).val());
      });
   }
   $('#total_bayar').html('Rp ' + numberFormat(total_bayar));
}

function formPembayaranFeeKomisiAgen(agen_id, JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Fee_agen/bayar_fee_keaganan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-6 px-0 py-2 text-left">
                         <span><b>${json.agen_name} (${json.agen_identity})</b></span>
                     </div>
                     <div class="col-6 px-0 py-2 text-right">
                         <span><b>INVOICE : #${json.invoice}</b></span>
                         <input type="hidden" value="${json.invoice}" name="invoice">
                         <input type="hidden" value="${agen_id}" name="id">
                     </div>
                     <div class="col-12 px-0">
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:5%;">#</th>
                                 <th style="width:10%;">No. Trans</th>
                                 <th style="width:20%;">Nama Paket</th>
                                 <th style="width:20%;">Nama Jamaah</th>
                                 <th style="width:15%;">Fee/Komisi</th>
                                 <th style="width:15%;">Sudah Bayar</th>
                                 <th style="width:15%;">Pembayaran</th>
                              </tr>
                           </thead>
                           <tbody>`;
                  var i = 1;
                  var total_fee = 0;
                  var total_sudah_bayar = 0;
                  for( x in json.info_pembayaran ) {
                     html += `<tr>
                                 <td class="align-middle">${i}</td>
                                 <td class="align-middle">${json.info_pembayaran[x].transaction_number}</td>
                                 <td class="align-middle">${json.info_pembayaran[x].paket_name}</td>
                                 <td class="align-middle">${json.info_pembayaran[x].jamaah}</td>
                                 <td class="align-middle">Rp ${numberFormat(json.info_pembayaran[x].fee)}</td>
                                 <td class="align-middle">Rp ${numberFormat(json.info_pembayaran[x].sudah_bayar)}</td>
                                 <td class="align-middle">
                                    <input type="text" onkeyup="HitungTotalBayar()" name="bayar[${json.info_pembayaran[x].id}]" class="bayar form-control currency form-control-sm" placeholder="Bayar">
                                 </td>
                              </tr>`;
                     total_fee = total_fee + parseInt(json.info_pembayaran[x].fee);
                     total_sudah_bayar = total_sudah_bayar + parseInt(json.info_pembayaran[x].sudah_bayar);
                     i++;
                  }
                  html +=    `<tr>
                                 <td class="text-right align-middle py-3" colspan="4"><b>TOTAL</b></td>
                                 <td class="align-middle">Rp ${numberFormat(total_fee)}</td>
                                 <td class="align-middle">Rp ${numberFormat(total_sudah_bayar)}</td>
                                 <td class="align-middle" id="total_bayar">Rp 0</td>
                              </tr>
                           </tbody>
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
   return html;
}

function bayar_fee_agen(id){
   ajax_x(
      baseUrl + "Fee_agen/get_info_pembayaran_fee_komisi_agen", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-9',
               title: 'Form Pembayaran Fee / Komisi Agen',
               theme: 'material',
               content: formPembayaranFeeKomisiAgen(id, JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  bayar_fee: {
                     text: 'Bayar Fee',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              window.open(baseUrl + "Kwitansi/", "_blank");
                              fee_agen_getData();
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

function riwayat_pembayaran_fee_agen(id){
   $(`#content_fee_agen`).html(riwayat_pembayaran_fee_agen_Pages(id));
   riwayat_pembayaran_fee_agen_getData(id);
}

function riwayat_pembayaran_fee_agen_Pages(id){
   return   `<div class="col-4 col-lg-4 my-3 py-1">
               <button type="button" class="btn btn-default" title="Daftar Fee Agen" onclick="fee_agen()">
                  <i class="fas fa-arrow-left"></i> Kembali ke Fee Agen
               </button>
             </div>
             <div class="col-4 col-lg-4 my-3 py-1 pt-2">
               <span><b>RIWAYAT PEMBAYARAN FEE / KOMISI AGEN</b></span>
               <label class="float-right">Filter :</label>
             </div>
             <div class="col-4 col-lg-4 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="riwayat_pembayaran_fee_agen_getData(${id})" id="searchRiwayatPembayaranFeeAgen" name="searchRiwayatPembayaranFeeAgen" placeholder="Nomor Transaksi" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="riwayat_pembayaran_fee_agen_getData(${id})">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
             </div>
             <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:3%;">#</th>
                           <th style="width:10%;">Invoice</th>
                           <th style="width:67%;">Info Transaksi</th>
                           <th style="width:10%;">Tanggal Transaksi</th>
                           <th style="width:10%;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable_riwayat_pembayaran_fee_agen">
                        <tr>
                           <td colspan="4">Daftar riwayat pembayaran fee agen tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
             </div>
             <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination_riwayat_pembayaran_fee_agen"></div>
             </div>`;
}

function riwayat_pembayaran_fee_agen_getData(id){
   get_riwayat_pembayaran_fee_agen(20, id);
}

function get_riwayat_pembayaran_fee_agen(perpage, id){
   get_data( perpage,
             { url : 'Fee_agen/riwayat_pembayaran_fee_agen',
               pagination_id: 'pagination_riwayat_pembayaran_fee_agen',
               bodyTable_id: 'bodyTable_riwayat_pembayaran_fee_agen',
               fn: 'ListRiwayatPembayaranFeeAgen',
               warning_text: '<td colspan="6">Daftar riwayat pembayaran fee agen tidak ditemukan</td>',
               param : { search : $('#searchRiwayatPembayaranFeeAgen').val(), id : id  } } );
}

function ListRiwayatPembayaranFeeAgen(JSONData){
   var json = JSON.parse(JSONData);
   var  detail =  `<table class="table table-hover">
                     <thead>
                        <tr>
                           <th style="width:15%;">No Transaksi</th>
                           <th style="width:20%;">Fee</th>
                           <th style="width:20%;">Bayar</th>
                           <th style="width:25%;">Pemohon</th>
                           <th style="width:20%;">Petugas</th>
                        </tr>
                     </thead>
                     <tbody>`;
      var total = 0;
         for( x in json.detail ) {
            detail +=   `<tr>
                           <td>${json.detail[x].transaction_number}</td>
                           <td>Rp ${numberFormat(json.detail[x].fee)}</td>
                           <td>Rp ${numberFormat(json.detail[x].biaya)}</td>
                           <td>${json.detail[x].applicant_name}<br>( ${json.detail[x].applicant_identity} )</td>
                           <td>${json.detail[x].receiver}</td>
                         </tr>`;
            total = total + parseInt(json.detail[x].biaya);
         }
      detail +=     `</tbody>
                  </table>`;

   return  `<tr>
               <td>${json.no}</td>
               <td>${json.invoice}<br> Total : <b>Rp ${numberFormat(total)}</b></td>
               <td>
                  <label class="float-left">Detail Transaksi</label>
                  ${detail}
               </td>
               <td>${json.date_transaction}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Riwayat Pembayaran Fee Agen"
                    onClick="cetak_riwayat_pembayaran_fee_agen('${json.invoice}')">
                    <i class="fas fa-print" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Riwayat Pembayaran Fee Agen"
                     onClick="delete_riwayat_pembayaran_fee_agen('${json.agen_id}', '${json.invoice}')">
                     <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function delete_riwayat_pembayaran_fee_agen( id, invoice ){
   ajax_x(
      baseUrl + "Fee_agen/delete_riwayat_pembayaran_fee_agen", function(e) {
         if( e['error'] == false ){
            riwayat_pembayaran_fee_agen_getData(id)
         }else{
            frown_alert(e['error_msg']);
         }
      },[{invoice:invoice}]
   );
}

function cetak_riwayat_pembayaran_fee_agen(invoice){
   ajax_x(
      baseUrl + "Fee_agen/cetak_invoice_riwayat_pembayaran_fee_agen", function(e) {
         if( e['error'] == true ){
            frown_alert(e['error_msg']);
         }else{
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{invoice:invoice}]
   );
}
