function pelanggan_ppob_Pages(){
	return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row">
                  <div class="col-6 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="addPelangganPPOB()">
                        <i class="fas fa-user"></i> Tambah Pelanggan PPOB
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="pelanggan_ppob_getData()" id="searchAllDaftarPelangganPPOB" name="searchAllDaftarPelangganPPOB" placeholder="Kode / Nama Pelanggan" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="pelanggan_ppob_getData()">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Kode Pelanggan</th>
                              <th style="width:20%;">Nama Pelanggan</th>
                              <th style="width:10%;">No Whatsapp</th>
                              <th style="width:10%;">Transaksi Hari Ini</th>
                              <th style="width:40%;">Info Saldo</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_pelanggan_ppob">
                           <tr>
                              <td colspan="6">Daftar pelanggan ppob tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_pelanggan_ppob"></div>
                  </div>
               </div>
            </div>`;
}

function pelanggan_ppob_getData() {
  get_pelanggan_ppob(300);
}

function get_pelanggan_ppob(perpage){
   get_data(perpage, {
      url: "Superman/Pelanggan_PPOB/daftar_pelanggan_ppob",
      pagination_id: "pagination_daftar_pelanggan_ppob",
      bodyTable_id: "bodyTable_daftar_pelanggan_ppob",
      fn: "ListDaftarPelangganPPOB",
      warning_text: '<td colspan="6">Daftar pelanggan ppob tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarPelangganPPOB').val() } ,
   });
}

function ListDaftarPelangganPPOB(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.code}</td>
                  <td>${json.name}</td>
                  <td>${json.whatsappnumber}</td>
                  <td>${json.transaksi_hari_ini}</td>
                  <td>
                     <div class="row">
                        <table class="table table-hover mb-0">
                           <tbody>
                              <tr>
                                 <td class="text-left" style="width:20%;">SALDO</td>
                                 <td class="px-0" style="width:1%;">:</td>
                                 <td class="text-left" style="width:79%;">Rp ${numberFormat(json.saldo)}</td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="row">
                        <table class="table table-hover ">
                           <thead>
                              <tr>
                                 <td class="text-center" colspan="5" style="background-color: #e7e7e7;"><b>5 DAFTAR RIWAYAT DEPOSIT SALDO PELANGGAN PPOB</b></td>
                              </tr>
                              <tr>
                                 <td class="text-center" style="width:10%;"><b>#</b></td>
                                 <td class="text-center" style="width:25%;"><b>DEBET</b></td>
                                 <td class="text-center" style="width:25%;"><b>KREDIT</b></td>
                                 <td class="text-center" style="width:30%;"><b>KET</b></td>
                                 <td class="text-center" style="width:10%;"><b>Aksi</b></td>
                              </tr>
                           </thead>
                           <tbody id="riwayat_deposit_saldo_${json.code}">`;
                  var n =1;
                  for ( x in json.riwayat_deposit_saldo ) {
                     html += `<tr>
                                 <td>${n}</td>
                                 <td>Rp ${numberFormat(json.riwayat_deposit_saldo[x].debet)}</td>
                                 <td>Rp ${numberFormat(json.riwayat_deposit_saldo[x].kredit)}</td>
                                 <td>${json.riwayat_deposit_saldo[x].ket}</td>
                                 <td>
                                    <button type="button" class="btn btn-default btn-action" title="Delete Riwayat Deposit" 
                                       onclick="deleteRiwayatDepositPPOB('${json.riwayat_deposit_saldo[x].id}', '${json.code}')" style="margin:.15rem .1rem  !important">
                                       <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </button>
                                 </td>
                              </tr>`;
                     n++;         
                  }
         html +=          `</tbody>
                        </table>
                     </div>
                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Tambah Saldo Pelanggan" onclick="tambahSaldoPelanggan(${json.id})" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-money-bill-wave" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Edit Pelanggan PPOB" onclick="editPelangganPPOB(${json.id})" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Pelanggan PPOB" onclick="deletePelangganPPOB(${json.id})" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function addPelangganPPOB(){
   ajax_x(
       baseUrl + "Superman/Pelanggan_PPOB/generated_code_new_pelanggan_ppob",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
               title: "Tambah Pelanggan PPOB",
               theme: "material",
               columnClass: "col-4",
               content: formAddUpdatePelangganPPOB( e.data ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  tambah: {
                    text: "Tambah Pelanggan",
                    btnClass: "btn-blue",
                    action: function () {
                      ajax_submit_t1("#form_utama", function (e) {
                        e["error"] == true
                          ? frown_alert(e["error_msg"])
                          : smile_alert(e["error_msg"]);
                          if( e["error"] == false ){
                              pelanggan_ppob_getData();
                          }
                      });
                    },
                  },
               },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    []
   );
}

function editPelangganPPOB(id){
   ajax_x(
       baseUrl + "Superman/Pelanggan_PPOB/get_edit_info_pelanggan_ppob",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
               title: "Edit Data Pelanggan PPOB",
               theme: "material",
               columnClass: "col-4",
               content: formAddUpdatePelangganPPOB( e.data, JSON.stringify( e.value ) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  tambah: {
                    text: "Update Pelanggan",
                    btnClass: "btn-blue",
                    action: function () {
                      ajax_submit_t1("#form_utama", function (e) {
                        e["error"] == true
                          ? frown_alert(e["error_msg"])
                          : smile_alert(e["error_msg"]);
                          if( e["error"] == false ){
                              pelanggan_ppob_getData();
                          }
                      });
                    },
                  },
               },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}


function formAddUpdatePelangganPPOB(kode, JSONValue){
   var id = '';
   var name = '';
   var whatsappnumber  = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse( JSONValue );
      id  = `<input type="hidden" value="${value.id}" name="id">`;
      kode = value.kode;
      name = value.name;
      whatsappnumber = value.whatsappnumber;
   }
   var html = `<form action="${baseUrl}Superman/Pelanggan_PPOB/proses_add_update_pelanggan_ppob" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           ${id}
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Kode Pelanggan</label>
                                 <input type="text" value="${kode}" class="form-control form-control-sm" disabled/>
                                 <input type="hidden" value="${kode}" name="kode" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nama Pelanggan</label>
                                 <input type="text" name="name" value="${name}" class="form-control form-control-sm" placeholder="Nama Pelanggan" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nomor Whatsapp</label>
                                 <input type="text" name="whatsappnumber" value="${whatsappnumber}" class="form-control form-control-sm" placeholder="Nomor Whatsapp" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Password</label>
                                 <input type="password" name="password"  class="form-control form-control-sm" placeholder="Password" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;     
}


function deletePelangganPPOB(id){
   ajax_x(
       baseUrl + "Superman/Pelanggan_PPOB/delete_pelanggan_ppob",
       function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            pelanggan_ppob_getData();
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}


function tambahSaldoPelanggan(id){
   $.confirm({
      title: "Tambah Saldo PPOB",
      theme: "material",
      columnClass: "col-4",
      content: formTambahSaldo( id ),
      closeIcon: false,
      buttons: {
         cancel: function () {
           return true;
         },
         tambah: {
           text: "Tambah Saldo Pelanggan",
           btnClass: "btn-blue",
           action: function () {
             ajax_submit_t1("#form_utama", function (e) {
               e["error"] == true
                 ? frown_alert(e["error_msg"])
                 : smile_alert(e["error_msg"]);
                 if( e["error"] == false ){
                     pelanggan_ppob_getData();
                 }
             });
           },
         },
      },
   });
}

function formTambahSaldo( id ) {
   var html = `<form action="${baseUrl}Superman/Pelanggan_PPOB/tambah_saldo_pelanggan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <input type="hidden" name="id" value="${id}"/>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Saldo yang ditambahkan</label>
                                 <input type="text" name="saldo" class="form-control form-control-sm currency" placeholder="Saldo yang ditambahkan"/>
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

function deleteRiwayatDepositPPOB(id, code){
   ajax_x(
       baseUrl + "Superman/Pelanggan_PPOB/deleteRiwayatDepositPPOB",
       function (e) {
         if (e["error"] == false) {
            pelanggan_ppob_getData();
            smile_alert(e["error_msg"]);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}