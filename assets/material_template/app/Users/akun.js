function akun_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAkun">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="close_book()" style="background-color: #ff8d8d !important;color: white !important;">
                        <i class="fas fa-book"></i> Tutup Buku
                     </button>
                     <button class="btn btn-default" type="button" onclick="reopen_book()">
                        <i class="fas fa-book"></i> Kembali Ke Buku Sebelumnya
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" id="filter">
                          <option value="0" >Pilih Semua</option>
                          <option value="10000">10000 | Asset</option>
                          <option value="20000">20000 | Kewajiban</option>
                          <option value="30000">30000 | Ekuitas</option>
                          <option value="40000">40000 | Pendapatan</option>
                          <option value="50000">50000 | Biaya Penjualan</option>
                          <option value="60000">60000 | Pengeluaran</option>
                        </select>
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_akun(1000)">
                              <i class="fas fa-filter"></i> Filter Akun
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;" colspan="2">Nomor</th>
                              <th style="width:35%;">Nama Akun</th>
                              <th style="width:15%;">Type</th>
                              <th style="width:15%;">Saldo Awal</th>
                              <th style="width:15%;">Saldo Akhir</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_akun">
                           <tr>
                              <td colspan="7">Daftar Akun tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_akun"></div>
                  </div>
               </div>
            </div>`;
}

function formCloseBook(){
   var form = `<form action="${baseUrl }Akun/close_book" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Nama Periode Sekarang</label>
                           <input type="text" required name="periode_name" placeholder="Nama Periode" class="form-control form-control-sm"
                              id="periode_name" >
                        </div>
                     </div>
                  </div>
               </form>`;
   return form;
}

function close_book(){
   $.confirm({
      title: 'Peringatan',
      theme: 'material',
      columnClass: 'col-4',
      content: 'Apakah benar anda akan melakukan penutupan buku?.',
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         formSubmit: {
            text: 'Ya',
            btnClass: 'btn-red',
            action: function () {
               $.confirm({
                  title: 'Form Tutup Buku',
                  theme: 'material',
                  columnClass: 'col-4',
                  content: formCloseBook(),
                  closeIcon: false,
                  buttons: {
                     cancel: function () {
                          return true;
                     },
                     formSubmit: {
                        text: 'Simpan dan Tutup Buku',
                        btnClass: 'btn-blue',
                        action: function () {
                           ajax_submit_t1("#form_utama", function(e) {
                              if( e.error == false ){
                                 get_akun(1000);
                                 return true;
                              }
                              e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           });
                        }
                     }
                  }
               });
            }
         }
      }
   });
}

function reopen_book() {
   ajax_x_t2(
      baseUrl + "Akun/reopen_book", function(e) {
         if( e['error'] == false ){
            smile_alert(e['error_msg']);
            get_akun(1000);
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function akun_getData(){
   get_akun(1000);
}

function get_akun(perpage){
   get_data( perpage,
             { url : 'Akun/daftar_akun',
               pagination_id: 'pagination_akun',
               bodyTable_id: 'bodyTable_akun',
               fn: 'ListDaftarAkun',
               warning_text: '<td colspan="6">Daftar akun tidak ditemukan</td>',
               param : { filter : $('#filter').val() } } );
}

function ListDaftarAkun(JSONData){
   var json = JSON.parse(JSONData);
   var html =    `<tr ${ json.level == 'primary' ? 'style="background-color:#efefef;"' : '' }>`;

   if( json.level == 'primary' ){
      html += `<td colspan="2" class="text-left" style="color: black !important;vertical-align: middle;"><b>${json.nomor_akun}</b></td>
               <td class="text-left" style="color: black !important;vertical-align: middle;"><b>${json.nama_akun}</b></td>
               <td style="color: black !important;vertical-align: middle;"><b>${json.nama_akun}</b></td>`;
   }else{
      html += `<td style="width:4%;"><i class="fas fa-arrow-right" ></i></td><td class="text-left">${json.nomor_akun}</td>
               <td class="text-left">${json.nama_akun}</td>
               <td>${json.tipe.toUpperCase()}</td>`;
   }

   html += ``;
   if( json.level == 'primary' ){
      html += `<td style="color: black !important;vertical-align: middle;"><b>${json.saldo_awal}</b></td>
               <td style="color: black !important;vertical-align: middle;"><b></b></td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Tambah Header" onclick="addAkun(${json.nomor_akun})" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-plus" style="font-size: 11px;"></i>
                  </button>
               </td>`;
   }else{
      html += `
               <td>${json.saldo_awal}</td>
               <td>${kurs}${ json.saldo_akhir != undefined ? numberFormat(json.saldo_akhir) : 0 }</td>
               <td>`;

      if( json.tipe == 'bawaan' ){
         html += `<button type="button" class="btn btn-default btn-action" title="Tambah Header" onclick="addSaldo(${json.id})" style="margin:.15rem .1rem  !important">
                     <i class="fas fa-money-bill-wave" style="font-size: 11px;"></i>
                  </button>`;
      }else{
         html += `<button type="button" class="btn btn-default btn-action" title="Edit Akun" onclick="editAkun(${json.id})"
                     style="margin:.15rem .1rem  !important">
                     <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Akun" onclick="deleteAkun(${json.id})"
                     style="margin:.15rem .1rem  !important">
                     <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>`;
      }

      html +=  `</td>`;
   }
html +=`</tr>`;
   return html;
}

// delete akun
function deleteAkun(akun_id){
   $.confirm({
      title: 'Peringatan',
      theme: 'material',
      columnClass: 'col-4',
      content: 'Jika anda menghapus akun ini, maka semua transaksi jurnal yang menggunakan akun ini juga ikut terhapus. Apakah anda yakin ingin melanjutkan menghapus?.',
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         formSubmit: {
            text: 'Lanjutkan',
            btnClass: 'btn-red',
            action: function () {
               ajax_x_t2(
                  baseUrl + "Akun/delete_akun", function(e) {
                      if( e['error'] == false ){
                         get_akun(1000);
                     }
                     e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                  },[{id:akun_id}]
               );
            }
         }
      }
   });
}


function editAkun(akun_id){
   ajax_x_t2(
      baseUrl + "Akun/info_edit_akun", function(e) {
         if( e['error'] == false ){
            $.confirm({
               title: 'Form Edit Akun',
               theme: 'material',
               columnClass: 'col-4',
               content: formAddUpdateAkun(e['data'], JSON.stringify(e['value']) ),
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
                           get_akun(1000);
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:akun_id}]
   );
}

function addAkun(nomor_akun){
   $.confirm({
      title: 'Form Tambah Akun',
      theme: 'material',
      columnClass: 'col-4',
      content: formAddUpdateAkun(nomor_akun),
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
                  get_akun(1000);
               });
            }
         }
      }
   });
}

// saldo akun
function formSaldoAkun(id, saldo){
   var form = `<form action="${baseUrl }Akun/update_saldo" id="form_utama" class="formName ">
                  <input type="hidden" name="id" value="${id}" >
                  <div class="row px-0 mx-0">
                     <div class="col-12 px-0" >
                        <div class="form-group">
                           <label>Saldo</label>
                           <input type="text" required name="saldo" placeholder="Saldo" class="form-control currency form-control-sm"
                              id="saldo" value="${saldo}">
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


function addSaldo(id){
   ajax_x_t2(
      baseUrl + "Akun/get_info_saldo_akun", function(e) {
         if( e['error'] == false ){
            $.confirm({
               title: `Form Edit Saldo Akun <br><div class="mb-1" style="color:red;margin-top: 10px;font-size: 13px;">${e['nama_akun']} (${e['nomor_akun']})</div><p class="my-0" style="font-size:11px;font-style:italic;font-weight: normal;">Saat anda mengedit akun, pastikan anda mengerti tentang akuntansi. Agar tidak terjadi kesalahan pada perhitungan laporan.</p>`,
               theme: 'material',
               columnClass: 'col-4',
               content: formSaldoAkun(id, e['saldo']) ,
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
                           get_akun(1000);
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

// check akun exist
function check_akun_exist(){
   var new_akun = $('#new_akun').val();
   if( new_akun.length > 4 ){
      var minLengAkun = new_akun.toString().length - 1;
      $('#new_akun').val(new_akun.toString().substring(0, parseInt(minLengAkun)));
   }else{
      if( new_akun != '' ) {
         if( $('#akun_id').length > 0 ){
            ajax_x_t2(
               baseUrl + "Akun/check_akun_exist", function(e) {
                  if( e['error'] == false ){
                     $('#akun_message').html('<span style="color:green;">' + e['error_msg'] + '</span>');
                  }else{
                     $('#akun_message').html('<span style="color:red;">' + e['error_msg'] + '</span>');
                  }
               },[{new_akun:$('#head_akun').val() + new_akun,  akun_id: $('#akun_id').val() }]
            );
         }else{
            ajax_x_t2(
               baseUrl + "Akun/check_akun_exist", function(e) {
                  if( e['error'] == false ){
                     $('#akun_message').html('<span style="color:green;">' + e['error_msg'] + '</span>');
                  }else{
                     $('#akun_message').html('<span style="color:red;">' + e['error_msg'] + '</span>');
                  }
               },[{new_akun:$('#head_akun').val() + new_akun }]
            );
         }
      }else{
         $('#akun_message').html('Check Nomor Akun');
      }
   }
}

function formAddUpdateAkun(nomor_akun, JSONValue){
   var new_akun = '';
   var nama_akun = '';
   var saldo = '';
   var id_area = '';
   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      id_area = `<input type="hidden" id="akun_id" name="akun_id" value="${value.id}">`;
      new_akun = value.nomor_akun;
      nama_akun = value.nama_akun;
      saldo = `${kurs} ` + numberFormat(value.saldo);
   }
   var form = `<form action="${baseUrl }Akun/add_update_akun" id="form_utama" class="formName ">
                  ${id_area}
                  <div class="row px-0 mx-0">
                     <div class="col-12 mb-2" >
                        <div class="form-group">
                           <label>Nomor Akun</label>
                           <div class="input-group">
                              <span class="input-group-text" style="font-size: 12px;border-top-right-radius: 0px;border-bottom-right-radius: 0px;line-height: 1.4;border-right: 0px;">${nomor_akun.toString().substring(0, 1)}</span>
                              <input maxlength="4" minlength="4" type="text" id="new_akun" name="new_akun" class="form-control form-control-sm" style="height: calc(1.9rem + 2px);" onkeyup="check_akun_exist()" value="${new_akun}" >
                              <input type="hidden" id="head_akun" name="head_akun" value="${nomor_akun.toString().substring(0, 1)}">
                              <div class="input-group-append">
                                 <span class="input-group-text" style="font-size: 12px;" id="akun_message" >Check Nomor Akun</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Nama Akun</label>
                           <input type="text" required name="nama_akun" placeholder="Nama Akun" class="form-control form-control-sm"
                              id="nama_akun" value="${nama_akun}">
                        </div>
                     </div>
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Saldo</label>
                           <input type="text" required name="saldo" placeholder="Saldo" class="form-control currency form-control-sm"
                              id="saldo" value="${saldo}">
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $(document).on( "keyup", ".currency", function(e){
                      var e = window.event || e;
                      var keyUnicode = e.charCode || e.keyCode;
                          if (e !== undefined) {
                              console.log("======================");
                              console.log(keyUnicode);
                              console.log("======================");
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
