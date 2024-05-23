function topik_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_topik()">
                        <i class="fas fa-tags"></i> Tambah Topik
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_topik(20)" id="searchAllTopik" name="searchAllTopik" placeholder="Topik" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_topik(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:60%;">Topik</th>
                              <th style="width:30%;">Pembaharuan Terakhir</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_topik">
                           <tr>
                              <td colspan="4">Daftar topik tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_topik"></div>
                  </div>
               </div>
            </div>`;
}

function topik_getData(){
   get_topik(20);
}

function get_topik(perpage){
   get_data( perpage,
            { url : 'Topik/daftar_topik',
               pagination_id: 'pagination_daftar_topik',
               bodyTable_id: 'bodyTable_daftar_topik',
               fn: 'ListDaftarTopik',
               warning_text: '<td colspan="4">Daftar topik tidak ditemukan</td>',
               param : { search : $('#searchAllTopik').val() } } );
}

function ListDaftarTopik(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.topik}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Topik"
                        onclick="edit_topik('${json.id}')" style="margin:.15rem .1rem !important">
                        <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Topik"
                        onclick="delete_topik('${json.id}')" style="margin:.15rem .1rem !important">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function formTopik(JSONValue){
   var id = '';
   var topik = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" value="${value.id}">`;
      topik = value.topik;
   }
   var form = `<form action="${baseUrl }Topik/addupdate_topik" id="form_utama" class="formName ">
                  ${id}
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Topik</label>
                           <input type="text" required name="topik" placeholder="Topik" class="form-control form-control-sm"
                              id="topik" value="${topik}">
                        </div>
                     </div>
                  </div>
               </form>`;
   return form;
}

function delete_topik(id){
   ajax_x_t2(
      baseUrl + "Topik/delete_topik", function(e) {
         if( e['error'] == false ){
            get_topik(20);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function add_topik(){
   $.confirm({
      title: 'Form Tambah Topik',
      theme: 'material',
      columnClass: 'col-4',
      content: formTopik(),
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         formSubmit: {
            text: 'Simpan',
            btnClass: 'btn-blue',
            action: function () {
               ajax_submit_t1("#form_utama", function(e) {
                  get_topik(20);
               });
            }
         }
      }
   });
}

function edit_topik(id){
   ajax_x_t2(
      baseUrl + "Topik/info_edit_topik", function(e) {
         $.confirm({
            title: 'Form Edit Topik',
            theme: 'material',
            columnClass: 'col-4',
            content: formTopik(JSON.stringify(e['value'])),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        get_topik(20);
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}
