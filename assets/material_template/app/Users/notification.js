function notification_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_notification()">
                        <i class="fas fa-bell"></i> Tambah Notification
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_notification(20)" id="searchNotification" name="searchNotification" placeholder="Judul Pesan" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_notification(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:20%;">Judul</th>
                              <th style="width:50%;">Pesan</th>
                              <th style="width:25%;">Update</th>
                              <th style="width:5%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_notification">
                           <tr>
                              <td colspan="4">Daftar pesan tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_notification"></div>
                  </div>
               </div>
            </div>`;
}

function notification_getData() {
   get_daftar_notification(20);
}

function get_daftar_notification(perpage){
   get_data( perpage,
          { url : 'Notif/server_side',
            pagination_id: 'pagination_daftar_notification',
            bodyTable_id: 'bodyTable_daftar_notification',
            fn: 'ListDaftarNotification',
            warning_text: '<td colspan="4">Daftar pesan tidak ditemukan</td>',
            param : { search : $('#searchNotification').val() } } );
}

function ListDaftarNotification(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.title}</td>
                  <td>${json.message}</td>
                  <td>${json.last_update}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Delete Notification"
                        onclick="delete_notification('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;

}

function add_notification() {
    $.confirm({
      columnClass: 'col-6',
      title: 'Tambah Pesan Notifikasi',
      theme: 'material',
      content: formaddupdate_notification(),
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
                     get_daftar_notification(20);
                  }
               });
            }
         }

      }
   });
}

function formaddupdate_notification(){
     var html = `<form action="${baseUrl }Notif/proses_addupdate_notification" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Judul</label>
                                 <input type="text" name="judul" class="form-control form-control-sm" placeholder="Judul" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Pesan</label>
                                 <textarea class="form-control form-control-sm" name="pesan" rows="3" style="resize: none;" placeholder="Pesan" required></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function delete_notification(id){
   ajax_x(
      baseUrl + "Notif/delete", function(e) {
         if( e['error'] == false ){
             get_daftar_notification(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}  


