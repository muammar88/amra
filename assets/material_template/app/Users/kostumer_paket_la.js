function kostumer_paket_la_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentKostumerPaketLA">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_kostumer_paket_la()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Kostumer Baru
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_kostumer_paket_la( 20)" id="searchAllKostumerPaketLA" name="searchAllKostumerPaketLA" placeholder="Nama Kostumer" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_kostumer_paket_la( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:15%;">Nama Kostumer</th>
                              <th style="width:15%;">Nomor HP</th>
                              <th style="width:25%;">Alamat</th>
                              <th style="width:35%;">Info Pembayaran</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_kostumer_paket_la">
                           <tr>
                              <td colspan="5">Daftar Kostumer Paket LA tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_kostumer_paket_la"></div>
                  </div>
               </div>
            </div>`;
}

function kostumer_paket_la_getData(){
   get_kostumer_paket_la(20);
}

function get_kostumer_paket_la(perpage){
   get_data( perpage,
             { url : 'Kostumer_paket_la/server_side',
               pagination_id: 'pagination_kostumer_paket_la',
               bodyTable_id: 'bodyTable_kostumer_paket_la',
               fn: 'ListServerSideKostumerPaketLA',
               warning_text: '<td colspan="5">Daftar Kostumer Paket LA tidak ditemukan</td>',
               param : { search : $('#searchAllKostumerPaketLA').val() } } );
}

function ListServerSideKostumerPaketLA(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.name}</td>
                  <td>${json.mobile_number}</td>
                  <td>${json.address}</td>
                  <td></td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Kostumer Paket LA"
                        onclick="edit_kostumer_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Kostumer Paket LA"
                        onclick="delete_kostumer_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_kostumer_paket_la(){
   $.confirm({
      columnClass: 'col-5',
      title: 'Tambah Kostumer Paket LA',
      theme: 'material',
      content: formaddupdate_kostumer_paket_la(),
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
                     get_kostumer_paket_la(20);
                  }
               });
            }
         }

      }
   });
}


function edit_kostumer_paket_la(id){
   ajax_x(
      baseUrl + "Kostumer_paket_la/get_info_edit", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Airlines',
               theme: 'material',
               content: formaddupdate_kostumer_paket_la(JSON.stringify(e['data'])),
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
                              get_kostumer_paket_la(20);
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


function formaddupdate_kostumer_paket_la(JSONValue){
   var id = '';
   var name = '';
   var mobile_number = '';
   var address = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" value="${value.id}">`;
      name = value.name;
      mobile_number = value.mobile_number;
      address = value.address;
   }
   var html = `<form action="${baseUrl }Kostumer_paket_la/proses_addupdate_kostumer_paket_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        ${id}
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Name</label>
                                 <input type="text"  name="name" value="${name}" class="form-control form-control-sm" placeholder="Nama" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nomor HP</label>
                                 <input type="text" name="mobile_number" value="${mobile_number}" class="form-control form-control-sm" placeholder="Nomor Hp" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alamat</label>
                                 <textarea class="form-control form-control-sm" name="address" id="address" placeholder="Alamat" rows="7" style="resize: none;">${address}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;

}


function delete_kostumer_paket_la(id){
   ajax_x(
      baseUrl + "Kostumer_paket_la/delete", function(e) {
         if( e['error'] == false ){
             get_kostumer_paket_la(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}

