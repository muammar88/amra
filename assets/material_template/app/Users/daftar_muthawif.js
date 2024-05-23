function daftar_muthawif_Pages(){
   return  `<div class="col-6 col-lg-8 my-3">
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-4 my-3">
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_muthawif( 20 )"
                     id="searchDaftarMuthawif" name="searchDaftarMuthawif" placeholder="Nomor Identitas/Nama Muthawif"
                     style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_daftar_muthawif( 20 )">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:60%;">Nama / Nomor Identitas Muthawif</th>
                        <th style="width:10%;">Jumlah Paket</th>
                        <th style="width:20%;">Waktu Input</th>
                        <th style="width:10%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_daftar_muthawif">
                     <tr>
                        <td colspan="6">Daftar muthawif tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_daftar_muthawif"></div>
            </div>`;
}

function daftar_muthawif_getData(){
   get_daftar_muthawif( 20 );
}

function get_daftar_muthawif(perpage){
   get_data( perpage,
             { url : 'Daftar_muthawif/daftar_muthawifs',
               pagination_id: 'pagination_daftar_muthawif',
               bodyTable_id: 'bodyTable_daftar_muthawif',
               fn: 'ListDaftarMuthawif',
               warning_text: '<td colspan="6">Daftar muthawif tidak ditemukan</td>',
               param : { search : $('#searchDaftarMuthawif').val() } } );
}

function ListDaftarMuthawif(JSONData){
   var json = JSON.parse(JSONData);
   return  `<tr>
               <td>${json.fullname} / ${json.identity_number}</td>
               <td>${json.jumlah_paket}</td>
               <td>${json.waktu_input}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Delete Muthawif"
                     onClick="delete_muthawif('${json["id"]}')">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function delete_muthawif(id){
   ajax_x(
      baseUrl + "Daftar_muthawif/delete_muthawif", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if( e['error'] != true ){
            menu( this, 'daftar_muthawif', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
         }
      },[{id:id}]
   );
}
