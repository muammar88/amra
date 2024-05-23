function dashboard_superman_Pages() { 
  return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentBerandaUtama">
                  <div class="col-lg-12 col-6 mb-3">
                     <div class="row" style="height: 100%;">
                        <div class="col-lg-3 col-6 mb-3">
                           <!-- small box -->
                           <div class="small-box bg-info" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="total_perusahaan">0</h3>
                                 <p class="my-5"><b>PERUSAHAAN </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">TERDAFTAR</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-building"></i>
                              </div>
                              <a onClick="perusahaan_terdaftar()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6 mb-3">
                           <div class="small-box bg-info" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="total_ppob_costumer">0</h3>
                                 <p class="my-5"><b>PELANGGAN PPOB</b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">TERDAFTAR</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-user"></i>
                              </div>
                              <a onClick="saldo_serpul()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6 mb-3">
                           <div class="small-box bg-danger" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="laba_amra">0</h3>
                                 <p class="my-5"><b>LABA </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">AMRA</span></p>
                              </div>
                              <div class="icon"><i class="fas fa-trophy"></i></div>
                              <a onClick="refresh_laba_amra()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6 mb-3">
                           <div class="small-box bg-danger" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="total_saldo_pelanggan">0</h3>
                                 <p class="my-5"><b>TOTAL SALDO </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">PELANGGAN</span></p>
                              </div>
                              <div class="icon"><i class="fas fa-trophy"></i></div>
                              <a onClick="refresh_laba_amra()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6 ">
                           <div class="small-box bg-success" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="saldo_iak">0</h3>
                                 <p class="my-5"><b>SALDO </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">IAK</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-money-bill-wave"></i>
                              </div>
                              <a onClick="saldo_serpul()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6">
                           <div class="small-box bg-success" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="saldo_tripay">0</h3>
                                 <p class="my-5"><b>SALDO </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">TRIPAY</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-money-bill-wave"></i>
                              </div>
                              <a onClick="saldo_serpul()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                           <div class="small-box bg-warning" style="height: 100%;">
                              <div class="inner">
                                 <h3 id="saldo_pelanggan">Rp 0,-</h3>
                                 <p class="my-5"><b>SALDO </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;" >PELANGGAN</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-money-bill"></i>
                              </div>
                              <a onClick="detail_saldo_perusahaan()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6">
                           <div class="small-box bg-warning" style="height: 100%;">
                              <div class="inner">
                                 <h3 id="saldo_perusahaan">Rp 0,-</h3>
                                 <p class="my-5"><b>SALDO </b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;" >PERUSAHAAN</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-money-bill"></i>
                              </div>
                              <a onClick="detail_saldo_perusahaan()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        
                     </div>
                  </div>
                  <div class="col-lg-4 col-6 mb-3">
                     <div class="row" >
                        <div class="col-6 col-lg-6 mb-0 mt-0 ">
                          <label class="float-left py-2 my-3">Transaksi PPOB hari ini :</label>
                        </div>
                        <div class="col-lg-6 my-3 text-right">
                           <div class="input-group ">
                              <input class="form-control form-control-sm" type="text" onkeyup="get_data_ppob_superman(10)" id="searchPPOBSuperman" name="searchPPOBSuperman" placeholder="Nomor Transaksi / Nomor Tujuan / Kode Produk" style="font-size: 12px;">
                           </div>
                        </div>
                        <div class="col-12 col-lg-12">
                           <table class="table" >
                              <thead>
                                 <tr>
                                    <th style="width:40%;">Produk</th>
                                    <th style="width:25%;">Harga</th>
                                    <th style="width:20%;">Status</th>
                                    <th style="width:15%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_ppob_superman">
                                 <td colspan="4">Daftar transaksi ppob tidak ditemukan</td>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-0" >
                           <div class="row" id="pagination_ppob_superman"></div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-4">
                     <div class="row" >
                        <div class="col-6 col-lg-6 mb-0 mt-0 ">
                          <label class="float-left py-2 my-3">Request Tambah Saldo :</label>
                        </div>
                        <div class="col-6 col-lg-6 mb-0 mt-3 text-right">
                           <button class="btn btn-default" type="button" onclick="tambahSaldoPerusahaan()">
                              <i class="fas fa-plus"></i> Tambahkan Saldo Perusahaan
                           </button>
                        </div>
                        <div class="col-12 col-lg-12">
                           <table class="table" >
                              <thead>
                                 <tr>
                                    <th style="width:30%;">Info Perusahaan</th>
                                    <th style="width:35%;">Info Trans</th>
                                    <th style="width:20%;">Tgl. Trans</th>
                                    <th style="width:15%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_request_tambah_saldo_superman">
                                 <td colspan="4">Daftar request tambah saldo tidak ditemukan</td>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-0" >
                           <div class="row" id="pagination_request_tambah_saldo_superman"></div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-4">
                     <div class="row">
                        <div class="col-lg-6">
                           <label class="float-left py-2 my-3">Request Perpanjangan Langganan :</label>
                        </div>
                         <div class="col-6 col-lg-6 mb-0 mt-3 text-right">
                           <button class="btn btn-default" type="button" onclick="tambahWaktuBerlangganan()">
                              <i class="fas fa-plus"></i> Tambahkan Waktu Berlangganan
                           </button>
                        </div>
                        <div class="col-lg-12">
                           <table class="table" >
                              <thead>
                                 <tr>
                                    <th style="width:40%;">Info Perusahaan</th>
                                    <th style="width:40%;">Tgl. Belangganan</th>
                                    <th style="width:20%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_request_tambah_waktu_berlangganan">
                                 <td colspan="3">Daftar request tambah waktu berlangganan tidak ditemukan</td>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-3" >
                           <div class="row" id="pagination_request_tambah_waktu_berlangganan"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>`;
}


// <div class="col-lg-2 col-6 mb-3">
//    <div class="alert alert-danger alert-dismissible">
//       <h4><i class="icon fa fa-ban"></i> Alert!</h4>
//       Danger alert preview. This alert is dismissable. A wonderful serenity has taken possession of my entire
//       soul, like these sweet mornings of spring which I enjoy with my whole heart.
//    </div>
// </div>
// <div class="col-lg-2 col-6 mb-3">
//    <div class="alert alert-dismissible alert-success">
//      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
//      <h4 class="alert-heading">Warning!</h4>
//      <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
//    </div>
// </div>
// <div class="col-lg-2 col-6 mb-3">
//    <div class="alert alert-dismissible alert-success">
//      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
//      <h4 class="alert-heading">Warning!</h4>
//      <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
//    </div>
// </div>
// <div class="col-lg-2 col-6 mb-3">
//    <div class="alert alert-dismissible alert-success">
//      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
//      <h4 class="alert-heading">Warning!</h4>
//      <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
//    </div>
// </div>
// <div class="col-lg-2 col-6 mb-3">
//    <div class="alert alert-dismissible alert-success">
//      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
//      <h4 class="alert-heading">Warning!</h4>
//      <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
//    </div>
// </div>
// <div class="col-lg-2 col-6 mb-3">
//    <div class="alert alert-dismissible alert-success">
//      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
//      <h4 class="alert-heading">Warning!</h4>
//      <p class="mb-0">Best check yo self, you're not looking too good. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, <a href="#" class="alert-link">vel scelerisque nisl consectetur et</a>.</p>
//    </div>
// </div>

function dashboard_superman_getData() {
  get_data_dashboard_superman();
  get_data_ppob_superman(10);
  get_data_tambah_saldo(10);
  get_data_request_waktu_berlangganan(10);
}

function get_data_request_waktu_berlangganan(perpage){
   get_data(perpage, {
      url: "Superman/get_data_request_waktu_berlangganan",
      pagination_id: "pagination_request_tambah_waktu_berlangganan",
      bodyTable_id: "body_request_tambah_waktu_berlangganan",
      fn: "ListDaftarRequestWaktuBerlangganan",
      warning_text: '<td colspan="3">Daftar request tambah waktu berlangganan tidak ditemukan</td>',
      param: [] ,
   });
}

function ListDaftarRequestWaktuBerlangganan(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td class="text-left">
                     <b>C</b> : #<b>${json.code}</b><br>
                     <b>N</b> : ${json.name}<br>
                     <b>PPM</b> : Rp ${numberFormat(json.pay_per_month)}<br>
                     <b>T</b> : Rp ${numberFormat(json.total)}</td>
                  <td class="text-left">
                     <b>S</b> : ${json.start_date_subscribtion}<br>
                     <b>E</b> : ${json.end_date_subscribtion}<br>
                     <b>STATUS</b> : ${json.status == 'process' ? `<span style="color:orange;text-transform:uppercase;">${json.status}</span>` : (json.status == 'accept' ? `<span style="color:green;text-transform:uppercase;">${json.status}</span>` : `<span style="color:red;text-transform:uppercase;">${json.status}</span>`)}</td>
                  <td>`;
         if( json.status == 'process'){
            html += `<button type="button" class="btn btn-default btn-action" title="Reject Permintaan Request Tambah Waktu Berlangganan" 
                        onclick="rejectRequestWaktuBerlangganan(${json.id})" 
                        style="margin:.15rem .1rem  !important;background-color: #d06464 !important;color: white!important;">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Setujui Permintaan Request Tambah Waktu Berlangganan" 
                        onclick="approveRequestWaktuBerlangganan(${json.id})" 
                        style="margin: 0.15rem 0.1rem !important;background-color: #4fa845 !important;color: white!important;">
                        <i class="fas fa-check" style="font-size: 11px;"></i>
                     </button>`;
         }else{
            html += '-';
         }
      html +=    `</td>
               </tr>`;
   return html;
}

function rejectRequestWaktuBerlangganan(id){
   ajax_x(
       baseUrl + "Superman/rejectRequestWaktuBerlangganan",
       function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_data_request_waktu_berlangganan(10);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}


function approveRequestWaktuBerlangganan(id){
   ajax_x(
       baseUrl + "Superman/approveRequestWaktuBerlangganan",
       function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_data_request_waktu_berlangganan(10);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}

function get_data_tambah_saldo(perpage){
   get_data(perpage, {
      url: "Superman/get_request_data_tambah_saldo",
      pagination_id: "pagination_request_tambah_saldo_superman",
      bodyTable_id: "body_request_tambah_saldo_superman",
      fn: "ListDaftarRequestTambahSaldo",
      warning_text: '<td colspan="4">Daftar request tambah saldo tidak ditemukan</td>',
      param: [] ,
   });
}

function ListDaftarRequestTambahSaldo(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<tr>
                  <td><b>${json.code}</b><br>${json.name}</td>
                  <td>Rp ${numberFormat(json.saldo)}<br><b>${json.status}</b></td>
                  <td>${json.transaction_date}</td>
                  <td>`;
         if( json.status == 'process'){
            html += `<button type="button" class="btn btn-default btn-action" title="Reject Permintaan Request Tambah Saldo" 
                        onclick="rejectRequestTambahSaldo(${json.id})" 
                        style="margin:.15rem .1rem  !important;background-color: #d06464 !important;color: white!important;">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Setujui Permintaan Request Tambah Saldo" 
                        onclick="approveRequestTambahSaldo(${json.id})" 
                        style="margin: 0.15rem 0.1rem !important;background-color: #4fa845 !important;color: white!important;">
                        <i class="fas fa-check" style="font-size: 11px;"></i>
                     </button>`;
         }else{
            html += '-';
         }
      html +=    `</td>
               </tr>`;
  return html;
}

function rejectRequestTambahSaldo(id){
   ajax_x(
       baseUrl + "Superman/rejectTambahSaldo",
       function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_data_tambah_saldo(10);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}

function approveRequestTambahSaldo(id){
    ajax_x(
       baseUrl + "Superman/approveTambahSaldo",
       function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_data_tambah_saldo(10);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}

function get_data_ppob_superman(perpage){
   get_data(perpage, {
      url: "Superman/get_data_ppob_superman",
      pagination_id: "pagination_ppob_superman",
      bodyTable_id: "body_ppob_superman",
      fn: "ListDaftarPPOBSuperman",
      warning_text: '<td colspan="4">Daftar transaksi ppob tidak ditemukan</td>',
      param: { search : $('#searchPPOBSuperman').val() } ,
   });
}

function ListDaftarPPOBSuperman(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<tr>
                  <td class="text-left"><b>T</b> : #${json.transaction_code}<br><b>P</b> : <b>${json.product_code}</b><br><b>NT</b> : ${json.nomor_tujuan}</td>
                  <td class="text-left"><b>S</b> : Rp ${numberFormat(json.server_price)} <br><b>A</b> : Rp ${numberFormat(json.application_price)} <br><b>LB</b> : Rp ${ json.status == 'failed' ? '0' : numberFormat(json.application_price - json.server_price)} </td>
                  <td><b>${json.status == 'failed' ? '<span style="color:red">FAILED</span>' : (json.status == 'success' ? '<span style="color:green">SUCCESS</span>' : '<span style="color:orange">PROSES</span>' ) }</b> <br>${json.created_at}</td>
                  <td></td>
               </tr>`;
  return html;
}

function get_data_dashboard_superman(){
    ajax_x(
       baseUrl + "Superman/get_info_superman_dashboard",
       function (e) {
         if (e["error"] == false) {
           $("#total_perusahaan").html(e.data.total_perusahaan);
           $("#saldo_perusahaan").html(numberFormat(e.data.saldo_perusahaan));
           $("#saldo_pelanggan").html(numberFormat(e.data.saldo_pelanggan));
           $("#total_saldo_pelanggan").html(numberFormat(e.data.saldo_pelanggan + e.data.saldo_perusahaan));
           $("#saldo_iak").html(numberFormat(e.data.saldo_amra.iak));
           $("#saldo_tripay").html(numberFormat(e.data.saldo_amra.tripay));
           $("#laba_amra").html(numberFormat(e.data.laba_amra));
           $("#total_ppob_costumer").html(e.data.total_ppob_costumer);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    []
   );
}

function perusahaan_terdaftar( ) {
   $.confirm({
      title: "Daftar Perusahaan",
      theme: "material",
      columnClass: "col-12",
      content: formDaftarPerusahaan(),
      closeIcon: false,
      buttons: {
         cancel: function () {
           return true;
         },
      },
   });
   get_daftar_perusahaan(10);
}

function formDaftarPerusahaan(){
   var html = `<div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:30%;">Nama Perusahaan</th>
                           <th style="width:15%;">Tipe Perusahaan</th>
                           <th style="width:15%;">Saldo</th>
                           <th style="width:20%;">Mulai Berlangganan</th>
                           <th style="width:20%;">Berakhir Berlangganan</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTableDaftarPerusahaanDashboard">
                        <tr>
                           <td colspan="5">Daftar perusahaan tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="paginationDaftarPerusahaanDashboard"></div>
               </div>`;
   return html;            
}

function get_daftar_perusahaan(perpage) {
   get_data(perpage, {
    url: "Superman/daftar_perusahaan",
    pagination_id: "paginationDaftarPerusahaanDashboard",
    bodyTable_id: "bodyTableDaftarPerusahaanDashboard",
    fn: "ListDaftarPerusahaanDashboard",
    warning_text: '<td colspan="5">Daftar perusahaan tidak ditemukan</td>',
    param: [],
  });
}

function ListDaftarPerusahaanDashboard( JSONData ) {
    var json = JSON.parse(JSONData);
  var html = `<tr>
                  <td><b>${json.code}</b>/<br>${json.name}</td>
                  <td style="text-transform: uppercase;"><b>${json.company_type}</b></td>
                  <td>Rp ${numberFormat(json.saldo)}</td>
                  <td>${json.start_date_subscribtion != null ? json.start_date_subscribtion : '-'}</td>
                  <td>${json.end_date_subscribtion != null ? json.end_date_subscribtion : '-'}</td>
               </tr>`;
  return html;
}

function tambahSaldoPerusahaan(){
   ajax_x(
       baseUrl + "Superman/get_info_tambah_saldo_perusahaan",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
               title: "Tambah Saldo Perusahaan",
               theme: "material",
               columnClass: "col-4",
               content: formTambahSaldoPerusahaan( JSON.stringify(e.data) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  tambah: {
                    text: "Tambah Saldo Perusahaan",
                    btnClass: "btn-blue",
                    action: function () {
                      ajax_submit_t1("#form_utama", function (e) {
                        e["error"] == true
                          ? frown_alert(e["error_msg"])
                          : smile_alert(e["error_msg"]);
                          if( e["error"] == false ){
                              get_data_tambah_saldo(10);
                          }
                      });
                    },
                  },
               },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    []
   );
}

function formTambahSaldoPerusahaan(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<form action="${baseUrl}Superman/proses_tambah_saldo_perusahaan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Perusahaan</label>
                                 <select class="form-control form-control-sm" name="perusahaan"  id="perusahaan" onChange="CountSaldoPerusahaanTerakhir()">
                                    <option value="0">Pilih Perusahaan</option>`;
                        for( c in json ) {
                           html += `<option value="${c}">${json[c]}</option>`;
                        }
                     html +=    `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Saldo Yang Ditambah</label>
                                 <input type="text" name="saldo" id="saldo_deposit" value="" class="form-control form-control-sm currency" placeholder="Saldo" onKeyup="CountSaldoPerusahaanTerakhir()" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Total Saldo Sekarang</label>
                                 <input type="text" id="total_saldo" value="Rp. 0" class="form-control form-control-sm" disabled />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $(document).on( "keyup", ".currency", function(e){
                      var e = window.event || e;
                      var keyUnicode = e.charCode || e.keyCode;
                          if (e !== undefined) {
                              switch (keyUnicode) {
                                  case 16: break;
                                  case 27: this.value = ''; break;
                                  case 35: break;
                                  case 36: break;
                                  case 37: break;
                                  case 38: break;
                                  case 39: break;
                                  case 40: break;
                                  case 78: break;
                                  case 110: break;
                                  case 190: break;
                                  default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                              }
                          }
                  } );
               </script>`;
   return html;            
}

function CountSaldoPerusahaanTerakhir(){
   var perusahaan = $('#perusahaan').val();
   var saldo_deposit = $('#saldo_deposit').val() == '' ? 'Rp 0' : $('#saldo_deposit').val();
   if( perusahaan == '0' ) {
      $('#total_saldo').val('Rp. 0');
   }else{
      ajax_x_t2(
          baseUrl + "Superman/countSaldoPerusahaanTerakhir",
          function (e) {
            if (e["error"] == false) {
               $('#total_saldo').val('Rp. ' + numberFormat(e.data));
            } else {
               $('#total_saldo').val('Rp 0');
            }
          },
       [{ perusahaan : perusahaan, saldo: saldo_deposit } ]
      );
   }
}

function tambahWaktuBerlangganan(){
   ajax_x(
       baseUrl + "Superman/get_info_tambah_waktu_berlangganan",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
               title: "Tambah Waktu Berlangganan",
               theme: "material",
               columnClass: "col-4",
               content: formTambahWaktuBerlangganan( JSON.stringify(e.data) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  tambah: {
                    text: "Tambah Saldo Perusahaan",
                    btnClass: "btn-blue",
                    action: function () {
                      ajax_submit_t1("#form_utama", function (e) {
                        e["error"] == true
                          ? frown_alert(e["error_msg"])
                          : smile_alert(e["error_msg"]);
                          if( e["error"] == false ){
                              get_data_tambah_saldo(10);
                          }
                      });
                    },
                  },
               },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    []
   );
}

function formTambahWaktuBerlangganan(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<form action="${baseUrl}Superman/proses_tambah_waktu_berlangganan_perusahaan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Perusahaan</label>
                                 <select class="form-control form-control-sm" name="perusahaan"  id="perusahaan" onChange="countTambahWwaktuBerlanggan()">
                                    <option value="0">Pilih Perusahaan</option>`;
                        for( c in json ) {
                           html += `<option value="${c}">${json[c]}</option>`;
                        }
                     html +=    `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Start Date</label>
                                 <input type="text" name="start_date" id="start_date" class="form-control form-control-sm" placeholder="Durasi" disabled/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>End Date</label>
                                 <input type="text" name="end_date" id="end_date" class="form-control form-control-sm" placeholder="Durasi" disabled/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Durasi</label>
                                 <input type="number" onKeyup="countTambahWwaktuBerlanggan()" name="durasi" id="durasi" class="form-control form-control-sm" placeholder="Durasi"  />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;        
}

function countTambahWwaktuBerlanggan(){
   var id_perusahaan = $('#perusahaan').val();
   var durasi = $('#durasi').val();
   if( durasi != '' ){
      ajax_x_t2(
         baseUrl + "Superman/count_tambah_waktu_berlangganan",
          function (e) {
            if (e["error"] == false) {
               $('#start_date').val(e.data.start_date_subscribtion);
               $('#end_date').val(e.data.end_date_subscribtion);
            } else {
              frown_alert(e["error_msg"]);
            }
          },
       [{id_perusahaan:id_perusahaan, durasi:durasi}]
      );
   }
}