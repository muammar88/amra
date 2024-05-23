function formaddupdate_bank(kodeBank,JSONValue){
   var id_bank = '';
   var nama_bank = '';
   var kode_bank = '';
   var path = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_bank = `<input type="hidden" name="id" value="${value.id}">`;
      nama_bank = value.nama_bank;
      kode_bank= `<input type="text" class="form-control form-control-sm" value="${value.kode_bank}" readonly />`;
      // path = `bank:asset:${value.kode_bank}`;
   } else {
      kode_bank= `<input type="text" class="form-control form-control-sm" value="${kodeBank}" readonly />
                  <input type="hidden" name="kode_bank" value="${kodeBank}" >`;
      // path = `bank:asset:${kodeBank}`;
   }

   var html = `<form action="${baseUrl }Daftar_bank/proses_addupdate_bank" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Kode Bank</label>
                                 ${id_bank}
                                 ${kode_bank}
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Bank</label>
                                 <input type="text" name="nama_bank" value="${nama_bank}" class="form-control form-control-sm" placeholder="Nama Bank" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}
// <div class="col-8">
//    <div class="form-group">
//       <label>Path Asset</label>
//       <input type="text" class="form-control form-control-sm" value="${path}" readonly />
//    </div>
// </div>

function delete_bank(id){
   ajax_x(
      baseUrl + "Daftar_bank/delete_bank", function(e) {
         if( e['error'] == false ){
            get_daftar_bank(20);
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'blue',
         });
      },[{id:id}]
   );
}

function edit_bank(id){
   ajax_x(
      baseUrl + "Daftar_bank/get_info_edit_bank", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Bank',
               theme: 'material',
               content: formaddupdate_bank('',JSON.stringify(e['data'])),
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
                              type: e['error'] == true ? 'red' :'blue'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_bank(20);
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

function add_bank(){
   ajax_x(
      baseUrl + "Daftar_bank/get_info_addupdate_bank", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Bank',
               theme: 'material',
               content: formaddupdate_bank(e['data']),
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
                              type: e['error'] == true ? 'red' :'blue'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_bank(20);
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
      },[]
   );
}

function daftar_bank_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarTiket">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_bank()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Bank
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_bank( 20)" id="searchAllDaftarBank" name="searchAllDaftarBank" placeholder="Kode Bank, Nama Bank" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_bank( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:15%;">Kode Bank</th>
                              <th style="width:75%;">Nama Bank</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_bank">
                           <tr>
                              <td colspan="3">Daftar bank tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_bank"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_bank_getData(){
   get_daftar_bank(20);
}

function get_daftar_bank(perpage){
   get_data( perpage,
             { url : 'Daftar_bank/daftar_banks',
               pagination_id: 'pagination_daftar_bank',
               bodyTable_id: 'bodyTable_daftar_bank',
               fn: 'ListDaftarBank',
               warning_text: '<td colspan="3">Daftar bank tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarBank').val() } } );
}

function ListDaftarBank(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.kode_bank}</td>
                  <td>${json.nama_bank}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Bank"
                        onclick="edit_bank('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Bank"
                        onclick="delete_bank('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}
