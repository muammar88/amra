function daftar_kota_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarKota">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_kota()" title="Tambah kota baru">
                        <i class="fas fa-city"></i> Tambah Kota
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_kota( 20)" id="searchAllDaftarKota" name="searchAllDaftarKota" placeholder="Nama Kota" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_kota( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:75%;">Nama Kota</th>
                              <th style="width:15%;">Kode Kota</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_kota">
                           <tr>
                              <td colspan="3">Daftar Kota tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_kota"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_kota_getData(){
   get_daftar_kota(20);
}

function get_daftar_kota(perpage){
   get_data( perpage,
             { url : 'Daftar_kota/daftar_kotas',
               pagination_id: 'pagination_daftar_kota',
               bodyTable_id: 'bodyTable_daftar_kota',
               fn: 'ListDaftarKota',
               warning_text: '<td colspan="3">Daftar kota tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarKota').val() } } );
}

function ListDaftarKota(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.city_name}</td>
                  <td>${json.city_code}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Kota"
                        onclick="edit_city('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Kota"
                        onclick="delete_city('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_kota(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Kota',
      theme: 'material',
      content: formaddupdate_kota(),
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
                     get_daftar_kota(20);
                  }
               });
            }
         }
      }
   });
}

function formaddupdate_kota(JSONValue){
   var city_id = '';
   var city_name = '';
   var city_code = '';
   if(JSONValue != undefined){
      var value = JSON.parse(JSONValue);
      city_id = `<input type="hidden" name="id" value="${value.id}">`;
      city_name = value.city_name;
      city_code = value.city_code;
   }
   var html = `<form action="${baseUrl }Daftar_kota/proses_addupdate_daftar_kota" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${city_id}
                                 <label>Nama Kota</label>
                                 <input type="text" name="nama_kota" value="${city_name}" class="form-control form-control-sm" placeholder="Nama Kota" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>Kode Kota</label>
                                 <input type="text" name="kode_kota" value="${city_code}" class="form-control form-control-sm" placeholder="Kode Kota" maxlength="3" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function edit_city(id){
   ajax_x(
      baseUrl + "Daftar_kota/get_edit_info_city", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Kota',
               theme: 'material',
               content: formaddupdate_kota(JSON.stringify(e['data'])),
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
                              get_daftar_kota(20);
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

function delete_city(id){
   ajax_x(
      baseUrl + "Daftar_kota/delete_city", function(e) {
         if( e['error'] == false ){
             get_daftar_kota(20);
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
