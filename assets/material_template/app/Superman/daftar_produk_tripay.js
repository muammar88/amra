function daftar_produk_tripay_Pages(){
	return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" >
                  <div class="col-6 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="UpdateDataProdukTripay()">
                        <i class="fas fa-money-bill"></i> Update Produk Tripay
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="daftar_produk_tripay_getData()" id="searchAllDaftarProdukTripay" name="searchAllDaftarProdukTripay" placeholder="Kode / Nama / Operator Produk" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="daftar_produk_tripay_getData()">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:3%;">#</th>
                              <th style="width:12%;">Kode Produk</th>
                              <th style="width:45%;">Nama Produk</th>
                              <th style="width:15%;">Operator</th>
                              <th style="width:15%;">Harga</th>
                              <th style="width:10%;">Status</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_produk_tripay">
                           <tr>
                              <td colspan="6">Daftar produk tripay tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_produk_tripay"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_produk_tripay_getData(){
   get_daftar_produk_tripay(300);
}

function get_daftar_produk_tripay(perpage){
   get_data(perpage, {
      url: "Superman/PPOB/daftar_produk_tripay",
      pagination_id: "pagination_daftar_produk_tripay",
      bodyTable_id: "bodyTable_daftar_produk_tripay",
      fn: "ListDaftarProdukTRIPAY",
      warning_text: '<td colspan="6">Daftar produk tripay tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarProdukTripay').val() } ,
   });
}

function ListDaftarProdukTRIPAY(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.number}</td>
                  <td><b>${json.product_code}</b></td>
                  <td>${json.product_name}</td>
                  <td>${json.operator}</td>
                  <td><b><span style="color:orange;">Rp ${numberFormat(json.product_price)}</span></b></td>
                  <td>${json.status == 'tersedia' ? '<span style="color:green;">ACTIVE</span>' : '<span style="color:red;">NON ACTIVE</span>'}</td>
               </tr>`;
   return html;
}

function UpdateDataProdukTripay(){
   ajax_x(
      baseUrl + "Superman/PPOB/updateDataProductTripay", function(e) {
         if( e['error'] == false ) {
            smile_alert(e['error_msg'])
            get_daftar_produk_tripay(300);
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}