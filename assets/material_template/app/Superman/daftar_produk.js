function daftar_produk_Pages(){
	return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-4 my-3 ">
                     <button class="btn btn-default" type="button" onclick="addProduk()">
                        <i class="fas fa-money-bill"></i> Tambah Produk
                     </button>
                     <button class="btn btn-default" type="button" onclick="MarkupMasssalProduk()">
                        <i class="fas fa-money-bill"></i> Markup
                     </button>
                     <button class="btn btn-default" type="button" onclick="UpdateHargaProduk()">
                        <i class="fas fa-money-bill"></i> Update Harga
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3 ">
                     <div class="form-group">
                        <select class="form-control form-control-sm" id="category_operator_product" title="Pilih Kategori Produk">
                           <option value="0">Pilih Kategori Operator</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 ">
                     <div class="form-group">
                        <select class="form-control form-control-sm" id="status_product" title="Pilih Status Produk">
                           <option value="pilih_semua">Pilih Status</option>
                           <option value="active">Aktif</option>
                           <option value="inactive">Inaktif</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 ">
                     <div class="form-group">
                        <select class="form-control form-control-sm" id="server_product" title="Pilih Server Produk">
                           <option value="pilih_semua">Pilih Server</option>
                           <option value="tripay">Tripay</option>
                           <option value="iak">Iak</option>
                           <option value="none">None</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" id="searchAllDaftarProduk" name="searchAllDaftarProduk" placeholder="Kode / Nama / Operator Produk" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_produk(300)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:5%;">Kode Produk</th>
                              <th style="width:20%;">Nama Produk</th>
                              <th style="width:15%;">Operator</th>
                              <th style="width:10%;">Harga</th>
                              <th style="width:10%;">Markup Perusahaan</th>
                              <th style="width:10%;">Status</th>
                              <th style="width:10%;">Server</th>
                              <th style="width:7%;">Diperbaharui</th>
                              <th style="width:13%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_produk">
                           <tr>
                              <td colspan="9">Daftar produk tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_produk"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_produk_getData() {
   get_parameter_product();
   get_daftar_produk(300);
}

function get_parameter_product(){
   ajax_x(
      baseUrl + "Superman/PPOB/get_parameter_product", function(e) {
         if( e['error'] == false ) {
            var html = `<option value="0">Pilih Kategori Operator</option>`;
            for( x in e.data ){
               html += `<option value="${e.data[x].id}">${e.data[x].name}</option>`;
            }
            $('#category_operator_product').html(html);
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function get_daftar_produk(perpage){
   get_data(perpage, {
      url: "Superman/PPOB/daftar_produk_amra",
      pagination_id: "pagination_daftar_produk",
      bodyTable_id: "bodyTable_daftar_produk",
      fn: "ListDaftarProduk",
      warning_text: '<td colspan="9">Daftar produk tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarProduk').val(), 
               category_operator: $('#category_operator_product').val(),
               status_product: $('#status_product').val(),
               server_product: $('#server_product').val(),  } ,
   });
}

function ListDaftarProduk( JSONData ) {
   var json = JSON.parse(JSONData);
   return `<tr>
               <td>${json.product_code}</td>
               <td>${json.product_name}</td>
               <td>${json.operator_name}</td>
               <td>Rp ${numberFormat(json.price)}</td>
               <td>Rp ${numberFormat(json.application_markup)}</td>
               <td><b style="text-transform:uppercase;color:${json.status=='active' ? 'green' : 'red'}">${json.status}</b></td>
               <td><b style="text-transform:uppercase;color:${json.server == '' || json.server == 'none' ? "red" : (json.server == 'iak' ? 'orange' : 'blue')}">${json.server}</b></td>
               <td>${json.updated_at}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Markup Produk" onclick="markupProduk(${
                       json.id
                     })" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-money-bill" style="font-size: 11px;"></i>
                   </button>
                  <button type="button" class="btn btn-default btn-action" title="Edit Produk" onclick="editProduk(${
                       json.id
                     })" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Produk" onclick="deleteProduk(${
                       json.id
                     })" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
           </tr>`;
}

// markup produk
function markupProduk(id){
   ajax_x(
      baseUrl + "Superman/PPOB/get_info_markup_produk", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-4',
               title: 'MarkUp Produk',
               theme: 'material',
               content: formaddupdatemarkup( JSON.stringify( e.data ) ),
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
                              // daftar_produk_getData();
                              get_daftar_produk(300)
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

// form add update markup
function formaddupdatemarkup(JSONValue){
   var product_code = '';
   var product_name = '';
   var markup = '';
   var id = '';
   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" value="${value.id}">`;
      product_code = value.product_code;
      product_name = value.product_name;
      markup = 'Rp ' + numberFormat(value.application_markup);
   }
   var html = `<form action="${baseUrl }Superman/PPOB/proses_update_markup" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id}
                                 <label>Kode Produk</label>
                                 <input type="text" disabled value="${product_code}" class="form-control form-control-sm" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Produk</label>
                                 <textarea class="form-control form-control-sm" disabled id="exampleFormControlTextarea1" rows="3" style="resize:none;" > ${product_name} </textarea>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Markup Produk</label>
                                 <input type="text" value="${markup}" name="markup" class="form-control form-control-sm currency" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $(document).on( "keyup", ".currency", function(e){
                      var e = window.event || e;
                      var keyUnicode = e.charCode || e.keyCode;
                          if (e !== undefined) {
                              switch (keyUnicode) {
                                  case 16: break;
                                  case 27: this.value = ''; break;
                                  case 35: break;
                                  case 36: break;
                                  case 37: break;
                                  case 38: break;
                                  case 39: break;
                                  case 40: break;
                                  case 78: break;
                                  case 110: break;
                                  case 190: break;
                                  default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                              }
                          }
                  } );
               </script>`;
   return html;
}

// add update produk
function addProduk(){
   ajax_x(
      baseUrl + "Superman/PPOB/get_info_add_update_produk", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-6',
               title: 'Tambah Produk',
               theme: 'material',
               content: formaddupdateproduk(JSON.stringify( e.data ) ),
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
                              get_daftar_produk(300)
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

// form add update produk
function formaddupdateproduk( JSONData, JSONValue ) {
   var json = JSON.parse(JSONData);
   var list_produk = json;
   var id = '';
   var product_code = '';
   var product_name = '';
   var operator_id = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" value="${value.id}">`;
      product_code = value.product_code;
      product_name = value.product_name;
      operator_id = value.operator_id;
   }
   var html = `<form action="${baseUrl }Superman/PPOB/proses_add_update_produk" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id}
                                 <label>List Operator</label>
                                 <select class="form-control form-control-sm sel" name="operator">`;
                        for( x in list_produk ) {
                           html +=  `<option value="${list_produk[x].id}" ${operator_id == list_produk[x].id ? 'selected' : ''}>( ${list_produk[x].category_code} : ${list_produk[x].category_name} )  ${list_produk[x].operator_name}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Kode Produk</label>
                                 <input type="text" name="kode" value="${product_code}" class="form-control form-control-sm" placeholder="Kode Produk"/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Produk</label>
                                 <textarea name="name" class="form-control form-control-sm" rows="3" style="resize:none;" placeholder="Nama Produk">${product_name}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $(".sel").select2({
                     tags: true,
                     dropdownParent: $(".jconfirm")
                  });
               </script>`;
   return html;
}

// edit produk
function editProduk(id){
   ajax_x(
      baseUrl + "Superman/PPOB/get_info_edit_update_produk", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Produk',
               theme: 'material',
               content: formaddupdateproduk(JSON.stringify( e.data ), JSON.stringify( e.value ) ),
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
                              get_daftar_produk(300)
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

// delete produk
function deleteProduk(id) {
    ajax_x(
      baseUrl + "Superman/PPOB/delete_produk", function(e) {
         if( e['error'] == false ){
            smile_alert(e['error_msg']);
            get_daftar_produk(300)
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function MarkupMasssalProduk(){
    ajax_x(
      baseUrl + "Superman/PPOB/get_info_add_update_produk", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-6',
               title: 'Markup Massal Produk',
               theme: 'material',
               content: formmarkupmassalproduk( JSON.stringify(e.data) ),
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
                              get_daftar_produk(300)
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

function formmarkupmassalproduk(JSONData){
   var json = JSON.parse(JSONData);
   var list_produk = json;
   var html = `<form action="${baseUrl }Superman/PPOB/proses_markup_massal_produk" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>List Operator</label>
                                 <select class="form-control form-control-sm sel" name="operator">
                                    <option value="0">Pilih Semua Operator</option>`;
                        for( x in list_produk ) {
                           html +=  `<option value="${list_produk[x].id}">
                                       ( ${list_produk[x].category_code} : ${list_produk[x].category_name} )  ${list_produk[x].operator_name}
                                    </option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Range Harga Produk 1 - 10.000</label>
                                 <input type="text" class="form-control form-control-sm" disabled value="1 - 10.000"/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nominal Markup</label>
                                 <input type="text" name="nominal_1" class="form-control form-control-sm currency" placeholder="1 - 10.000"/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Range Harga Produk 10.001 - 50.000</label>
                                 <input type="text" class="form-control form-control-sm" disabled value="10.001 - 50.000"/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nominal Markup</label>
                                 <input type="text" name="nominal_2" class="form-control form-control-sm currency" placeholder="10.001 - 50.000"/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Range Harga Produk 50.001 - 100.000</label>
                                 <input type="text" class="form-control form-control-sm" disabled value="50.001 - 100.000"/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nominal Markup</label>
                                 <input type="text" name="nominal_3" class="form-control form-control-sm currency" placeholder="50.001 - 100.000"/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Range Harga Produk 100.001 - 300.000</label>
                                 <input type="text" class="form-control form-control-sm" disabled value="100.001 - 300.000"/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nominal Markup</label>
                                 <input type="text" name="nominal_4" class="form-control form-control-sm currency" placeholder="100.001 - 300.000"/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Range Harga Produk 300.001 - 500.000</label>
                                 <input type="text" class="form-control form-control-sm" disabled value="300.001 - 500.000"/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nominal Markup</label>
                                 <input type="text" name="nominal_5" class="form-control form-control-sm currency" placeholder="300.001 - 500.000"/>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Range Harga Produk > 500.001</label>
                                 <input type="text" class="form-control form-control-sm" disabled value="> 500.001"/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nominal Markup</label>
                                 <input type="text" name="nominal_6" class="form-control form-control-sm currency" placeholder="> 500.001"/>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $(document).on( "keyup", ".currency", function(e){
                      var e = window.event || e;
                      var keyUnicode = e.charCode || e.keyCode;
                          if (e !== undefined) {
                              switch (keyUnicode) {
                                  case 16: break;
                                  case 27: this.value = ''; break;
                                  case 35: break;
                                  case 36: break;
                                  case 37: break;
                                  case 38: break;
                                  case 39: break;
                                  case 40: break;
                                  case 78: break;
                                  case 110: break;
                                  case 190: break;
                                  default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                              }
                          }
                  } );
               </script>`;
   return html;
}

// update harga produk
function UpdateHargaProduk(){
     ajax_x(
      baseUrl + "Superman/PPOB/update_harga_produk", function(e) {
          if( e['error'] == false ) {
            smile_alert(e['error_msg'])
            get_daftar_produk(300)
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}
