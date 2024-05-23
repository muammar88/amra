function pengaturan_perangkat_whatsapp_Pages(){
   return  `<style>
               
            </style>
            <div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentPengaturanPerangkatWhatsapp">
                  <div class="col-lg-4">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th colspan="3" style="background-color: #e7e7e7;">INFO PERANGKAT</th>
                           </tr>
                        </thead>
                        <tbody id="info_perangkat">
                           <tr>
                              <td colspan="3" class="border-0 pt-3">
                                 <span>Perangkat Tidak Ditemukan</span>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     
                  </div>
                   <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th colspan="3" style="background-color: #e7e7e7;">QR CODE PERANGKAT</th>
                           </tr>
                        </thead>
                        <tbody id="qrcodeperangkat">
                           <tr>
                              <td colspan="3" class="border-0 pt-3">
                                 <span>Perangkat Tidak Ditemukan</span>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     
                  </div>
               </div>
            </div>`;
}

function pengaturan_perangkat_whatsapp_getData(){
   ajax_x(
      baseUrl + "Pengaturan_perangkat_whatsapp/get_info_perangkat", function(e) {
         if( e['error'] == false ){
            var html = `<tr>
                           <td style="width:40%;" class="text-left"><b>Nomor WA Perangkat</b></td>
                           <td style="width:1%;">:</td>
                           <td style="width:59%;" class="text-left px-0">${e.data.whatsapp_number}</td>
                        </tr>
                        <tr>
                           <td class="text-left"><b>Device Key</b></td>
                           <td >:</td>
                           <td class="text-left px-0">${e.data.device_key}</td>
                        </tr>
                        <tr>
                           <td class="text-left"><b>Tanggal Berlangganan</b></td>
                           <td >:</td>
                           <td class="text-left px-0">${e.data.start_date}</td>
                        </tr>
                        <tr>
                           <td class="text-left"><b>Tanggal Berakhir</b></td>
                           <td >:</td>
                           <td class="text-left px-0">${e.data.end_date}</td>
                        </tr>
                        <tr>
                           <td class="text-left"><b>Status Berlangganan</b></td>
                           <td >:</td>
                           <td class="text-left px-0">${e.data.status}</td>
                        </tr>
                        <tr>
                           <td colspan="3" class="border-0 pt-3">
                              <button class="btn btn-default float-right" type="button" onclick="restart_perangkat()">
                                  <i class="fas fa-money-bill-wave"></i> Restart Perangkat
                              </button>
                           </td>
                        </tr>`;

            $('#info_perangkat').html(html);      
            $('#qrcodeperangkat').html(`<tr>
                                          <td class="text-left px-0" colspan="3" style="width:100%;height:2000px;overflow:hidden;">
                                             <iframe src="https://wapisender.id/api/v5/device/qr?api_key=${e.data.api_id}&device_key=${e.data.device_key}" allowfullscreen  style="width:100%;height:2000px;overflow:hidden;"></iframe></td>
                                          </tr>`);
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function restart_perangkat(){
   ajax_x(
      baseUrl + "Pengaturan_perangkat_whatsapp/restart_perangkat", function(e) {
         if( e['error'] == false ){
            smile_alert(e['error_msg']);
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}