function withdraw_deposit_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-7 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="status_withdraw" id="status_withdraw" onchange="get_withdraw_deposit(20)" title="Status Request">
                           <option value="diproses">Diproses</option>
                           <option value="disetujui">Disetujui</option>
                           <option value="ditolak">Ditolak</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_withdraw_deposit(20)" id="searchwithdraw" name="searchwithdraw" placeholder="Nama Member" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_withdraw_deposit(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                        <button class="btn btn-default float-left ml-1" onClick="setMarkupWithDraw()" type="button" title="Set Markup Withdraw Perusahaan">
                           <i class="fas fa-cogs"></i>
                        </button>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Nomor Transaksi</th>
                              <th style="width:25%;">Member Info</th>
                              <th style="width:10%;">Amount</th>
                              <th style="width:15%;">Info Bank</th>
                              <th style="width:15%;">Status</th>
                              <th style="width:15%;">Terakhir Terupdate</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_withdraw_deposit">
                           <tr>
                              <td colspan="7">Daftar withdraw tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_withdraw_deposit"></div>
                  </div>
               </div>
            </div>`;
}

function withdraw_deposit_getData(){
   get_withdraw_deposit(20);
}

function get_withdraw_deposit(perpage){
   get_data( perpage,
             { url : 'Withdraw_deposit/server_side_withdraw',
               pagination_id: 'pagination_withdraw_deposit',
               bodyTable_id: 'bodyTable_withdraw_deposit',
               fn: 'ListWithdrawDeposit',
               warning_text: '<td colspan="7">Daftar withdraw tidak ditemukan</td>',
               param : { search : $('#searchwithdraw').val() , status : $('#status_withdraw').val() } } );
}

function ListWithdrawDeposit(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td><b>#${json.transaction_number}</b></td>
                  <td>${json.fullname}<br><b>No Identitas : ${json.identity_number}</b></td>
                  <td>Rp ${numberFormat(json.amount)}</td>
                  <td>${json.nama_bank} | <b>No Rek : ${json.account_number}</b><br><img class="mt-1" src="${json.logo_bank}" style="max-width: 150px;"></td>
                  <td style="text-align: justify !important;">${json.status_request == "diproses" ? `<center><b style="color:orange;">DIPROSES</b></center>` : (json.status_request == "disetujui" ? `<center><b style="color:green;">DISETUJUI</b></center>` : `<center><b style="color:red;">DITOLAK</b></center>`)  } ${json.status_request == 'ditolak' ? `<span style="color:red;"><b>Catatan : </b> ${json.status_note}</span>` : `` }</td>
                  <td><b style="color:orange;">${json.last_update}</b></td>
                  <td>`;
         if( json.status_request == 'diproses' ) {
            html += `<button type="button" class="btn btn-default btn-action" title="Reject Permintaan Withdraw" onclick="rejectRequestWithdraw('${json.id}')" style="margin:.15rem .1rem  !important;background-color: #d06464 !important;color: white!important;">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Setujui Permintaan Withdraw" onclick="approveRequestWithdraw('${json.id}')" style="margin: 0.15rem 0.1rem !important;background-color: #4fa845 !important;color: white!important;">
                        <i class="fas fa-check" style="font-size: 11px;"></i>
                     </button>`;
         }else{
            html += `<span>Status withdraw sudah tidak dapat dirubah</span>`;
         }
         html += `</td>
              </tr>`;
   return html;
}

function rejectRequestWithdraw(id){

   $.confirm({
      columnClass: "col-4",
      title: "Pemberitahuan",
      theme: "material",
      content: `Apakah anda yakin ingin menghapus permintaan withdraw Member.`,
      closeIcon: false,
      buttons: {
         cancel: function () {
            return true;
         },
         simpan: {
            text: "Iya",
            btnClass: "btn-red",
            action: function () {
               $.confirm({
                  columnClass: "col-4",
                  title: "Pemberitahuan",
                  theme: "material",
                  content: formRejectWithdraw(id),
                  closeIcon: false,
                  buttons: {
                     cancel: function () {
                        return true;
                     },
                     simpan: {
                        text: "Tolak Request Withdraw",
                        btnClass: "btn-red",
                        action: function () {
                           ajax_submit_t1("#form_utama", function(e) {
                              e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                              if ( e['error'] == true ) {
                                 return false;
                              } else {
                                 get_withdraw_deposit(20);
                              }
                           });
                        },
                     },
                  },
               });  
            },
         },
      },
   });
}


function formRejectWithdraw(id){
   var html = `<form action="${baseUrl }Withdraw_deposit/tolak_request_withdraw" id="form_utama" class="formName ">
                  <small class="form-text text-muted px-2 mb-3">Silahkan berikan alasan dari penolakan permintaan withdraw member.</small>
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <input type="hidden" name="id" value="${id}">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alasan Penolakan Request Withdraw</label>
                                 <textarea class="form-control form-control-sm" name="alasan" placeholder="Alasan Penolakan" rows="7" style="resize: none;"></textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function approveRequestWithdraw(id){
   ajax_x(
      baseUrl + "Withdraw_deposit/approve_request_withdraw",
      function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_withdraw_deposit(5);
         } else {
            frown_alert(e["error_msg"]);
         }
      },
      [{ id: id }]
   );
}


function setMarkupWithDraw(){
   ajax_x(
      baseUrl + "Withdraw_deposit/get_info_markup_withdraw", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Edit Markup Withdraw Perusahaan',
               theme: 'material',
               content: formaddupdate_markup_withdraw(e['data']),
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
                              get_markup_produk(20);
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


function formaddupdate_markup_withdraw(markup){
   var html = `<form action="${baseUrl }Withdraw_deposit/proses_update_markup_withdraw" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Markup Default Perusahaan</label>
                                 <input type="text" name="markup_withdraw" value="Rp ${numberFormat(markup)}" class="form-control form-control-sm currency" placeholder="Markup Withdraw" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
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
