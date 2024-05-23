function daftar_asuransi_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarProviderVisa">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_asuransi()" title="Tambah asuransi baru">
                        <i class="fab fa-cc-visa"></i> Tambah Asuransi
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_asuransi(20)" id="searchAsuransi" name="searchAsuransi" placeholder="Nama Asuransi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_asuransi(20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:65%;">Nama Asuransi</th>
                              <th style="width:25%;">Waktu Pembaharuan Terakhir</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_asuransi">
                           <tr>
                              <td colspan="3">Daftar  tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_asuransi"></div>
                  </div>
               </div>
            </div>`;
}


function daftar_asuransi_getData(){
   get_daftar_asuransi(20);
}

function get_daftar_asuransi(perpage){
   get_data( perpage,
             { url : 'Daftar_asuransi/daftar_asuransi_server',
               pagination_id: 'pagination_daftar_asuransi',
               bodyTable_id: 'bodyTable_daftar_asuransi',
               fn: 'ListDaftarAsuransi',
               warning_text: '<td colspan="3">Daftar asuransi tidak ditemukan</td>',
               param : { search : $('#searchAsuransi').val() } } );
}

function ListDaftarAsuransi(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.nama_asuransi} </td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Asuransi"
                        onclick="edit_asuransi('${json.id}', 'daftar_paket')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Asuransi"
                        onclick="delete_asuransi('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_asuransi(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Asuransi',
      theme: 'material',
      content: formaddupdateAsuransi(),
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
                     get_daftar_asuransi(20);
                  }
               });
            }
         }

      }
   });
}

// edit asuransi
function edit_asuransi(id){
   ajax_x(
      baseUrl + "Daftar_asuransi/info_edit_asuransi", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Edit Asuransi',
            theme: 'material',
            content: formaddupdateAsuransi(JSON.stringify( e['data'] ) ),
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
                           get_daftar_asuransi(20);
                        }
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}

// form add update asuransi
function formaddupdateAsuransi(JSONValue){
   var id = '';
   var nama = '';
   if( JSONValue != undefined ){
      value = JSON.parse( JSONValue );
      id = `<input type="hidden" name="id" value="${value.id}">`;
      nama = value.nama;
   }
   var html = `<form action="${baseUrl }Daftar_asuransi/proses_addupdate_asuransi" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${id}
                                 <label>Nama Asuransi</label>
                                 <input type="text" name="nama" value="${nama}" class="form-control form-control-sm" placeholder="Nama Asuransi" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

// delete asuransi
function delete_asuransi(id){
   ajax_x(
      baseUrl + "Daftar_asuransi/delete_asuransi", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if ( e['error'] == true ) {
            return false;
         } else {
            get_daftar_asuransi(20);
         }
      },[{id:id}]
   );
}