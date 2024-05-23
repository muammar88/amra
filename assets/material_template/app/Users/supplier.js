function supplier_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarSupplier">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_supplier()" title="Tambah supplier baru">
                        <i class="fas fa-people-carry"></i> Tambah Supplier
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_supplier( 20)" id="searchAllDaftarSupplier" name="searchAllDaftarSupplier" placeholder="Nama Supplier" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_supplier( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:40%;">Nama Supplier</th>
                              <th style="width:25%;">Nama Bank/Nomor Rekening</th>
                              <th style="width:25%;">Alamat</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_supplier">
                           <tr>
                              <td colspan="4">Daftar Supplier tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_supplier"></div>
                  </div>
               </div>
            </div>`;
}

function supplier_getData(){
   get_supplier(20);
}

function get_supplier(perpage){
   get_data( perpage,
             { url : 'Supplier/daftar_supplier',
               pagination_id: 'pagination_supplier',
               bodyTable_id: 'bodyTable_supplier',
               fn: 'ListDaftarSupplier',
               warning_text: '<td colspan="3">Daftar supplier tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarSupplier').val() } } );
}

function ListDaftarSupplier(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.supplier_name}</td>
                  <td>(${json.nama_bank}) ${json.rekening_number}</td>
                  <td>${json.address}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit supplier"
                        onclick="edit_supplier('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete supplier"
                        onclick="delete_supplier('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_supplier(){
   ajax_x(
      baseUrl + "Supplier/get_info_supplier", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Supplier',
               theme: 'material',
               content: formaddupdate_supplier(JSON.stringify(e['bank'])),
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
                              get_supplier(20);
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

function formaddupdate_supplier(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   var id_supplier = '';
   var nama_supplier = '';
   var alamat_supplier = '';
   var nomor_rekening = '';
   var selected_bank = '';
   if(JSONValue != undefined){
      var value = JSON.parse(JSONValue);
      id_supplier = `<input type="hidden" name="id" value="${value.id}">`;
      nama_supplier = value.supplier_name;
      alamat_supplier = value.address;
      selected_bank = value.bank_id;
      nomor_rekening = value.rekening_number;
   }
   var html = `<form action="${baseUrl }Supplier/proses_addupdate_supplier" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${id_supplier}
                                 <label>Nama Supplier</label>
                                 <input type="text" name="nama_supplier" value="${nama_supplier}" class="form-control form-control-sm" placeholder="Nama Supplier" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Alamat Supplier</label>
                                 <textarea class="form-control form-control-sm" id="alamat_supplier" name="alamat_supplier" rows="3" style="resize:none;">${alamat_supplier}</textarea>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                <label for="exampleSelect1" class="form-label">Bank</label>
                                <select class="form-control form-control-sm" id="bank" name="bank">`;
                     for(x in json){
                        html  += `<option value="${json[x]['id']}" ${ selected_bank == json[x]['id'] ? 'selected' : ''}>${json[x]['nama_bank']}</option>`;
                     }
                     html  +=  `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nomor Rekening</label>
                                 <input type="number" name="nomor_rekening" value="${nomor_rekening}" class="form-control form-control-sm" placeholder="Nomor Rekening Supplier" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function edit_supplier(id){
   ajax_x(
      baseUrl + "Supplier/get_edit_info_supplier", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Supplier',
               theme: 'material',
               content: formaddupdate_supplier(JSON.stringify(e['bank']), JSON.stringify(e['data'])),
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
                              get_supplier(20);
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


function delete_supplier(id){
   ajax_x(
      baseUrl + "Supplier/delete_supplier", function(e) {
         if( e['error'] == false ){
            get_supplier(20);
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'green',
         });


         // if( e['error'] == false ){
         //
         // }else{
         //    $.alert({
         //       icon: 'far fa-frown',
         //       title: 'Peringatan',
         //       content: e['error_msg'],
         //       type: 'red'
         //    });
         // }
      },[{id:id}]
   );
}
