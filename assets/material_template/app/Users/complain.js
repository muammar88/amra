function complain_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-7 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_komplain()">
                        <i class="fa fa-comment-dots"></i> Tambah Komplain
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-3 col-lg-2 my-3 text-right">
                     <div class="form-group my-0">
                        <select class="form-control form-control-sm" name="status_komplain" id="status_komplain" onchange="get_daftar_komplain(20)" title="Status Komplain">
                           <option value="all">Semua</option>
                           <option value="proses">Proses</option>
                           <option value="selesai">Selesai</option>
                           <option value="ditolak">Ditolak</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-3 col-lg-3 my-3 text-right">
                     <div class="input-group my-0">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_komplain(20)" id="searchDaftarKomplain" name="searchDaftarKomplain" placeholder="Komplain" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_komplain(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:13%;">Tanggal Komplain</th>
                              <th style="width:37%;">Komplain</th>
                              <th style="width:25%;">Tab</th>
                              <th style="width:15%;">Status</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_complain">
                           <tr>
                              <td colspan="5">Daftar komplain tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_complain"></div>
                  </div>
               </div>
            </div>`;
}


function complain_getData(){
   get_daftar_komplain(20);
}

function get_daftar_komplain(perpage){
   get_data( perpage,
             { url : 'Komplain/server_daftar_komplain',
               pagination_id: 'pagination_daftar_complain',
               bodyTable_id: 'bodyTable_daftar_complain',
               fn: 'ListDaftarKomplain',
               warning_text: '<td colspan="5">Daftar komplain tidak ditemukan</td>',
               param : { search : $('#searchDaftarKomplain').val(), status : $('#status_komplain').val() } } );
}

function ListDaftarKomplain(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.tanggal_komplain}</td>
                  <td>${json.komplain}</td>
                  <td>${json.tab}</td>
                  <td>${json.status == 'proses' ? '<span style="color:orange;"><b>PROSES</b></span>' : (json.status == 'selesai' ? '<span style="color:green;"><b>SELESAI</b></span>' : '<span style="color:red;"><b>DITOLAK</b></span>' )} <br>
                  ${ json.status == 'ditolak' ? `<a onClick="showAlasan('${json.info_penolakan}')" style="color:black !important;">Alasan Ditolak</a>` : '' }</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Detail Foto Komplain" onclick="detail_komplain(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-info" style="font-size: 11px;"></i>
                     </button>`;
            if( json.status == 'proses'){
               html += `<button type="button" class="btn btn-default btn-action" title="Delete Komplain" onclick="delete_komplain(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>`;
            }         
         html += `</td>
              </tr>`;
   return html;           
}

function showAlasan(alasan){
   $.confirm({
      columnClass: "col-4",
      title: "Alasan Ditolak Komplain",
      theme: "material",
      content: alasan,
      closeIcon: false,
      buttons: {
         tutup: function () {
           return true;
         },
      },
   });
}

// tambah komplain
function add_komplain(){
   ajax_x(
      baseUrl + "Komplain/get_info_tambah_komplain",
         function (e) {
            if (e["error"] == false) {
               $.confirm({
                  columnClass: "col-9",
                  title: "Tambah Komplain Baru",
                  theme: "material",
                  content: formTambahKomplain( JSON.stringify(e.data) ),
                  closeIcon: false,
                  buttons: {
                     cancel: function () {
                       return true;
                     },
                     simpan: {
                       text: "Tambah Komplain",
                       btnClass: "btn-blue",
                       action: function () {
                           ajax_submit_t1("#form_utama", function (e) {
                              e["error"] == true ? frown_alert(e["error_msg"]) : smile_alert(e["error_msg"]);
                              if (e["error"] == true) {
                                 return false;
                              } else {
                                 get_daftar_komplain(20);
                              }
                           });
                        },
                     },
                  },
               });
      } else {
         frown_alert(e["error_msg"]);
      }
    },
    []
  );
}

function formTambahKomplain(JSONData){
   var json = JSON.parse(JSONData);

   var html = `<form action="${baseUrl}Komplain/proses_add_komplain"
                     id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-4">
                        <div class="form-group">
                           <label>Tab</label>
                           <select class="form-control form-control-sm" name="tab" id="tab">`;
                     for (x in json) {
                        html += `<option value="${x}" >
                                    ${json[x]}
                                 </option>`;
                     }
                  html += `</select>
                        </div>
                     </div>
                     <div class="col-8">
                        <div class="form-group">
                           <label>Komplain</label>
                           <textarea class="form-control form-control-sm" name="komplain" rows="6"
                              style="resize: none;" placeholder="Komplain"></textarea>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Photo Bukti</label>
                           <input class="form-control form-control-sm" type="file" id="photo" name="photo">
                           <small id="photo" class="form-text text-muted">Ukuran file photo maksimum adalah <b>400KB</b>.</small>
                        </div>
                     </div>
                     <div class="col-8">
                        <div class="form-group">
                           <label>Deskripsi Photo</label>
                           <textarea class="form-control form-control-sm" name="deskripsi_photo" rows="4"
                              style="resize: none;" placeholder="Deskripsi Photo"></textarea>
                        </div>
                     </div>
                  </div>
               </form>`;
  return html;
}

function delete_komplain(id){
   ajax_x(
      baseUrl + "Komplain/delete_komplain",
         function (e) {
            if (e["error"] == false) {
               smile_alert(e["error_msg"]);
            } else {
               frown_alert(e["error_msg"]);
            }
            get_daftar_komplain(20);
      },
      [{id:id}]
   );
}

function detail_komplain(id){
   ajax_x(
      baseUrl + "Komplain/detail_komplain",
         function (e) {
            $.confirm({
               columnClass: "col-8",
               title: "Detail Komplain",
               theme: "material",
               content: formImageBuktiError( JSON.stringify(e.data) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },

               },
            });
         },
      [{id:id}]
   );
}

function formImageBuktiError(image_path){
   var json = JSON.parse(image_path);
   return `<img src="${baseUrl}/image/komplain/${json.path}" 
            class="img-fluid w-100" alt="${json.comment}" title="${json.comment}" 
            style="box-shadow: rgb(0 0 0 / 75%) 0px 7px 13px -9px;">`;
}
