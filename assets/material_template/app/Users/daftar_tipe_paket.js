function daftar_tipe_paket_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarTipePaket">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_tipe_paket()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Tipe Paket
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_tipe_paket( 20)" id="searchAllDaftarTipePaket" name="searchAllDaftarTipePaket" placeholder="Nama Tipe Paket" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_tipe_paket( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:70%;">Nama Tipe Paket</th>
                              <th style="width:20%;">Waktu Pembaharuan Terakhir</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_tipe_paket">
                           <tr>
                              <td colspan="4">Daftar tipe paket tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_tipe_paket"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_tipe_paket_getData(){
   get_daftar_tipe_paket(20);
}

function get_daftar_tipe_paket(perpage){
   get_data( perpage,
             { url : 'Daftar_tipe_paket/daftar_tipe_pakets',
               pagination_id: 'pagination_daftar_tipe_paket',
               bodyTable_id: 'bodyTable_daftar_tipe_paket',
               fn: 'ListDaftarTipePaket',
               warning_text: '<td colspan="4">Daftar tipe paket tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarTipePaket').val() } } );
}

function ListDaftarTipePaket(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.nama_tipe_paket}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Tipe Paket"
                        onclick="edit_tipe_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Tipe Paket"
                        onclick="delete_tipe_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_tipe_paket(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Tipe Paket',
      theme: 'material',
      content: formaddupdate_tipe_paket(),
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
                  $.alert({
                     title: 'Peringatan',
                     content: e['error_msg'],
                     type: e['error'] == true ? 'red' :'green'
                  });
                  if ( e['error'] == true ) {
                     return false;
                  } else {
                     get_daftar_tipe_paket(20);
                  }
               });
            }
         }
      }
   });
}

function edit_tipe_paket(id){
   ajax_x(
      baseUrl + "Daftar_tipe_paket/get_info_addupdate_tipe_paket", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Tipe Paket',
               theme: 'material',
               content: formaddupdate_tipe_paket(JSON.stringify(e['data'])),
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
                           $.alert({
                              title: 'Peringatan',
                              content: e['error_msg'],
                              type: e['error'] == true ? 'red' :'green'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_tipe_paket(20);
                           }
                        });
                     }
                  }
               }
            });
         }else{
            $.alert({
               icon: 'far fa-frown',
               title: 'Peringatan',
               content: e['error_msg'],
               type: 'red'
            });
         }
      },[{id:id}]
   );
}

function formaddupdate_tipe_paket(JSONValue){
   var id_tipe_paket = '';
   var nama_tipe_paket = '';
   var input_date = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_tipe_paket = `<input type="hidden" name="id" value="${value.id}">`;
      nama_tipe_paket = value.nama_tipe_paket;
   }

   var html = `<form action="${baseUrl }Daftar_tipe_paket/proses_addupdate_tipe_paket" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id_tipe_paket}
                                 <label>Nama Tipe Paket</label>
                                 <input type="text" name="nama_tipe_paket" value="${nama_tipe_paket}" class="form-control form-control-sm" placeholder="Nama tipe Paket" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}

// delete tipe paket
function delete_tipe_paket(id){
   ajax_x(
      baseUrl + "Daftar_tipe_paket/delete_tipe_paket", function(e) {
         if( e['error'] == false ){
             get_daftar_tipe_paket(20);
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'green',
         });
      },[{id:id}]
   );
}
