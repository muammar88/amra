function info_saldo_member_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                   
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
                              <th style="width:40%;">Info Member</th>
                              <th style="width:13%;">Status Member</th>
                              <th style="width:47%;">Info Saldo</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_info_saldo_member">
                           <tr>
                              <td colspan="4">Daftar member tidak ditemukan</td>
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



function info_saldo_member_getData(){
   get_info_saldo_member(20);
}

function get_info_saldo_member(perpage){
   get_data( perpage,
             { url : 'Info_saldo_member/daftar_member',
               pagination_id: 'pagination_info_saldo_member',
               bodyTable_id: 'bodyTable_info_saldo_member',
               fn: 'ListInfoSaldoMember',
               warning_text: '<td colspan="4">Daftar member tidak ditemukan</td>',
               param : { search : $('#search_saldo_member').val() } } );
}

function ListInfoSaldoMember(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>
                     <table class="table table-hover mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="text-left px-0 border-0" style="width:20%;"><b>Nama Member</b></td>
                              <td class="px-0 border-0" style="width:1%;">:</td>
                              <td class="text-left border-0" style="width:76%;">${json.fullname}</td>
                           </tr>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="text-left px-0 border-0" style="width:20%;"><b>Nomor Identitas</b></td>
                              <td class="px-0 border-0" style="width:1%;">:</td>
                              <td class="text-left border-0" style="width:76%;">${json.identity_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="text-left px-0 border-0" style="width:20%;"><b>Jenis Kelamin</b></td>
                              <td class="px-0 border-0" style="width:1%;">:</td>
                              <td class="text-left border-0" style="width:76%;">${json.gender}</td>
                           </tr>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="text-left px-0 border-0" style="width:20%;"><b>Tempat / Tgl Lahir</b></td>
                              <td class="px-0 border-0" style="width:1%;">:</td>
                              <td class="text-left border-0" style="width:76%;">${json.birth_place} / ${json.birth_date}</td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
                  <td>
                     <table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="border-0 text-left px-0"><b>Member</b></td>
                           </tr>`;
               for( x in json.register_as ){
                  html += `<tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="border-0 text-left px-0"><b>${json.register_as[x]}</b></td>
                           </tr>`;
               }
            html +=    `</tbody>
                     </table>
                  </td>
                  <td>
                     <table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="text-left border-0 px-0" style="width:20%;"><b>Total Deposit</b></td>
                              <td class="px-0 border-0" style="width:1%;">:</td>
                              <td class="text-left border-0" style="width:76%;"><b style="color:red">Rp ${numberFormat(json.deposit)}</b></td>
                           </tr>
                           <tr>
                              <td class="text-left border-0" style="width:3%;"><b><i class="fas fa-arrow-right"></b></td>
                              <td class="text-left border-0 px-0" style="width:20%;"><b>Total Tabungan</b></td>
                              <td class="px-0 border-0" style="width:1%;">:</td>
                              <td class="text-left border-0" style="width:76%;"><b style="color:red">Rp ${numberFormat(json.tabungan)}</b></td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
                  
               </tr>`;
   return html;
}