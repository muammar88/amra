function deposit_saldo_Pages(){
   return  `<div class="col-6 col-lg-8 my-3">
               <button class="btn btn-default" type="button" onclick="start_deposit_saldo()">
                  <i class="fas fa-hand-holding-usd"></i> Deposit Saldo
               </button>
               <button class="btn btn-default mx-3" type="button" onclick="()">
                  <i class="fas fa-print"></i> Download Excel Data Jamaah
               </button>
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-4 my-3">
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_deposit_saldo(20)"
                     id="searchDepositSaldo" name="searchDepositSaldo" placeholder="Nomor Transaksi/Nomor Identitas/Nama Agen"
                     style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_deposit_saldo(20)">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:15%;">Nomor Transaksi</th>
                        <th style="width:20%;">Info Member</th>
                        <th style="width:37%;">Info</th>
                        <th style="width:18%;">Waktu Transaksi</th>
                        <th style="width:10%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_riwayat_deposit_saldo">
                     <tr>
                        <td colspan="5">Riwayat deposit saldo tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_riwayat_deposit_saldo"></div>
            </div>`;
}

function deposit_saldo_getData(){
   get_deposit_saldo(20);
}

function get_deposit_saldo(perpage){
   get_data( perpage,
             { url : 'Deposit_saldo/daftar_deposit_saldo',
               pagination_id: 'pagination_riwayat_deposit_saldo',
               bodyTable_id: 'bodyTable_riwayat_deposit_saldo',
               fn: 'ListRiwayatDepositSaldo',
               warning_text: '<td colspan="5">Daftar riwayat deposit saldo tidak ditemukan</td>',
               param : { search : $('#searchDepositSaldo').val() } } );
}

function ListRiwayatDepositSaldo(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.nomor_transaksi}</td>
                  <td>${json.fullname} <br>(${json.identity_number})</td>
                  <td>
                     <ul class="list my-0">
                        <li>Biaya Deposit : Rp ${numberFormat(json.debet)}</li>
                        <li>Penerima : ${json.penerima}</li>
                        <li>Info : ${json.info}</li>
                     </ul>
                  </td>
                  <td>${json.waktu_transaksi}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Delete Riwayat Deposit Saldo"
                        onclick="delete_deposit_saldo('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Deposit Saldo"
                        onclick="cetak_kwitansi_deposit_saldo('${json.id}')" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function cetak_kwitansi_deposit_saldo(id){
   ajax_x(
      baseUrl + "Deposit_saldo/cetak_kwitansi_deposit_saldo", function(e) {
         if( e['error'] == false ){
            window.open(baseUrl + "Kwitansi/", "_blank");
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function start_deposit_saldo(){
   ajax_x(
      baseUrl + "Deposit_saldo/get_info_deposit_saldo", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Form Transaksi Deposit Saldo',
               theme: 'material',
               content: formaddupdate_transaksi_deposit_saldo(JSON.stringify(e['data'])),
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
                              window.open(baseUrl + "Kwitansi/", "_blank");
                              get_deposit_saldo(20);
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

function formaddupdate_transaksi_deposit_saldo(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Deposit_saldo/proses_addupdate_deposit_saldo" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nomor Transaksi</label>
                                 <input type="text" name="nomor_transaksi" value="${json.nomor_transaksi}" class="form-control form-control-sm"  readonly/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Member</label>
                                 <select class="form-control form-control-sm" name="member" id="jamaah_id">`;
                          for( x in json.list_member ) {
                             html += `<option value="${json.list_member[x]['id']}" >${json.list_member[x]['fullname']} (${json.list_member[x]['nomor_identitas']})</option>`;
                          }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Biaya Deposit</label>
                                 <input type="text" name="biaya_deposit" value="" class="form-control form-control-sm currency" placeholder="Biaya Deposit" required />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Info Deposit</label>
                                 <textarea class="form-control form-control-sm" name="info" rows="6"
                                    style="resize: none;" placeholder="Info Deposit" required></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>
               <script>
                  $("#jamaah_id").select2({
                     dropdownParent: $(".jconfirm")
                  });
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

function delete_deposit_saldo(id){
   ajax_x(
      baseUrl + "Deposit_saldo/delete_riwayat_saldo", function(e) {
         if( e['error'] == false ){
             get_deposit_saldo(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
