function request_keagenan_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentRequestKeagenan">
                  <div class="col-6 col-lg-9 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="status" id="status" onChange="get_request_keagenan(20)">
                           <option value="pilih_semua" >Pilih semua status request</option>
                           <option value="disetujui" >Disetujui</option>
                           <option value="ditolak" >Ditolak</option>
                           <option value="diproses" >Diproses</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:28%;">Nama / Nomor Identitas</th>
                              <th style="width:20%;">Penerima</th>
                              <th style="width:12%;">Status</th>
                              <th style="width:30%;">Catatan</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_request_keagenan">
                           <tr>
                              <td colspan="5">Daftar request keagenan tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_request_keagenan"></div>
                  </div>
               </div>
            </div>`;
}

function request_keagenan_getData(){
   get_request_keagenan(20);
}

function get_request_keagenan(perpage){
   get_data( perpage,
             { url : 'Request_keagenan/daftar_request_keagenan',
               pagination_id: 'pagination_request_keagenan',
               bodyTable_id: 'bodyTable_request_keagenan',
               fn: 'ListRequestKeagenan',
               warning_text: '<td colspan="5">Daftar request_keagenan tidak ditemukan</td>',
               param : { search : $('#status').val() } } );
}

function ListRequestKeagenan(JSONData){
   var  json = JSON.parse(JSONData);
   var button = '';
   var status = '';
   if( json.status_request == 'disetujui'){
      status  =  '<span style="color:green;font-weight:bold;">'+json.status_request.toUpperCase() + '</span>';
      button +=  `<button type="button" class="btn btn-default btn-action" title="Tolak request"
                     onclick="decline('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>`;
   }else if (json.status_request == 'ditolak'){
      status  =  '<span style="color:red;font-weight:bold;">'+json.status_request.toUpperCase() + '</span>';
      button +=  `<button type="button" class="btn btn-default btn-action" title="Setujui request"
                     onclick="approve('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-check" style="font-size: 11px;"></i>
                  </button>`;
   }else if (json.status_request == 'diproses'){
      status  =  '<span style="color:orange;font-weight:bold;">'+json.status_request.toUpperCase() + '</span>';
      button +=  `<button type="button" class="btn btn-default btn-action" title="Setujui request"
                     onclick="approve('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-check" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Tolak request"
                     onclick="decline('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>`;
   }
   var html = `<tr>
                  <td>${json.fullname} /<br>${json.identity_number}</td>
                  <td>${json.approver == '' ? '-' : json.approver}</td>
                  <td>${status}</td>
                  <td>${json.status_note == '' ? '-' : json.status_note}</td>
                  <td>${button}</td>
               </tr>`;
   return html;
}

function approve(id){
   ajax_x(
      baseUrl + "Request_keagenan/approve", function(e) {
         if( e['error'] == false ){
            get_request_keagenan(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}

function formRequestKeagenan(id){
   var html = `<form action="${baseUrl }Request_keagenan/proses_decline_request_keagenan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <input type="hidden" name="id" value="${id}">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Catatan Penolakan</label>
                                 <textarea class="form-control form-control-sm" name="note" rows="6"
                                    style="resize: none;" placeholder="Catatan Penolakan" required></textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function decline(id){
   ajax_x(
      baseUrl + "Request_keagenan/info_decline", function(e) {
         if( e['error'] == true ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Peringatan',
               theme: 'material',
               content: e['error_msg'],
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  lanjutkan: {
                     text: 'Lanjutkan',
                     btnClass: 'btn-blue',
                     action: function () {
                        $.confirm({
                           columnClass: 'col-4',
                           title: 'Tolak request keagenan',
                           theme: 'material',
                           content: formRequestKeagenan(id),
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
                                       if( e['error'] == false ){
                                          get_request_keagenan(20);
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
         }else{
            $.confirm({
               columnClass: 'col-4',
               title: 'Tolak request keagenan',
               theme: 'material',
               content: formRequestKeagenan(id),
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
                           if( e['error'] == false ){
                              get_request_keagenan(20);
                           }
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                        });
                     }
                  }
               }
            });
         }
      },[{id:id}]
   );





}
