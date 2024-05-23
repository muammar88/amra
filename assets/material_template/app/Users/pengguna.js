function pengguna_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentPengguna">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_pengguna()">
                        <i class="fas fa-plus"></i> Tambah Pengguna
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" id="searchPengguna" name="searchPengguna" placeholder="Nama Pengguna" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_pengguna(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:35%;">Nama Lengkap / Nomor Whatsapp</th>
                              <th style="width:30%;">Nama Group</th>
                              <th style="width:25%;">Last Update</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_pengguna">
                           <tr>
                              <td colspan="4">Daftar pengguna tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_pengguna"></div>
                  </div>
               </div>
            </div>`;
}

function pengguna_getData(){
   get_pengguna(20);
}

function get_pengguna(perpage){
   get_data( perpage,
             { url : 'Pengguna/daftar_pengguna',
               pagination_id: 'pagination_pengguna',
               bodyTable_id: 'bodyTable_pengguna',
               fn: 'ListPengguna',
               warning_text: '<td colspan="4">Daftar pengguna tidak ditemukan</td>',
               param : { search : $('#searchPengguna').val() } } );
}

function ListPengguna(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.fullname} / ${json.nomor_whatsapp}</td>
                  <td>${json.nama_group}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Pengguna"
                        onclick="edit_pengguna('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Pengguna"
                        onclick="delete_pengguna('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

// edit pengguna
function edit_pengguna(id){
   ajax_x(
      baseUrl + "Pengguna/get_info_edit_pengguna", function(e) {
         if( e['error'] == false ) {
            simpleConfirmModalDialog({
               width:'col-4',
               title:'Form Edit Pengguna',
               fn:'Pengguna',
               data:JSON.stringify(e['data']),
               value:JSON.stringify(e['value']),
               btn_yes:'Simpan',
               list:'get_pengguna'}, function(e){
                  var error = 0;
                  var error_msg = '';
                  if( $('#nomor_whatsapp').val() == '' ) {
                     error = 1;
                     error_msg = 'Nomor Whatsapp tidak boleh kosong';
                  }else{
                     if( $('#id').length > 0  ){
                        var check_nomor_whatsapp_exist =  ajax_x_sync(baseUrl + "Pengguna/check_nomor_whatsapp", [{ nomor_whatsapp: $('#nomor_whatsapp').val(), id: $('#id').val() }] );
                     }else{
                        var check_nomor_whatsapp_exist =  ajax_x_sync(baseUrl + "Pengguna/check_nomor_whatsapp", [{ nomor_whatsapp: $('#nomor_whatsapp').val() }] );
                     }
                     if( check_nomor_whatsapp_exist.error == true ) {
                        error = 1;
                        error_msg = check_nomor_whatsapp_exist.error_msg;
                     }
                  }
                  if( $('#password').val() != '' ) {
                     if( $('#password').val() != $('#conf_password').val() ) {
                        error = 1;
                        error_msg = 'Password konfirmasi tidak sesuai.';
                     }
                  }
                  return {error:error, error_msg:error_msg}
               });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

// add pengguna
function add_pengguna(){
   ajax_x(
      baseUrl + "Pengguna/info_add_pengguna", function(e) {
         if( e['error'] == false ) {
            simpleConfirmModalDialog({
               width:'col-4',
               title:'Form Tambah Pengguna',
               fn:'Pengguna',
               data:JSON.stringify(e['data']),
               btn_yes:'Simpan',
               list:'get_pengguna'}, function(e){
                  var error = 0;
                  var error_msg = '';
                  if( $('#nomor_whatsapp').val() == '' ) {
                     error = 1;
                     error_msg = 'Nomor Whatsapp tidak boleh kosong';
                  }else{
                     if( $('#id').length > 0  ){
                        var check_nomor_whatsapp_exist =  ajax_x_sync(baseUrl + "Pengguna/check_nomor_whatsapp", [{ nomor_whatsapp: $('#nomor_whatsapp').val(), id: $('#id').val() }] );
                     }else{
                        var check_nomor_whatsapp_exist =  ajax_x_sync(baseUrl + "Pengguna/check_nomor_whatsapp", [{ nomor_whatsapp: $('#nomor_whatsapp').val() }] );
                     }
                     if( check_nomor_whatsapp_exist.error == true ) {
                        error = 1;
                        error_msg = check_nomor_whatsapp_exist.error_msg;
                     }
                  }
                  if( $('#password').val() != '' ) {
                     if( $('#password').val() != $('#conf_password').val() ) {
                        error = 1;
                        error_msg = 'Password konfirmasi tidak sesuai.';
                     }
                  }
                  return {error:error, error_msg:error_msg}
               });
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function formPengguna(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   // define value variable
   var id = '';
   var fullname = '';
   var nomor_whatsapp = '';
   var group_selected = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" id="id" name="id" value="${value.user_id}">`;
      fullname = value.fullname;
      nomor_whatsapp = value.nomor_whatsapp;
      group_selected = value.group_id;
   }
   var form = `<form action="${baseUrl }Pengguna/addUpdatePengguna" id="form_utama" class="formName">
                  ${id}
                  <div class="row px-0 py-3 mx-0">
                     <div class="col-12">
                        <div class="form-group">
                           <label class="form-label">Nama Lengkap Pengguna</label>
                           <input class="form-control form-control-sm" type="text" id="fullname" value="${fullname}" name="fullname" placeholder="Nama Lengkap" required>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label class="form-label">Grup</label>
                           <select id="grup" name="grup" class="dropdown-own js-example-basic-single" style="width:90%" title="Grup">`;
               for( x in json) {
                  form += `<option value="${json[x].group_id}" ${ json[x].group_id == group_selected ? 'selected' : '' }>${json[x].nama_group}</option>`;
               }
               form +=     `</select>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label class="form-label">Nomor Whatsapp</label>
                           <input class="form-control form-control-sm" type="text" id="nomor_whatsapp" value="${nomor_whatsapp}" name="nomor_whatsapp" placeholder="Nomor Whatsapp" required>
                           <small id="nomor_whatsappHelp" class="form-text text-muted">Pastikan nomor yang terdaftar adalah nomor Whatsapp yang aktif. Nomor ini akan digunakan untuk menerima OTP.</small>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label class="form-label">Password</label>
                           <input class="form-control form-control-sm" type="password" id="password" name="password" placeholder="Password">
                           <small class="form-text text-muted">Password hanya terdiri dari alpha numeric.</small>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label class="form-label">Konfirmasi Password</label>
                           <input class="form-control form-control-sm" type="password" id="conf_password" name="conf_password" placeholder="Konfirmasi Password">
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $(".dropdown-own").select2({
                     dropdownParent: $(".jconfirm")
                  });
               </script>`;
   return form;
}

// delete pengguna
function delete_pengguna(id){
   ajax_x(
      baseUrl + "Pengguna/delete_pengguna", function(e) {
         if( e['error'] == false ) {
            get_pengguna(20);
            smile_alert(e['error_msg']);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}
