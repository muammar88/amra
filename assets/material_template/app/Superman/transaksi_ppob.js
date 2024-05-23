function transaksi_ppob_Pages(){
	return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="UpdateStatusProsesTransaksi()">
                        <i class="fas fa-money-bill"></i> Update Status Proses Transaksi
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="status" id="status" onchange="transaksi_ppob_getData()" title="Status">
                           <option value="semua">Pilih Semua</option>
                           <option value="perusahaan">Perusahaan</option>
                           <option value="pelanggan">Pelanggan PPOB</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="transaksi_ppob_getData()" id="searchAllDaftarTransaksiPPOB" name="searchAllDaftarTransaksiPPOB" placeholder="Kode Transaksi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="transaksi_ppob_getData()">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:25%;">Info Transaksi</th>
                              <th style="width:25%;">Info Pelanggan</th>
                              <th style="width:20%;">Info Produk</th>
                              <th style="width:20%;">Info Harga</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_transaksi_pelanggan_ppob">
                           <tr>
                              <td colspan="5">Daftar info transaksi ppob tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_transaksi_pelanggan_ppob"></div>
                  </div>
               </div>
            </div>`;
}

function transaksi_ppob_getData() {
  get_transaksi_ppob(300);
}

function get_transaksi_ppob(perpage){
   get_data(perpage, {
      url: "Superman/Pelanggan_PPOB/daftar_transaksi_ppob",
      pagination_id: "pagination_daftar_transaksi_pelanggan_ppob",
      bodyTable_id: "bodyTable_daftar_transaksi_pelanggan_ppob",
      fn: "ListDaftarTransaksiPPOB",
      warning_text: '<td colspan="5">Daftar info transaksi ppob tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarTransaksiPPOB').val(), status: $('#status').val() } ,
   });
}

function ListDaftarTransaksiPPOB(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>
                     <table class="table table-hover mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;"><b>KODE TRANSAKSI</b></td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;"><b>#${json.transaction_code}</b></td>
                           </tr>
                           <tr>
                              <td class="text-left" ><b>NOMOR TUJUAN</b></td>
                              <td class="px-0" >:</td>
                              <td class="text-left" >${json.nomor_tujuan}</td>
                           </tr>
                           <tr>
                              <td class="text-left" ><b>TANGGAL TRANSAKSI</b></td>
                              <td class="px-0" >:</td>
                              <td class="text-left" >${json.created_at}</td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
                  <td>
                     <table class="table table-hover mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;"><b>STATUS PELANGGAN</b></td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;"><b style="text-transform:uppercase;color: ${json.status_pelanggan == 'Perusahaan' ? 'blue' : 'amber'}">${json.status_pelanggan}</b></td>
                           </tr>
                           <tr>
                              <td class="text-left" ><b>NAMA PELANGGAN</b></td>
                              <td class="px-0" >:</td>
                              <td class="text-left" ><b style="color:orange;">${json.nama_pelanggan}</b></td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
                  <td>
                     ${json.product_name} <br>
                     ${json.product_code}
                  </td>
                  <td>
                     <table class="table table-hover mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;"><b>STATUS</b></td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;"><b style="text-transform:uppercase;color:${json.status=='success'? 'green' : (json.status=='failed' ? 'red' : 'orange') }">${json.status}</b></td>
                           </tr>
                           <tr>
                              <td class="text-left" style="width:40%;"><b>SERVER</b></td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;"><b style="text-transform:uppercase;">${json.server}</b></td>
                           </tr>
                           <tr>
                              <td class="text-left" ><b>HARGA SERVER</b></td>
                              <td class="px-0" >:</td>
                              <td class="text-left" style="color:red !important;">Rp ${numberFormat(json.server_price)}</td>
                           </tr>
                           <tr>
                              <td class="text-left" ><b>HARGA APLIKASI</b></td>
                              <td class="px-0" >:</td>
                              <td class="text-left" style="color:red !important;">Rp ${numberFormat(json.application_price)}</td>
                           </tr>

                        </tbody>
                     </table>
                  </td>
                  <td></td>
                </tr>`;
   return html;             
}


function UpdateStatusProsesTransaksi(){
   ajax_x(
      baseUrl + "Superman/PPOB/updateStatusTransaksi", function(e) {
         if( e['error'] == false ) {
            smile_alert(e["error_msg"]);
            get_transaksi_ppob(300);
         }else{
            frown_alert(e["error_msg"]);
         }
      },[]
   );
}