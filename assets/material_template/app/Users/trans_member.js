function trans_member_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentTransMember">
                  <div class="col-6 col-lg-7 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="status" id="status" onChange="get_trans_member(20)" title="Status Transaksi">
                           <option value="diproses" >Diproses</option>
                           <option value="disetujui" >Disetujui</option>
                           <option value="ditolak" >Ditolak</option>
                           <option value="semua" >Pilih Semua</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input title="Nama / Nomor Identitas Member" class="form-control form-control-sm" type="text" onkeyup="get_trans_member(20)" id="searchNamaIdentityMember" name="searchNamaIdentityMember" placeholder="Nama / Nomor Identitas Member" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_trans_member(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:20%;">Nama / <br>Nomor Identitas Member</th>
                              <th style="width:25%;">Ref</th>
                              <th style="width:30%;">Info Transaksi</th>
                              <th style="width:15%;">Bukti Pembayaran</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_trans_member">
                           <tr>
                              <td colspan="5">Daftar transaksi member tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_trans_member"></div>
                  </div>
               </div>
            </div>`;
}

function trans_member_getData(){
   get_trans_member(20);
}

function get_trans_member(perpage){
   get_data( perpage,
             { url : 'Trans_member/daftar_transaksi_member',
               pagination_id: 'pagination_trans_member',
               bodyTable_id: 'bodyTable_trans_member',
               fn: 'ListTransMember',
               warning_text: '<td colspan="5">Daftar transaksi member tidak ditemukan</td>',
               param : { status : $('#status').val(), name_identity: $('#searchNamaIdentityMember').val() } } );
}

function ListTransMember(JSONData){
   var json = JSON.parse(JSONData);
   var status = '';
   if( json.status_request == 'diproses' ){
      status += `<span style="color:orange;font-weight:bold;">${json.status_request.toUpperCase()}</span>`;
   }else if ( json.status_request == 'disetujui' ) {
      status += `<span style="color:green;font-weight:bold;">${json.status_request.toUpperCase()}</span>`;
   }else if ( json.status_request == 'ditolak' ) {
      status += `<span style="color:red;font-weight:bold;">${json.status_request.toUpperCase()}</span>`;
   }

   var html = `<tr>
                  <td>${json.fullname} /<br>${json.identity_number}</td>
                  <td>${json.ref}</td>
                  <td>
                     <ul class="list">
                        <li>Amount : ${kurs} ${numberFormat(json.amount)}</li>
                        <li>Tipe Transaksi : ${json.activity_type}</li>
                        <li>Status Request : ${status}</li>
                        <li>Sumber Pembayaran : ${json.payment_source}</li>
                        <li>Penerima : ${json.approver == '' ? '-' : json.approver}</li>
                        <li>Catatan : ${json.status_note == '' ? '-' : json.status_note}</li>
                     </ul>
                  </td>
                  <td>${json.transfer_evidence == '' ? 'Bukti tidak ditemukan' : `<img src="${baseUrl}/image/bukti/${json.transfer_evidence}" class="img-fluid" alt="Bukti Transfer" style="border: 2px solid #c9ccd7;border-radius: 4px;width: 68px;height: 94px;">`}</td>
                  <td>`;
                  if( json.status_request != 'disetujui') {
                     html += `<button type="button" class="btn btn-default btn-action" title="Setujui request"
                                 onclick="approve_trans_member('${json.id}')" style="margin:.15rem .1rem  !important">
                                  <i class="fas fa-check" style="font-size: 11px;"></i>
                              </button>`;
                  }
                  if( json.status_request != 'ditolak' ) {
                     html += `<button type="button" class="btn btn-default btn-action" title="Tolak request"
                                 onclick="decline_trans_member('${json.id}')" style="margin:.15rem .1rem  !important">
                                  <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>`;
                  }
         html += `</td>
               </tr>`;
   return html;
}

function approve_trans_member(id){
   ajax_x(
      baseUrl + "Trans_member/approve", function(e) {
         if( e['error'] == false ){
            get_trans_member(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}

function decline_trans_member(id){
   ajax_x(
      baseUrl + "Trans_member/decline", function(e) {
         if( e['error'] == false ){
            get_trans_member(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
