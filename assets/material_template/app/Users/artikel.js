function artikel_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentSlider">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_artikel()">
                        <i class="fas fa-newspaper"></i> Tambah Artikel Baru
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_artikel(20)" id="searchAllArtikel" name="searchAllArtikel" placeholder="Judul Artikel" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_artikel(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:35%;">Judul Artikel</th>
                              <th style="width:25%;">Pengarang</th>
                              <th style="width:20%;">Topic</th>
                              <th style="width:10%;">Headline</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_artikel">
                           <tr>
                              <td colspan="4">Daftar artikel tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_artikel"></div>
                  </div>
               </div>
            </div>`;
}

function artikel_getData(){
   get_artikel(20);
}

function get_artikel(perpage){
   get_data( perpage,
            { url : 'Artikel/daftar_artikel',
               pagination_id: 'pagination_daftar_artikel',
               bodyTable_id: 'bodyTable_daftar_artikel',
               fn: 'ListDaftarArtikel',
               warning_text: '<td colspan="4">Daftar artikel tidak ditemukan</td>',
               param : { search : $('#searchAllArtikel').val() } } );
}

function formImageArtikel(JSONData){
   var json = JSON.parse(JSONData);
   return  `<div class="row">
               <div class="col-12">
                  <img src="${baseUrl}/image/artikel/${json.photo}" class="img-fluid w-100" alt="${json.photo_caption}" style="box-shadow: rgb(0 0 0 / 75%) 0px 7px 13px -9px;">
               </div>
               <div class="col-12 py-2" style="background-color: #ffffff;font-style: italic;color: #999;font-size: 12px;">
                  <p>${json.photo_caption}</p>
               </div>
            </div>`;
}

function preview_photo_artikel(id){
   ajax_x(
      baseUrl + "Artikel/info_photo_artikel",
      function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-5',
               title: 'Preview Photo Artikel',
               type:'blue',
               theme: 'material',
               content: formImageArtikel(JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  tutup: function () {
                       return true;
                  },

               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },
      [{id:id}]
   );
}

function ListDaftarArtikel(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.title}
                     <button class="btn btn-default b-block float-none mt-2 mb-1 mx-auto" style="display: block !important;" onClick="preview_photo_artikel(${json.id})">Preview Image</button></td>
                  <td>${json.fullname}</td>
                  <td>${json.topik}</td>
                  <td>${json.headline == 'ya' ? '<i class="fas fa-check" style="color: #718ae9;"></i>' : '<i class="fas fa-times" style="color: grey;"></i>'}</td>
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

// edit topik
function edit_topik(id){
   ajax_x(
      baseUrl + "Artikel/get_info_edit_artikel",
      function(e) {
         $.confirm({
            columnClass: 'col-10',
            title: 'Form Edit Artikel',
            theme: 'material',
            content: formAddUpdateArtikel(JSON.stringify(e['data']), JSON.stringify(e['value'])),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_ckeditor("#form_utama", 'artikel', function(e) {
                        $.alert({
                           title: 'Peringatan',
                           content: e['error_msg'],
                           type: e['error'] == true ? 'red' :'green'
                        });
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           get_artikel(20);
                        }
                     });
                  }
               }
            }
         });
      },
      [{id:id}]
   );

}

function add_artikel(){
   ajax_x(
      baseUrl + "Artikel/get_info_add_artikel",
      function(e) {
         $.confirm({
            columnClass: 'col-10',
            title: 'Form Tambah Artikel',
            theme: 'material',
            content: formAddUpdateArtikel(JSON.stringify(e['data'])),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_ckeditor("#form_utama", 'artikel', function(e) {
                        $.alert({
                           title: 'Peringatan',
                           content: e['error_msg'],
                           type: e['error'] == true ? 'red' :'green'
                        });
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           get_artikel(20);
                        }
                     });
                  }
               }
            }
         });
      },
      []
   );
}


function formAddUpdateArtikel(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   // param
   var id = ``;
   var photo = '';
   var photo_caption = '';
   var title = '';
   var topic = '';
   var tempat_terbit = '';
   var headline = '';
   var artikel = '';
   // value
   if ( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" value="${value.id}">`;
      photo = `<div class="col-12 justify-content-between mx-auto mb-4">
                  <img src="${baseUrl+'image/artikel/'+value.photo}" class="img-fluid w-100 rounded" alt="Photo artikel" style="box-shadow: rgb(0 0 0 / 75%) 0px 7px 13px -9px;">
               </div>`;
      photo_caption = value.photo_caption;
      title = value.title;
      topic = value.topic_id;
      tempat_terbit = value.place;
      headline = value.headline;
      artikel = value.description;
   }
   // form
   var form = `<form action="${baseUrl }Artikel/addUpdateArtikel" id="form_utama" class="formName">
                  <div class="row px-0 py-3 mx-0">
                     ${id}
                     ${photo}
                     <div class="col-4">
                        <div class="form-group">
                           <label class="form-label">Upload Photo Artikel</label>
                           <input class="form-control form-control-sm" type="file" id="photo" name="photo">
                           <small id="photo" class="form-text text-muted">Ukuran file photo maksimum adalah <b>400KB</b>.</small>
                        </div>
                     </div>
                     <div class="col-8">
                        <div class="form-group">
                           <label>Judul Artikel</label>
                           <input type="text" class="form-control form-control-sm" name="title" value="${title}" placeholder="Judul Artikel">
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="form-group">
                           <label>Photo Caption</label>
                           <textarea class="form-control" name="photo_caption" id="photo_caption" rows="5" style="resize:none;" placeholder="Photo Caption">${photo_caption}</textarea>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Topic</label>
                                 <select id="topic" name="topic" class="form-control form-control-sm" title="Topic Artikel">`;
                        for ( x in  json['topic'] ) {
                           form +=   `<option value="${json['topic'][x]['id']}" ${ json['topic'][x]['id'] == topic ? 'selected' : ''}>${json['topic'][x]['topic']}</option>`;
                        }
                        form += `</select>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Tempat Terbit</label>
                                 <input type="text" class="form-control form-control-sm" name="place" value="${tempat_terbit}" placeholder="Tempat Terbit">
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Headline</label>
                                 <div class="form-check">
                                    <input class="form-check-input" name="headline" type="checkbox" value="ya" id="flexCheckDefault" ${headline == 'ya' ? 'checked' : ''}>
                                    <label class="form-check-label" >
                                       Jadikan Headline
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label>Artikel</label>
                           <textarea class="form-control" id="artikel" name="artikel" rows="3" style="resize:none;" placeholder="Artikel">${artikel}</textarea>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  CKEDITOR.replace('artikel');
                  $(".dropdown-own").select2({
                     dropdownParent: $(".jconfirm")
                  });
                  $("#tag").select2({
                     tags: true,
                     dropdownParent: $(".jconfirm")
                  });
               </script>`;
    return form;
}

// delete topik
function delete_topik(id){
   ajax_x_t2(
      baseUrl + "Artikel/delete_topik", function(e) {
         if( e['error'] == false ){
            get_artikel(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
