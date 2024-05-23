function daftar_mobil_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarMobil">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_mobil()" title="Tambah mobil baru">
                        <i class="fas fa-car"></i> Tambah Mobil
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_mobil( 20)" id="searchAllDaftarMobil" name="searchAllDaftarMobil" placeholder="Nama Mobil" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_mobil( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:65%;">Nama Mobil</th>
                              <th style="width:25%;">Waktu Pembaharuan Terakhir</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_mobils">
                           <tr>
                              <td colspan="3">Daftar mobil tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_mobils"></div>
                  </div>
               </div>
            </div>`;
}


function daftar_mobil_getData(){
   get_daftar_mobil(20);
}

function get_daftar_mobil(perpage){
   get_data( perpage,
             { url : 'Daftar_mobil/daftar_mobils',
               pagination_id: 'pagination_daftar_mobils',
               bodyTable_id: 'bodyTable_daftar_mobils',
               fn: 'ListDaftarMobil',
               warning_text: '<td colspan="3">Daftar mobil tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarMobil').val() } } );
}

function ListDaftarMobil(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.car_name}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Mobil"
                        onclick="edit_car('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Mobil"
                        onclick="delete_car('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;

   return html;
}

function add_mobil(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Mobil',
      theme: 'material',
      content: formaddupdate_mobil(),
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
                     get_daftar_mobil(20);
                  }
               });
            }
         }
      }
   });
}

function edit_car(id){
   ajax_x(
      baseUrl + "Daftar_mobil/get_edit_info_car", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Kota',
               theme: 'material',
               content: formaddupdate_mobil(JSON.stringify(e['data'])),
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
                              get_daftar_mobil(20);
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

function delete_car(id){
   ajax_x(
      baseUrl + "Daftar_mobil/delete_car", function(e) {
         if( e['error'] == false ){
             get_daftar_mobil(20);
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

function formaddupdate_mobil(JSONValue){
   var car_id = '';
   var car_name = '';
   if(JSONValue != undefined){
      var value = JSON.parse(JSONValue);
      car_id = `<input type="hidden" name="id" value="${value.id}">`;
      car_name = value.car_name;
   }
   var html = `<form action="${baseUrl }Daftar_mobil/proses_addupdate_daftar_mobil" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${car_id}
                                 <label>Nama Mobil</label>
                                 <input type="text" name="nama_mobil" value="${car_name}" class="form-control form-control-sm" placeholder="Nama Mobil" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}
