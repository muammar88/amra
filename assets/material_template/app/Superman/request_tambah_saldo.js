function request_tambah_saldo_Pages(){
	 return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-3 col-lg-1 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="status" id="status" onchange="get_request_tambah_saldo(20)" title="Status Request">
                           <option value="proses">Proses</option>
                           <option value="disetujui">Disetujui</option>
                           <option value="ditolak">Ditolak</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-3 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_request_tambah_saldo(20)" id="searchAllRequestTambahSaldo" name="searchAllRequestTambahSaldo" placeholder="Kode Request" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_request_tambah_saldo(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Kode</th>
                              <th style="width:20%;">Nama Perusahaan</th>
                              <th style="width:20%;">Nama Bank</th>
                              <th style="width:10%;">Nominal Transfer</th>
                              <th style="width:10%;">Status</th>
                              <th style="width:15%;">Waktu Kirim</th>
                              <th style="width:15%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_request_tambah_saldo">
                           <tr>
                              <td colspan="7">Daftar request tambah saldo tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_request_tambah_saldo"></div>
                  </div>
               </div>
            </div>`;
}

function request_tambah_saldo_getData() {
  get_request_tambah_saldo(20);
}

function get_request_tambah_saldo(perpage){
   get_data(perpage, {
      url: "Superman/daftar_request_tambah_saldo",
      pagination_id: "pagination_daftar_request_tambah_saldo",
      bodyTable_id: "bodyTable_daftar_request_tambah_saldo",
      fn: "ListDaftarRequestTambahSaldo",
      warning_text: '<td colspan="7">Daftar request tambah saldo tidak ditemukan</td>',
      param: { search: $('#searchAllRequestTambahSaldo').val(), status: $('#status').val() } ,
   });
}

function ListDaftarRequestTambahSaldo(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td><b>#${json.kode}</b></td>
                  <td><b>#${json.kode_perusahaan}</b><br>${json.nama_perusahaan}</td>
                  <td>${json.nama_bank} <br> AN : ${json.nama_akun_bank} <br> NOREK : ${json.nomor_akun_bank}</td>
                  <td>Rp ${numberFormat(json.nominal)}</td>
                  <td style="text-transform:uppercase; font-weight:bold;color: ${json.status == 'proses' ? 'orange' : ( json.status == 'disetujui' ? 'green' : 'red' ) }!important;">${json.status}</td>
                  <td>${json.waktu_kirim}</td>
                  <td>`;
                  if( json.status == 'proses' ) {
                     html += `<button type="button" class="btn btn-default btn-action" title="Delete Request" onclick="delete_request(${json.id})" style="margin:.15rem .1rem  !important">
                                     <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                              <button type="button" class="btn btn-default btn-action" title="Reject Request" onclick="reject_request_tambah_saldo(${json.id})"  style="margin:.15rem .1rem  !important;background-color: #d06464 !important;color: white !important;">
                                     <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                              <button type="button" class="btn btn-default btn-action" title="Approve Request" onclick="approve_request_tambah_saldo(${json.id})"  style="margin:.15rem .1rem  !important;background-color: #4fa845 !important;color: white !important;">
                                     <i class="fas fa-check-double" style="font-size: 11px;"></i>
                              </button>`;
                  }else{
                     html += `<span>Request sudah diproses</span>`;
                  }
      html +=    `</td>
               </tr>`;
   return html;
}

function approve_request_tambah_saldo(id){
   $.confirm({
      title: "Menyetujui Request Tambah Saldo",
      theme: "material",
      columnClass: "col-4",
      content: 'Apakah anda akan menyetujui request tambah saldo ini?',
      closeIcon: false,
      buttons: {
         cancel: function () {
           return true;
         },
         simpan: {
            text: 'Setujui/Approve',
            btnClass: 'btn-green',
            action: function () {
               ajax_x(
                   baseUrl + "Superman/proses_approve_request_tambah_saldo",
                   function (e) {
                     if (e["error"] == false) {
                         smile_alert(e["error_msg"]);
                         get_request_tambah_saldo(20);
                     } else {
                       frown_alert(e["error_msg"]);
                     }
                   },
                [{id:id}]
               );
            }
         }
      },
   });
}

function reject_request_tambah_saldo(id){
    $.confirm({
      title: "Tolak Request Tambah Saldo",
      theme: "material",
      columnClass: "col-4",
      content: `<form action="${baseUrl}Superman/proses_tolak_request_tambah_saldo" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <input type="hidden" value="${id}" name="id">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Alasan ditolak</label>
                                 <textarea class="form-control form-control-sm" name="alasan" placeholder="Alasan penolakan" rows="10" style="resize: none;"></textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`,
      closeIcon: false,
      buttons: {
         cancel: function () {
           return true;
         },
         simpan: {
            text: 'Tolak',
            btnClass: 'btn-red',
            action: function () {
               var ajax = ajax_submit_r("#form_utama");
               if ( ajax['error'] == true ) {
                  return false;
               } else {
                  get_request_tambah_saldo(20);
                  return true;
               }
               ajax['error'] == true ? frown_alert(ajax['error_msg']) : smile_alert(ajax['error_msg']);
            }
         }
      },
   });
}

function delete_request(id){
   $.confirm({
      title: "Menghapus Request Tambah Saldo",
      theme: "material",
      columnClass: "col-4",
      content: 'Apakah anda akan menghapus request tambah saldo pelanggan AMRA?.',
      closeIcon: false,
      buttons: {
         cancel: function () {
           return true;
         },
         simpan: {
            text: 'ya',
            btnClass: 'btn-red',
            action: function () {
               ajax_x(
                   baseUrl + "Superman/proses_delete_request_tambah_saldo",
                   function (e) {
                     if (e["error"] == false) {
                         smile_alert(e["error_msg"]);
                         get_request_tambah_saldo(20);
                     } else {
                       frown_alert(e["error_msg"]);
                     }
                   },
                [{id:id}]
               );
            }
         }
      },
   });
}


// function formMenyetujuiRequestTambahSaldo(id){

// var ajax = ajax_submit_r("#form_utama");
// if ( ajax['error'] == true ) {
//    return false;
// } else {
//    get_daftar_perusahaan(20);
// }

// function formMenyetujuiRequestTambahSaldo(id){
//    var html = `<form action="${baseUrl}Superman/proses_approve_request_tambah_saldo" id="form_utama" class="formName ">
//                   <div class="row px-0 mx-0">
//                      <div class="col-12">
//                         <input type="hidden" value="${id}" name="id">
//                         <div class="row">
//                            <div class="col-12">
//                               <div class="form-group mb-2">
//                                  <label>Saldo Yang Ditambah</label>
//                                  <input type="text" name="saldo" id="saldo" class="form-control form-control-sm currency" placeholder="Saldo" />
//                               </div>
//                            </div>
//                         </div>
//                      </div>
//                   </div>
//                </form>
//                <script>
//                   $(document).on( "keyup", ".currency", function(e){
//                       var e = window.event || e;
//                       var keyUnicode = e.charCode || e.keyCode;
//                           if (e !== undefined) {
//                               switch (keyUnicode) {
//                                   case 16: break;
//                                   case 27: this.value = ''; break;
//                                   case 35: break;
//                                   case 36: break;
//                                   case 37: break;
//                                   case 38: break;
//                                   case 39: break;
//                                   case 40: break;
//                                   case 78: break;
//                                   case 110: break;
//                                   case 190: break;
//                                   default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
//                               }
//                           }
//                   } );
//                </script>`;
//    return html; 
// }
