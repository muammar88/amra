function daftar_provider_visa_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarProviderVisa">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_provider_visa()" title="Tambah provider visa baru">
                        <i class="fab fa-cc-visa"></i> Tambah Provider Visa
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_provider_visa(20)" id="searchProviderVisa" name="searchProviderVisa" placeholder="Nama Provider Visa" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_provider_visa(20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:65%;">Nama Provider</th>
                              <th style="width:25%;">Waktu Pembaharuan Terakhir</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_provider_visa">
                           <tr>
                              <td colspan="3">Daftar provider visa tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_provider_visa"></div>
                  </div>
               </div>
            </div>`;
}


function daftar_provider_visa_getData(){
   get_daftar_provider_visa(20);
}

function get_daftar_provider_visa(perpage){
   get_data( perpage,
             { url : 'Daftar_provider_visa/daftar_provider_visas',
               pagination_id: 'pagination_daftar_provider_visa',
               bodyTable_id: 'bodyTable_daftar_provider_visa',
               fn: 'ListDaftarProviderVisa',
               warning_text: '<td colspan="3">Daftar provider visa tidak ditemukan</td>',
               param : { search : $('#searchProviderVisa').val() } } );
}

function ListDaftarProviderVisa(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.nama_provider} </td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Provider Visa"
                        onclick="edit_provider_visa('${json.id}', 'daftar_paket')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Provider"
                        onclick="delete_provider_visa('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_provider_visa(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Provider Visa',
      theme: 'material',
      content: formaddupdateProviderVisa(),
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
                     get_daftar_provider_visa(20);
                  }
               });
            }
         }

      }
   });
}

function edit_provider_visa(id){
   ajax_x(
      baseUrl + "Daftar_provider_visa/info_edit_provider_visa", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Edit Provider Visa',
            theme: 'material',
            content: formaddupdateProviderVisa(JSON.stringify( e['data'] ) ),
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
                           get_daftar_provider_visa(20);
                        }
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}

function formaddupdateProviderVisa(JSONValue){

   var id = '';
   var nama = '';
   if( JSONValue != undefined ){
      value = JSON.parse( JSONValue );
      id = `<input type="hidden" name="id" value="${value.id}">`;
      nama = value.nama;
   }

    var html = `<form action="${baseUrl }Daftar_provider_visa/proses_addupdate_provider_visa" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${id}
                                 <label>Nama Provider Visa</label>
                                 <input type="text" name="nama" value="${nama}" class="form-control form-control-sm" placeholder="Nama Provider Visa" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

// delete provider visa
function delete_provider_visa(id){
   ajax_x(
      baseUrl + "Daftar_provider_visa/delete_provider_visa", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if ( e['error'] == true ) {
            return false;
         } else {
            get_daftar_provider_visa(20);
         }
      },[{id:id}]
   );
}