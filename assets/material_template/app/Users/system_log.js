function system_log_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarSystemLog">
                  <div class="col-6 col-lg-8 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" id="searchAllDaftarSystemLog" name="searchAllDaftarSystemLog" placeholder="Username" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_system_log( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:60%;">Log Msg</th>
                              <th style="width:20%;">Nama Lengkap dan Username Pengguna</th>
                              <th style="width:20%;">Transaction Date</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_system_log">
                           <tr>
                              <td colspan="4">Daftar airlines tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_system_log"></div>
                  </div>
               </div>
            </div>`;
}

function system_log_getData(){
   get_system_log(30);
}

function get_system_log(perpage){
   get_data( perpage,
             { url : 'System_log/daftar_system_log',
               pagination_id: 'pagination_daftar_system_log',
               bodyTable_id: 'bodyTable_daftar_system_log',
               fn: 'ListDaftarSystemLog',
               warning_text: '<td colspan="4">Daftar aktifitas pengguna tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarSystemLog').val() } } );
}

function ListDaftarSystemLog(JSONData){
   var json = JSON.parse(JSONData);
   return `<tr>
               <td class="text-left">${json.log_msg}</td>
               <td>${json.fullname} <br> ${json.nomor_whatsapp}</td>
               <td>${json.input_date}</td>
           </tr>`
}
