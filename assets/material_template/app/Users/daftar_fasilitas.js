function daftar_fasilitas_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarFasilitas">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_fasilitas()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Fasilitas
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_fasilitas( 20)" id="searchAllDaftarFasilitas" name="searchAllDaftarFasilitas" placeholder="Nama Fasilitas" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_fasilitas( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:70%;">Nama Fasilitas</th>
                              <th style="width:20%;">Waktu Input</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_fasilitas">
                           <tr>
                              <td colspan="4">Daftar fasilitas tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_fasilitas"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_fasilitas_getData(){
   get_daftar_fasilitas(20);
}

function get_daftar_fasilitas(perpage){
   get_data( perpage,
             { url : 'Fasilitas/daftar_fasilitas',
               pagination_id: 'pagination_daftar_fasilitas',
               bodyTable_id: 'bodyTable_daftar_fasilitas',
               fn: 'ListDaftarFasilitas',
               warning_text: '<td colspan="4">Daftar fasilitas tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarFasilitas').val() } } );
}

function ListDaftarFasilitas(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.nama_fasilitas}</td>
                  <td>${json.input_date}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Fasilitas"
                        onclick="edit_fasilitas('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Fasilitas"
                        onclick="delete_fasilitas('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_fasilitas(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Fasilitas',
      theme: 'material',
      content: formaddupdate_fasilitas(),
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
                     get_daftar_fasilitas(20);
                  }
               });
            }
         }
      }
   });
}

function edit_fasilitas(id){
   ajax_x(
      baseUrl + "Fasilitas/get_info_edit_fasilitas", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Edit Fasilitas',
            theme: 'material',
            content: formaddupdate_fasilitas(JSON.stringify(e['data'])),
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
                           get_daftar_fasilitas(20);
                        }
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}

function delete_fasilitas(id){
   ajax_x(
      baseUrl + "Fasilitas/delete_fasilitas", function(e) {
         if( e['error'] == false ){
             get_daftar_fasilitas(20);
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

function formaddupdate_fasilitas(JSONValue){
   var id_fasilitas = '';
   var nama_fasilitas = '';
   var input_date = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_fasilitas = `<input type="hidden" name="id" value="${value.id}">`;
      nama_fasilitas = value.nama_fasilitas;
   }

   var html = `<form action="${baseUrl }Fasilitas/proses_addupdate_fasilitas" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id_fasilitas}
                                 <label>Nama Fasilitas</label>
                                 <input type="text" name="nama_fasilitas" value="${nama_fasilitas}" class="form-control form-control-sm" placeholder="Nama Fasilitas" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}
