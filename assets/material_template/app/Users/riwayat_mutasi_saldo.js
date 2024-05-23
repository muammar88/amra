function riwayat_mutasi_saldo_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_riwayat_mutasi_saldo( 20)" id="searchAllDaftarMutasiSaldo" name="searchAllDaftarMutasiSaldo" placeholder="Kode Transaksi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_riwayat_mutasi_saldo( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Kode</th>
                              <th style="width:15%;">Nominal</th>
                              <th style="width:15%;">Jenis Transaksi</th>
                              <th style="width:30%;">Ket</th>
                              <th style="width:15%;">Status</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_riwayat_mutasi_saldo">
                           <tr>
                              <td colspan="6">Daftar riwayat mutasi saldo tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_riwayat_mutasi_saldo"></div>
                  </div>
               </div>
            </div>`;
}

function riwayat_mutasi_saldo_getData(){
   get_riwayat_mutasi_saldo(20);
}

function get_riwayat_mutasi_saldo(perpage){
   get_data( perpage,
             { url : 'Saldo_perusahaan/daftar_riwayat_mutasi_saldo',
               pagination_id: 'pagination_daftar_riwayat_mutasi_saldo',
               bodyTable_id: 'bodyTable_daftar_riwayat_mutasi_saldo',
               fn: 'ListDaftarRiwayatMutasiSaldo',
               warning_text: '<td colspan="6">Daftar riwayat mutasi saldo tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarMutasiSaldo').val() } } );
}

function ListDaftarRiwayatMutasiSaldo(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td><b>${json.kode}</b></td>
                  <td>Rp ${numberFormat(json.saldo)}</td>
                  <td>${json.request_type}</td>
                  <td>${json.ket}</td>
                  <td>${json.status}</td>
                  <td>${json.last_update}</td>
               </tr>`;
   return html;
}
