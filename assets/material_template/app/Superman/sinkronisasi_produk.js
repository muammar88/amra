function sinkronisasi_produk_Pages(){
	return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" >
                  <div class="col-6 col-lg-6 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-12 col-lg-3 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="category" id="category" onchange="get_sinkronisasi_produk(300)" title="Kategori">
                           <option value="0">Pilih Semua</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-12 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_sinkronisasi_produk(300)" id="searchAllDaftarProdukSinkronisasi" name="searchAllDaftarProdukSinkronisasi" placeholder="Kode / Nama / Operator Produk" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_sinkronisasi_produk(300)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:30%;">Info Produk</th>
                              <th style="width:20%;">Operator</th>
                              <th style="width:5%;">Server IAK</th>
                              <th style="width:25%;">Server Tripay</th>
                              <th style="width:15%;">Diperbaharui</th>
                              <th style="width:5%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_produk_sinkronisasi">
                           <tr>
                              <td colspan="6">Daftar produk tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_produk_sinkronisasi"></div>
                  </div>
               </div>
            </div>`;
}

function sinkronisasi_produk_getData(){
   ajax_x(
      baseUrl + "Superman/PPOB/get_category", function(e) {
         if( e['error'] == false ) {
            var html = `<option value="0">Pilih Semua</option>`;
            for ( x in e.data ){
               html += `<option value="${e.data[x].id}">(${e.data[x].category_code}) ${e.data[x].category_name}</option>`;
            }
            $('#category').html(html);
            get_sinkronisasi_produk(300);
         }
      },[]
   );
}

function get_sinkronisasi_produk(perpage){
   get_data(perpage, {
      url: "Superman/PPOB/daftar_produk_sinkronisasi",
      pagination_id: "pagination_daftar_produk_sinkronisasi",
      bodyTable_id: "bodyTable_daftar_produk_sinkronisasi",
      fn: "ListDaftarProdukSinkronisasi",
      warning_text: '<td colspan="6">Daftar produk tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarProdukSinkronisasi').val(), category: $('#category').val() } ,
   });
}

function ListDaftarProdukSinkronisasi(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td><b>${json.product_code}</b><br>${json.product_name}<br><span style="color:orange;">(Harga : Rp ${numberFormat(json.price)})</span></td>
                  <td>${json.operator}</td>
                  <td id="iak_${json.id}" >
                     <span onClick="getIAKList(${json.id})"> ${json.product_name_iak == null ? `<b>Tidak Ditemukan</b>` : `<b style="color:green;">${json.product_name_iak}</b>`} </span>
                  </td>
                  <td id="tripay_${json.id}">
                     <span onClick="getTRIPAYList(${json.id})"> ${json.product_name_tripay == null ? `<b>Tidak Ditemukan</b>` : `<b style="color:green;">${json.product_name_tripay}</b>`} </span>
                  </td>
                  <td>${json.updated_at}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Abaikan Produk" 
                        onclick="abaikanProduk(${json.id})" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-eye-slash" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;            
}

function getIAKList(id){
   ajax_x(
      baseUrl + "Superman/PPOB/get_list_product_iak", function(e) {
         if( e['error'] == false ) {
            var html = `<div class="row">
                           <div class="col-8">
                              <div class="form-group">
                                 <select class="form-control form-control-sm sel" id="select_iak_${id}">`;
                        for( x in e.data ) {
                           html +=  `<option value="${e.data[x].id}" > (${e.data[x].type} : ${e.data[x].operator}) ${e.data[x].product_name}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-4 px-0">
                              <button type="button" class="btn btn-default btn-action" title="Simpan Koneksi Produk IAK" 
                                 onclick="savePerubahan('iak', ${id})" style="margin:.15rem .1rem  !important">
                                 <i class="fas fa-save" style="font-size: 11px;"></i>
                              </button>
                              <button type="button" class="btn btn-default btn-action" title="Delete Koneksi" onclick="deleteKoneksi('iak', ${id})" style="margin:.15rem .1rem  !important">
                                  <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </div>
                        <script>
                           $(".sel").select2({
                              tags: true,
                           });
                        </script>`;
            $('#iak_'+id ).html(html);            
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}


function getTRIPAYList(id){
    ajax_x(
      baseUrl + "Superman/PPOB/get_list_product_tripay", function(e) {
         if( e['error'] == false ) {
            var html = `<div class="row">
                           <div class="col-8">
                              <div class="form-group">
                                 <select class="form-control form-control-sm sel" id="select_tripay_${id}">`;
                        for( x in e.data ) {
                           html +=  `<option value="${e.data[x].id}" > (${e.data[x].category} : ${e.data[x].operator}) ${e.data[x].product_name}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-4 px-0">
                              <button type="button" class="btn btn-default btn-action" title="Simpan Koneksi Produk Tripay" 
                                 onclick="savePerubahan('tripay', ${id})" style="margin:.15rem .1rem  !important">
                                 <i class="fas fa-save" style="font-size: 11px;"></i>
                              </button>
                              <button type="button" class="btn btn-default btn-action" title="Delete Koneksi" onclick="deleteKoneksi('tripay', ${id})" style="margin:.15rem .1rem  !important">
                                  <i class="fas fa-times" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </div>
                        <script>
                           $(".sel").select2({
                              tags: true,
                           });
                        </script>`;
            $('#tripay_'+id ).html(html);            
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function savePerubahan(server, id){
   var server_id ='';
   if( server == 'iak' ){
      server_id = $(`#select_iak_` + id).val();
   }else if( server == 'tripay' ) {
      server_id = $(`#select_tripay_` + id).val();
   }
   ajax_x(
      baseUrl + "Superman/PPOB/simpanPerubahanKoneksi", function(e) {
         if( e['error'] == false ) {
             smile_alert(e['error_msg']);
             // sinkronisasi_produk_getData();
              get_sinkronisasi_produk(300);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{server:server, id:id, server_id: server_id}]
   );
}

function deleteKoneksi(server, id){
   ajax_x(
      baseUrl + "Superman/PPOB/deleteKoneksi", function(e) {
         if( e['error'] == false ) {
             smile_alert(e['error_msg']);
               get_sinkronisasi_produk(300);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{server:server, id:id}]
   );
}