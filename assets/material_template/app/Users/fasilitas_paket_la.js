function fasilitas_paket_la_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarFasilitasLA">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_fasilitas_la()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Fasilitas LA
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_fasilitas_la( 20)" id="searchAllDaftarFasilitasLA" name="searchAllDaftarFasilitasLA" placeholder="Nama Fasilitas LA" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_fasilitas_la( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:40%;">Nama Fasilitas LA</th>
                              <th style="width:18%;">Header Fasilitas LA</th>
                              <th style="width:12%;">Harga</th>
                              <th style="width:20%;">Waktu Pembaharuan</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_fasilitas_la">
                           <tr>
                              <td colspan="5">Daftar fasilitas LA tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_fasilitas_la"></div>
                  </div>
               </div>
            </div>`;
}

function fasilitas_paket_la_getData(){
   get_daftar_fasilitas_la(20);
}

function get_daftar_fasilitas_la(perpage){
   get_data( perpage,
             { url : 'Fasilitas_la/daftar_fasilitas_la',
               pagination_id: 'pagination_daftar_fasilitas_la',
               bodyTable_id: 'bodyTable_daftar_fasilitas_la',
               fn: 'ListDaftarFasilitasLA',
               warning_text: '<td colspan="5">Daftar fasilitas LA tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarFasilitasLA').val() } } );
}

function ListDaftarFasilitasLA(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.nama_fasilitas}</td>
                  <td>${json.header}</td>
                  <td>${kurs} ${numberFormat(json.price)}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Fasilitas LA"
                        onclick="edit_fasilitas_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Fasilitas LA"
                        onclick="delete_fasilitas_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_fasilitas_la(){
   ajax_x(
      baseUrl + "Fasilitas_la/get_info_fasilitas_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Fasilitas LA',
               theme: 'material',
               content: formaddupdate_fasilitas_la(JSON.stringify(e['header'])),
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
                              get_daftar_fasilitas_la(20);
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

function formaddupdate_fasilitas_la(header, JSONValue){
   var list_header = JSON.parse(header);
   var id_fasilitas_la = '';
   var header_selected = '';
   var nama_fasilitas_la = '';
   var harga_fasilitas_la = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_fasilitas_la = `<input type="hidden" name="id" value="${value.id}">`;
      header_selected = value.header_id;
      nama_fasilitas_la = value.nama_fasilitas_la;
      harga_fasilitas_la = kurs + ' ' + numberFormat(value.price);
   }
   var html = `<form action="${baseUrl }Fasilitas_la/proses_addupdate_fasilitas_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id_fasilitas_la}
                                 <label>Nama Fasilitas</label>
                                 <select class="form-control form-control-sm" name="header">`;
                           for( x in list_header ) {
                              html += `<option value="${list_header[x]['id']}" ${ header_selected == list_header[x]['id'] ? 'selected' : ''}>${list_header[x]['header_name']}</option>`;
                           }
                     html +=    `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 ${id_fasilitas_la}
                                 <label>Nama Fasilitas</label>
                                 <input type="text" name="nama_fasilitas_la" value="${nama_fasilitas_la}" class="form-control form-control-sm" placeholder="Nama Fasilitas LA" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Harga</label>
                                 <input type="text" name="harga_fasilitas_la" value="${harga_fasilitas_la}" class="form-control form-control-sm currency" placeholder="Harga Fasilitas LA" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
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

function edit_fasilitas_la(id){
   ajax_x(
      baseUrl + "Fasilitas_la/get_info_edit_fasilitas_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Fasilitas LA',
               theme: 'material',
               content: formaddupdate_fasilitas_la(JSON.stringify(e['header']), JSON.stringify(e['data'])),
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
                              get_daftar_fasilitas_la(20);
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

function delete_fasilitas_la(id){

   ajax_x(
      baseUrl + "Fasilitas_la/delete_fasilitas_la", function(e) {
         if( e['error'] == false ){
             get_daftar_fasilitas_la(20);
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
