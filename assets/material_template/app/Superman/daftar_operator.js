function daftar_operator_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" >
                  <div class="col-6 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="addOperator()">
                        <i class="fas fa-money-bill"></i> Tambah Operator
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="daftar_operator_getData()" id="searchAllDaftarOperator" name="searchAllDaftarOperator" placeholder="Kode Operator" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="daftar_operator_getData()">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:30%;">Kategori</th>
                              <th style="width:30%;">Kode Operator</th>
                              <th style="width:30%;">Nama Operator</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_operator">
                           <tr>
                              <td colspan="4">Daftar operator tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_operator"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_operator_getData() {
  get_daftar_operator(50);
}

function get_daftar_operator(perpage){
   get_data(perpage, {
      url: "Superman/PPOB/daftar_operator_amra",
      pagination_id: "pagination_daftar_operator",
      bodyTable_id: "bodyTable_daftar_operator",
      fn: "ListDaftarOperator",
      warning_text: '<td colspan="4">Daftar operator tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarOperator').val() } ,
   });
}

function ListDaftarOperator(JSONData){
   var json = JSON.parse(JSONData);
   return  `<tr>
               <td>(${json.category_code}) ${json.category_name}</td>
               <td>${json.operator_code}</td>
               <td>${json.operator_name}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Edit Operator" 
                     onclick="editOperator(${json.id})" style="margin:.15rem .1rem  !important">
                     <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Operator" 
                     onclick="deleteOperator(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

// add operator
function addOperator() {
   ajax_x(
      baseUrl + "Superman/PPOB/get_info_operator", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Operator',
               theme: 'material',
               content: formaddupdateoperator( JSON.stringify( e.data ) ),
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
                              daftar_operator_getData();
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}


function formaddupdateoperator(JSONData, JSONValue){
   var json = JSON.parse(JSONData);

   var id = '';
   var category_id = '';
   var operator_code = '';
   var operator_name = '';

   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" value="${value.id}" name="id">`;
      category_id = value.category_id;
      operator_code = value.operator_code;
      operator_name = value.operator_name;
   }

   var html = `<form action="${baseUrl }Superman/PPOB/proses_update_operator" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           ${id}
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Kategori</label>
                                 <select class="form-control form-control-sm sel" name="category">`;
                        for( x in json ) {
                           html +=  `<option value="${json[x].id}" ${category_id == json[x].id ? 'selected' : ''}>( ${json[x].category_code} )  ${json[x].category_name}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Kode Operator</label>
                                 <input type="text" value="${operator_code}" name="kode" class="form-control form-control-sm" placeholder="Kode Operator" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Operator</label>
                                 <input type="text" value="${operator_name}" name="name" class="form-control form-control-sm" placeholder="Nama Operator"/>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}


function editOperator(id){
   ajax_x(
      baseUrl + "Superman/PPOB/get_info_edit_operator", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Operator',
               theme: 'material',
               content: formaddupdateoperator( JSON.stringify( e.data ), JSON.stringify( e.value ) ),
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
                              daftar_operator_getData();
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


function deleteOperator(id){
   ajax_x(
      baseUrl + "Superman/PPOB/delete_operator", function(e) {
         if( e['error'] == false ) {
            smile_alert(e['error_msg']);
            daftar_operator_getData();
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}