function slider_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_slider()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Slider
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_slider(20)" id="searchAllSlider" name="searchAllSlider" placeholder="Judul Slider" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_slider(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:30%;">Slider</th>
                              <th style="width:60%;">Judul Slider</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_slider">
                           <tr>
                              <td colspan="4">Daftar slider tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_slider"></div>
                  </div>
               </div>
            </div>`;
}

function slider_getData(){
   get_slider(20);
}

function get_slider(perpage){
   get_data( perpage,
            { url : 'Slider/daftar_slider',
               pagination_id: 'pagination_daftar_slider',
               bodyTable_id: 'bodyTable_daftar_slider',
               fn: 'ListDaftarSlider',
               warning_text: '<td colspan="4">Daftar slider tidak ditemukan</td>',
               param : { search : $('#searchAllSlider').val() } } );
}

function ListDaftarSlider(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td><img src="${baseUrl + 'image/slider/' + json.img}" style="box-shadow: rgb(0 0 0 / 75%) 0px 7px 13px -9px;"></td>
                  <td>${json.title}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Slider"
                        onclick="edit_slider('${json.id}')" style="margin:.15rem .1rem !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Slider"
                        onclick="delete_slider('${json.id}')" style="margin:.15rem .1rem !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function formSlider(JSONValue){
   var id = '';
   var img = '';
   var title = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" value="${value.id}" name="id">`;
      img =`<div class="row px-0 mx-0">
               <div class="col-12" >
                  <div class="form-group">
                     <label>Gambar Slider</label>
                     <div class="text-center">
                        <img src="${baseUrl + 'image/slider/' + value.img}" class="img-fluid w-100" alt="${value.title}" style="box-shadow: rgb(0 0 0 / 75%) 0px 7px 13px -9px;">
                     </div>
                  </div>
               </div>
            </div>`;
      title = value.title;
   }
   var form = `<form action="${baseUrl }Slider/update_slider" id="form_utama" class="formName ">
                  ${id}
                  ${img}
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Upload Gambar Slider</label>
                           <input class="form-control form-control-sm" type="file" name="userFile" id="file">
                        </div>
                     </div>
                  </div>
                  <div class="row px-0 mx-0">
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Judul Slider</label>
                           <input type="text" required name="title" placeholder="Judul Slider" class="form-control form-control-sm"
                              id="title" value="${title}">
                        </div>
                     </div>
                  </div>
               </form>`;
   return form;
}

function add_slider(){
   $.confirm({
      title: 'Form Tambah Slider',
      theme: 'material',
      columnClass: 'col-4',
      content: formSlider(),
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
                  get_slider(20);
               });
            }
         }
      }
   });
}

// edit slider
function edit_slider(id){
   ajax_x_t2(
      baseUrl + "Slider/info_edit_slider", function(e) {
         $.confirm({
            title: 'Form Edit Slider',
            theme: 'material',
            columnClass: 'col-4',
            content: formSlider(JSON.stringify(e['value'])),
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
                        get_slider(20);
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}

// delete slider
function delete_slider(id){
   ajax_x_t2(
      baseUrl + "Slider/delete_slider", function(e) {
         if( e['error'] == false ){
            get_slider(20);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}
