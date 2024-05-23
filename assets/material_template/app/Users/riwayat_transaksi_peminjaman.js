function riwayat_transaksi_peminjaman_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" >
                  <div class="col-6 col-lg-6 my-3 ">
                     <button class="btn btn-default" type="button" onClick="cetak_riwayat_transaksi_peminjaman()">
                        <i class="fas fa-print"></i> Cetak Riwayat Transaksi Peminjaman
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="date" onChange="get_riwayat_transaksi_peminjaman(20)" id="start_date" placeholder="Start Date" style="font-size: 12px;" title="Start Date">
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="date" onChange="get_riwayat_transaksi_peminjaman(20)" id="end_date"  placeholder="End Date" style="font-size: 12px;" title="End Date">
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onChange="get_riwayat_transaksi_peminjaman(20)" id="search"  placeholder="Nomor Registrasi / Nomor Invoice " style="font-size: 12px;" title="Nomor Registrasi / Nomor Invoice">
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:15%;">No Registrasi / Invoice</th>
                              <th style="width:30%;">Info Jamaah</th>
                              <th style="width:15%;">Biaya</th>
                              <th style="width:10%;">Status Biaya</th>
                              <th style="width:15%;">Penerima</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_riwayat_transaksi_peminjaman">
                           <tr>
                              <td colspan="6">Riwayat transaksi peminjaman tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_riwayat_transaksi_peminjaman"></div>
                  </div>
               </div>
            </div>`;
}

// airlines_getData

function riwayat_transaksi_peminjaman_getData(){
   // console.log('1231231231231');
   get_riwayat_transaksi_peminjaman(20);
}

function get_riwayat_transaksi_peminjaman(perpage){
   // console.log('xxxxxxxxx');
   get_data( perpage,
             { url : 'Riwayat_transaksi_peminjaman/daftar_riwayat_transaksi_peminjaman',
               pagination_id: 'pagination_riwayat_transaksi_peminjaman',
               bodyTable_id: 'bodyTable_riwayat_transaksi_peminjaman',
               fn: 'ListRiwayatTransaksiPeminjaman',
               warning_text: '<td colspan="6">Riwayat transaksi peminjaman tidak ditemukan</td>',
               param : { start_date: $('#start_date').val(), end_date: $('#end_date').val(), search : $('#search').val() } } );
}

function ListRiwayatTransaksiPeminjaman(JSONData){
   var json = JSON.parse(JSONData);

     return `<tr>
               <td>#${json.register_number}/<br>#${json.invoice}</td>
               <td>${json.fullname}<br>(ID : ${json.identity_number})</td>
               <td>Rp ${numberFormat(json.bayar)}</td>
               <td>${json.status}</td>
               <td>${json.petugas}</td>
               <td>${json.transaction_date}</td>
           </tr>`;
}

function cetak_riwayat_transaksi_peminjaman(){
   var start_date = $('#start_date').val();
   var end_date = $('#end_date').val();
   var search = $('#search').val();
   // filter
   ajax_x_t2(
      baseUrl + "Riwayat_transaksi_peminjaman/cetak_riwayat_transaksi_peminjaman", function(e) {
         if( e.error == false ) {
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{ start_date: start_date, end_date: end_date, search:search }]
   );
}