function info_saldo_member_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="download_excel_info_saldo_member()">
                        <i class="fas fa-print"></i> Download Excel Data Jamaah
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="info_saldo_member_getData(20)" id="search_saldo_member" name="search_saldo_member" placeholder="Nama Member & Nomor Identitas" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="info_saldo_member_getData(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:30%;">Info Member</th>
                              <th style="width:60%;">Riwayat Deposit</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_info_saldo_member">
                           <tr>
                              <td colspan="3">Daftar member tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_info_saldo_member"></div>
                  </div>
               </div>
            </div>`;
}


function download_excel_info_saldo_member(){
   ajax_x(
      baseUrl + "Info_saldo_member/download_excel_info_saldo_member", function(e) {
         if( e['error'] == false ){
            window.open(baseUrl + "Download/", "_blank");
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}


function info_saldo_member_getData(){
   get_info_saldo_member(20);
}

function get_info_saldo_member(perpage){
   get_data( perpage,
             { url : 'Info_saldo_member/daftar_member',
               pagination_id: 'pagination_info_saldo_member',
               bodyTable_id: 'bodyTable_info_saldo_member',
               fn: 'ListInfoSaldoMember',
               warning_text: '<td colspan="3">Daftar member tidak ditemukan</td>',
               param : { search : $('#search_saldo_member').val() } } );
}

function ListInfoSaldoMember(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>
                     <table class="table table-hover mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left px-2 border border-right-0" style="width:30%;"><b>Nama Member</b></td>
                              <td class="px-0 border border-right-0 border-left-0" style="width:1%;">:</td>
                              <td class="text-left border border-left-0" >${json.fullname}</td>
                           </tr>
                           <tr>
                              <td class="text-left px-2 border border-right-0" ><b>Nomor Identitas</b></td>
                              <td class="px-0 border border-right-0 border-left-0" >:</td>
                              <td class="text-left border border-left-0" >${json.identity_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left px-2 border border-right-0" ><b>Jenis Kelamin</b></td>
                              <td class="px-0 border border-right-0 border-left-0" >:</td>
                              <td class="text-left border border-left-0" >${json.gender}</td>
                           </tr>
                           <tr>
                              <td class="text-left px-2 border border-right-0" ><b>Tempat / Tgl Lahir</b></td>
                              <td class="px-0 border border-right-0 border-left-0" >:</td>
                              <td class="text-left border border-left-0" >${json.birth_place} / ${json.birth_date}</td>
                           </tr>
                           <tr>
                              <td class="text-left px-2 border border-right-0" ><b>Status Member</b></td>
                              <td class="px-0 border border-right-0 border-left-0" >:</td>
                              <td class="text-left border border-left-0" style="font-weight:bold;">Member, `;
            for( x in json.register_as ){
               html += json.register_as[x] + ', ';
            }
            html +=    `     </td>
                           </tr>

                            <tr>
                              <td class="text-left px-2 border border-right-0" ><b>Total Deposit</b></td>
                              <td class="px-0 border border-right-0 border-left-0" >:</td>
                              <td class="text-left border border-left-0" ><b style="color:red">Rp ${numberFormat(json.deposit)}</b></td>
                           </tr>
                            <tr>
                              <td class="text-left px-2 border border-right-0" ><b>Total Tabungan</b></td>
                              <td class="px-0 border border-right-0 border-left-0" >:</td>
                              <td class="text-left border border-left-0" ><b style="color:red">Rp ${numberFormat(json.tabungan)}</b></td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
                  <td>
                     <table class="table table-hover mt-0 ">
                         <tbody>
                           <tr>
                              <td class="text-left" colspan="7" style="background-color: #e7e7e7;"><b>RIWAYAT DEPOSIT</b></td>
                           </tr>
                           <tr>
                              <td class="text-center" style="width:5%;">#</td>
                              <td class="text-center" style="width:10%;"><b>INVOICE</b></td>
                              <td class="text-center" style="width:40%;"><b>BIAYA</b></td>
                              <td class="text-center" style="width:20%;"><b>TANGGAL TRANSAKSI</b></td>
                              <td class="text-center" style="width:20%;"><b>PENERIMA</b></td>
                              <td class="text-center" style="width:5%;"><b>AKSI</b></td>
                           </tr>`;

                           if( json.riwayat_deposit.length > 0  ) {
                              var n = 1;
                              for ( x in json.riwayat_deposit ) {
                                 html +=  `<tr>
                                             <td class="text-center align-middle">${n}</td>
                                             <td class="text-center align-middle">${json.riwayat_deposit[x].nomor_transaksi}</td>
                                             <td class="text-center px-0 align-middle" >
                                                <ul class="list my-0">
                                                   <li>Biaya <b>DEBET</b>: Rp ${numberFormat(json.riwayat_deposit[x].debet)}</li>
                                                   <li>Biaya <b>KREDIT</b>: Rp ${numberFormat(json.riwayat_deposit[x].kredit)}</li>
                                                   <li>Deposit Sebelum : Rp ${numberFormat(json.riwayat_deposit[x].saldo_sebelum)}</li>
                                                   <li>Deposit Sesudah : Rp ${numberFormat(json.riwayat_deposit[x].saldo_sesudah)}</li>
                                                </ul>

                                             </td>
                                             <td class="text-center align-middle">2024-04-22 10:27:51</td>
                                             <td class="text-center align-middle">${json.riwayat_deposit[x].penerima}</td>
                                             <td class="text-center align-middle">
                                                <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Deposit" onclick="cetak_kwitansi_deposit_saldo('${json.riwayat_deposit[x].id}')" style="margin:.15rem .1rem  !important">
                                                   <i class="fas fa-print" style="font-size: 11px;"></i>
                                                </button>
                                             </td>
                                          </tr>`;
                                 n++;         
                              }
                           }else{
                              html += `<tr>
                                          <td class="text-center" colspan="6">Riwayat deposit saldo tidak ditemukan</td>
                                       </tr>`;
                           }

         html +=       `</tbody>
                     </table>

                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Deposit" onclick="deposit_paket('1160')" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-hand-holding-usd" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Refund Tabungan Paket" onclick="refund_tabungan_paket('1160')" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-box-open" style="font-size: 11px;"></i>
                     </button>
                  </td>
                  
               </tr>`;
   return html;
}