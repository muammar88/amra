function daftar_agen_Pages(){
   return  `<div class="col-6 col-lg-8 my-3">
               <button class="btn btn-default" type="button" onclick="download_excel_daftar_agen()">
                  <i class="fas fa-download"></i> Download Excel Agen
               </button>
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-4 my-3">
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_agen(20)"
                     id="searchDaftarAgen" name="searchDaftarAgen" placeholder="Nomor Identitas/Nama Agen"
                     style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_daftar_agen(20)">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:50%;">Nama / Nomor Identitas Agen</th>
                        <th style="width:40%;">Info Agen</th>
                        <th style="width:10%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_daftar_agen">
                     <tr>
                        <td colspan="6">Daftar agen tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_daftar_agen"></div>
            </div>`;
}

function daftar_agen_getData(){
   get_daftar_agen(20);
}

function get_daftar_agen(perpage){
   get_data( perpage,
             { url : 'Daftar_agen/daftar_agens',
               pagination_id: 'pagination_daftar_agen',
               bodyTable_id: 'bodyTable_daftar_agen',
               fn: 'ListDaftarAgen',
               warning_text: '<td colspan="4">Daftar agen tidak ditemukan</td>',
               param : { search : $('#searchDaftarAgen').val() } } );
}

function ListDaftarAgen(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.fullname} / ${json.identity_number}</td>
                  <td>
                     <ul class="list my-0">
                        <li>Level Agen : ${json.level_agen}</li>
                        <li>Upline : ${json.upline != null ? json.upline : '-'}</li>
                        <li>Jumlah Jamaah : ${json.jumlah_jamaah} Orang</li>
                     </ul>
                  </td>
                  <td>`;

      if( json.level_agen == 'agen' ) {
         html +=    `<button type="button" class="btn btn-default btn-action" title="Upgrade Level Agen"
                        onclick="upgrade_level_agen('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-level-up-alt" style="font-size: 11px;"></i>
                     </button>`;
      }

         html +=    `<button type="button" class="btn btn-default btn-action" title="Delete Agen"
                        onclick="delete_agen('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function delete_agen(id){
   ajax_x(
      baseUrl + "Daftar_agen/delete_agen", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if( e['error'] != true ){
            menu( this, 'daftar_agen', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
         }
      },[{id:id}]
   );
}

function upgrade_level_agen(id){
   ajax_x(
      baseUrl + "Daftar_agen/upgrade_level_agen", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if( e['error'] != true ){
            menu( this, 'daftar_agen', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
         }
      },[{id:id}]
   );
}

function download_excel_daftar_agen(){
   ajax_x_t2(
      baseUrl + "Daftar_agen/download_excel_daftar_agen",
      function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
      },
      []
   );
}
