function daftar_trans_ppob_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_trans_ppob(20)" id="searchDaftarTransPPOB" name="searchDaftarTransPPOB" placeholder="Judul Pesan" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_trans_ppob(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:12%;">Nomor Transaksi</th>
                              <th style="width:12%;">Kode Produk</th>
                              <th style="width:10%;">Harga Produk</th>
                              <th style="width:31%;">Info Member</th>
                              <th style="width:10%;">Markup Perusahaan</th>
                              <th style="width:10%;">Harga Perusahaan</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_trans_ppob">
                           <tr>
                              <td colspan="8">Daftar transaksi PPOB tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_trans_ppob"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_trans_ppob_getData() {
   get_daftar_trans_ppob(20);
}

function get_daftar_trans_ppob(perpage){
   get_data( perpage,
          { url : 'PPOB/server_side',
            pagination_id: 'pagination_daftar_trans_ppob',
            bodyTable_id: 'bodyTable_daftar_trans_ppob',
            fn: 'ListDaftarTransPPOB',
            warning_text: '<td colspan="8">Daftar transaksi PPOB tidak ditemukan</td>',
            param : { search : $('#searchNotification').val() } } );
}

function ListDaftarTransPPOB( JSONData ){
   var json = JSON.parse(JSONData);
   var status = '<b style="color:orange;">PROSES</b>';
   if( json.status == 'success' ){
      status = '<b style="color:green;">SUKSES</b>';
   } else if( json.status == 'failed' ){
      status = '<b style="color:red;">GAGAL</b>';
   }
   var html =  `<tr>
                  <td>#<b>${json.transaction_code}</b><br>(${status})</td>
                  <td>${json.product_code}</td>
                  <td>Rp. ${numberFormat(json.application_price)}</td>
                  <td>${json.fullname}<br>${json.identity_number}</td>
                  <td>Rp. ${numberFormat(json.company_markup)}</td>
                  <td>Rp. ${numberFormat(json.company_price)}</td>
                  <td>${json.created_at}</td>
               </tr>`;
   return html;
}