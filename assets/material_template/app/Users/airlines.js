function airlines_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_airlines()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Airlines
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_airlines( 20)" id="searchAllDaftarAirlines" name="searchAllDaftarAirlines" placeholder="Nama Airlines" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_airlines( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:90%;">Nama Airlines</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_airlines">
                           <tr>
                              <td colspan="2">Daftar airlines tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_airlines"></div>
                  </div>
               </div>
            </div>`;
}

function airlines_getData(){
   get_daftar_airlines(20);
}

function get_daftar_airlines(perpage){
   get_data( perpage,
             { url : 'Airlines/daftar_airlines',
               pagination_id: 'pagination_daftar_airlines',
               bodyTable_id: 'bodyTable_daftar_airlines',
               fn: 'ListDaftarAirlines',
               warning_text: '<td colspan="4">Daftar airlines tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarAirlines').val() } } );
}

function ListDaftarAirlines(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.nama_airlines}</td>

                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Airlines"
                        onclick="edit_airlines('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Airlines"
                        onclick="delete_airlines('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_airlines(){
   $.confirm({
      columnClass: 'col-4',
      title: 'Tambah Airlines',
      theme: 'material',
      content: formaddupdate_airlines(),
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
                     get_daftar_airlines(20);
                  }
               });
            }
         }

      }
   });
}

function formaddupdate_airlines(JSONValue){
   var id_airlines = '';
   var nama_airlines = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_airlines = `<input type="hidden" name="id" value="${value.id}">`;
      nama_airlines = value.airlines_name;
   }

   var html = `<form action="${baseUrl }Airlines/proses_addupdate_airlines" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id_airlines}
                                 <label>Nama Airlines</label>
                                 <input type="text" name="nama_airlines" value="${nama_airlines}" class="form-control form-control-sm" placeholder="Nama Airlines" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function edit_airlines(id){
   ajax_x(
      baseUrl + "Airlines/get_info_edit_airlines", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Airlines',
               theme: 'material',
               content: formaddupdate_airlines(JSON.stringify(e['data'])),
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
                              get_daftar_airlines(20);
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

// delete data airlines
function delete_airlines(id){
   ajax_x(
      baseUrl + "Airlines/delete_airlines", function(e) {
         if( e['error'] == false ){
             get_daftar_airlines(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
