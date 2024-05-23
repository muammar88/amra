
function daftar_bandara_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarTiket">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_bandara()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Bandara
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_bandara( 20)" id="searchAllDaftarBandara" name="searchAllDaftarBandara" placeholder="Nama atau Kota Bandara" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_bandara( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:45%;">Nama Bandara</th>
                              <th style="width:45%;">Nama Kota</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_bandara">
                           <tr>
                              <td colspan="3">Daftar bandara tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_bandara"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_bandara_getData(){
   get_daftar_bandara(20);
}

function get_daftar_bandara(perpage){
   get_data( perpage,
             { url : 'Daftar_bandara/daftar_bandaras',
               pagination_id: 'pagination_daftar_bandara',
               bodyTable_id: 'bodyTable_daftar_bandara',
               fn: 'ListDaftarBandara',
               warning_text: '<td colspan="3">Daftar bandara tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarBandara').val() } } );
}

function ListDaftarBandara(JSONData){
   var json = JSON.parse(JSONData);

   return `<tr>
               <td>${json.airport_name}</td>
               <td>${json.city_name}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Edit Bandara"
                     onclick="edit_bandara('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Bandara"
                     onclick="delete_bandara('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
           </tr>`;
}


function add_bandara(){
   ajax_x(
      baseUrl + "Daftar_bandara/get_info_addupdate_bandara", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Bandara',
               theme: 'material',
               content: formaddupdate_bandara(JSON.stringify(e['data'])),
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
                           e['error'] == true ? frown_alert(e['error_msg']) :smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_bandara( 20 );
                           }
                        });
                     }
                  }

               }
            });
         }else{
           forwn_alert(e['error_msg']);
         }
      },[]
   );
}

function edit_bandara( id ){
   ajax_x(
      baseUrl + "Daftar_bandara/get_info_edit_bandara", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Bandara',
               theme: 'material',
               content: formaddupdate_bandara(JSON.stringify(e['data']), JSON.stringify(e['value']) ),
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
                           e['error'] == true ? frown_alert(e['error_msg']) :smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_bandara( 20 );
                           }
                        });
                     }
                  }

               }
            });
         }else{
           forwn_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}


function formaddupdate_bandara(JSONData, JSONValue){
   var list_city = JSON.parse(JSONData);
   var id_bandara = '';
   var nama_bandara = '';
   var city_id = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_bandara = `<input type="hidden" name="id" value="${value.id}">`;
      nama_bandara = value.airport_name;
      city_id = value.city_id;
   }
   var html = `<form action="${baseUrl }Daftar_bandara/proses_addupdate_bandara" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           ${id_bandara}
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Bandara</label>
                                 <input type="text" name="nama_bandara" value="${nama_bandara}" class="form-control form-control-sm" placeholder="Nama Bandara" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Kota</label>
                                 <select class="form-control form-control-sm" id="city" name="city">`;
                  for( x in list_city ){
                     console.log(list_city[x].id);
                     html += `<option value="${list_city[x].id}" ${ city_id == list_city[x].id ? 'selected' : '' } >${list_city[x].city_name} (${list_city[x].city_code})</option>`;
                  }
               html +=          `</select>
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function delete_bandara( id ) {
   ajax_x(
      baseUrl + "Daftar_bandara/delete_daftar_bandara", function(e) {
         if( e['error'] == false ){
             get_daftar_bandara( 20 );
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
