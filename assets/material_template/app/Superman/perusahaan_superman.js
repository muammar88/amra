function daftar_perusahaan_Pages(){
	return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-9 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_perusahaan()">
                        <i class="fas fa-building"></i> Tambah Perusahaan
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_perusahaan(20)" id="searchAllDaftarPerusahaan" name="searchAllDaftarPerusahaan" placeholder="Kode / Nama Perusahaan" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_perusahaan(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:13%;">Info Perusahaan</th>
                              <th style="width:17%;">Alamat</th>
                              <th style="width:15%;">Whatsapp</th>
                              <th style="width:15%;">Email</th>
                              <th style="width:15%;">Berlangganan</th>
                              <th style="width:10%;">Saldo</th>
                              <th style="width:15%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_perusahaan">
                           <tr>
                              <td colspan="7">Daftar perusahaan tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_perusahaan"></div>
                  </div>
               </div>
            </div>`;
}


function daftar_perusahaan_getData() {
  get_daftar_perusahaan(20);
}

function get_daftar_perusahaan(perpage){
	get_data(perpage, {
      url: "Superman/server_perusahaan",
      pagination_id: "pagination_daftar_perusahaan",
      bodyTable_id: "bodyTable_daftar_perusahaan",
      fn: "ListDaftarPerusahaan",
      warning_text: '<td colspan="7">Daftar perusahaan tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarPerusahaan').val() } ,
   });
}

// list daftar perusahaan
function ListDaftarPerusahaan(JSONData){
	var json = JSON.parse(JSONData);
	var html = `<tr>
               	<td class="text-left">
                     #<b>${json.code}</b><br>
                     <b>${json.name}</b><br>
                     <b style="text-transform:uppercase;color:orange;">${json.company_type}</b>
                  </td>
               	<td>
                     <b>${json.address != '' ? json.address : '-'}</b><br>
                     <b>${json.city != '' ? json.city : '-'}</b><br>
                     <b>${json.pos_code != '' ? json.pos_code : '-'}</b>
                  </td>
               	<td>${json.whatsapp_number}</td>
               	<td>${json.email}</td>
               	<td class="text-left">
                     <b>Mulai</b> : ${json.start_date_subscribtion != null ? json.start_date_subscribtion : '-' }<br> 
                     <b>Berakhir</b> : ${json.end_date_subscribtion != null ? json.end_date_subscribtion : '-'}</td>
               	<td>Rp ${numberFormat(json.saldo)}</td>
               	<td>
                     <button type="button" class="btn btn-default btn-action" title="Tambah Waktu Berlangganan" onclick="tambah_waktu_berlangganan_perusahaan(${
                       json.id
                     })" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-money-check" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Tambah Saldo Perusahaan" onclick="tambah_saldo_perusahaan(${
                       json.id
                     })" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-money-bill-wave" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Ambil Biaya Berlangganan" onclick="take_charge(${
                       json.id
                     })" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-money-bill-wave-alt" style="font-size: 11px;"></i>
                     </button>
               		<button type="button" class="btn btn-default btn-action" title="Edit Perusahaan" onclick="edit_perusahaan(${
	                    json.id
	                  })" style="margin:.15rem .1rem  !important">
	                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
	                </button>
	                <button type="button" class="btn btn-default btn-action" title="Delete Perusahaan" onclick="delete_perusahaan(${
	                    json.id
	                  })" style="margin:.15rem .1rem  !important">
	                      <i class="fas fa-times" style="font-size: 11px;"></i>
	                </button>
               	</td>
            	</tr>`;
   return html;
}

function take_charge(id){
   $.confirm({
      title: "Ambil Biaya Berlangganan Perusahaan",
      theme: "material",
      columnClass: "col-4",
      content: formTakeCharge(id),
      closeIcon: false,
      buttons: {
         cancel: function () {
           return true;
         },
         simpan: {
            text: 'Ambil Biaya Perusahaan',
            btnClass: 'btn-blue',
            action: function () {
               var ajax = ajax_submit_r("#form_utama");
               if ( ajax['error'] == true ) {
                  return false;
               } else {
                  get_daftar_perusahaan(20);
               }
            }
         }
      },
   });
}

function formTakeCharge(id){
   var html = `<form action="${baseUrl}Superman/TakeCharge" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <input type="hidden" value="${id}" name="id">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Ambil Biaya</label>
                                  <select class="form-control form-control-sm" id="tipe" name="tipe" title="Pilih Tipe">
                                    <option value="pilih_tipe">Pilih Tipe</option>
                                    <option value="berlangganan">Berlangganan</option>
                                    <option value="pembelian">Pembelian</option>
                                 </select>
                              </div>
                           </div>
                            <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Ambil Biaya</label>
                                 <input type="text" name="biaya" id="biaya" class="form-control form-control-sm currency" placeholder="Biaya" />
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

function tambah_saldo_perusahaan(id){
   ajax_x(
       baseUrl + "Superman/get_info_saldo_perusahaan",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
               title: "Tambah Saldo Perusahaan",
               theme: "material",
               columnClass: "col-4",
               content: formTambahSaldoPerusahaan( id, JSON.stringify( e.data ) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  simpan: {
                     text: 'Tambah Perusahaan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var ajax = ajax_submit_r("#form_utama");
                        if ( ajax['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_perusahaan(20);
                        }
                     }
                  }
               },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}


function formTambahSaldoPerusahaan(id, data){
   var json = JSON.parse(data);
   var html = `<form action="${baseUrl}Superman/proses_tambah_saldo" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <input type="hidden" value="${id}" name="id">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Saldo Sekarang</label>
                                 <input type="text" value="Rp ${numberFormat(json.saldo)}" class="form-control form-control-sm" disabled/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Saldo Yang Ditambah</label>
                                 <input type="text" name="saldo" id="saldo" class="form-control form-control-sm currency" placeholder="Saldo" />
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

// add perusahaan
function add_perusahaan(){
	ajax_x(
       baseUrl + "Superman/get_info_tambah_perusahaan",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
   		      title: "Tambah Perusahaan",
   		      theme: "material",
   		      columnClass: "col-6",
   		      content: formTambahPerusahaan( JSON.stringify( e.data ) ),
   		      closeIcon: false,
   		      buttons: {
   		         cancel: function () {
   		           return true;
   		         },
   		         simpan: {
   		            text: 'Tambah Perusahaan',
   		            btnClass: 'btn-blue',
   		            action: function () {
                        var ajax = ajax_submit_r("#form_utama");

                        console.log("ajax");
                        console.log(ajax);
                        console.log("ajax");
                        if ( ajax['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_perusahaan(20);
                        }
   		            }
   		         }
   		      },
   		   });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    []
   );
}


function edit_perusahaan(id){
   ajax_x(
       baseUrl + "Superman/get_info_edit_perusahaan",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
               title: "Tambah Perusahaan",
               theme: "material",
               columnClass: "col-6",
               content: formTambahPerusahaan( JSON.stringify( e.data ), JSON.stringify(e.value) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  simpan: {
                     text: 'Tambah Perusahaan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var ajax = ajax_submit_r("#form_utama");

                        console.log("ajax");
                        console.log(ajax);
                        console.log("ajax");
                        if ( ajax['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_perusahaan(20);
                        }
                     }
                  }
               },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
    [{id:id}]
   );
}

function formTambahPerusahaan( JSONData, JSONValue ) {
	var json = JSON.parse(JSONData);
	var id = '';
	var kode_perusahaan = json;
	var nama_perusahaan = '';
	var tipe_perusahaan = '';
	var no_whatsapp = '';
	var mulai_berlangganan = '';
	var akhir_berlangganan = '';
	var email = '';
   var saldo = 0;
	if( JSONValue != undefined ){
		var value = JSON.parse(JSONValue);
		id = `<input type="hidden" name="id" value="${value.id}">`;
		kode_perusahaan = value.code;
		nama_perusahaan = value.name;
		tipe_perusahaan = value.company_type;
		no_whatsapp = value.whatsapp_number;
		mulai_berlangganan = value.start_date_subscribtion;
		akhir_berlangganan = value.end_date_subscribtion;
		email = value.email;
      saldo = value.saldo;
	}
	var html = `<form action="${baseUrl}Superman/proses_tambah_perusahaan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                        	${id}
                        	<div class="col-4">
                              <div class="form-group mb-2">
                                 <label>Kode Perusahaan</label>
                                 <input type="text" value="${kode_perusahaan}" class="form-control form-control-sm" placeholder="Kode Perusahaan" disabled />
                                 <input type="hidden" name="kode" value="${kode_perusahaan}"  />
                              </div>
                           </div>
                           <div class="col-8">
                              <div class="form-group mb-2">
                                 <label>Nama Perusahaan</label>
                                 <input type="text" name="nama" id="nama" value="${nama_perusahaan}" class="form-control form-control-sm" placeholder="Nama Perusahaan" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Tipe Perusahaan</label>
                                 <select class="form-control form-control-sm" name="tipe"  id="tipe" >
                                    <option value="limited" ${tipe_perusahaan == 'limited' ? 'selected': ''}>Limited</option>
                                    <option value="unlimited" ${tipe_perusahaan == 'unlimited' ? 'selected': ''}>Unlimited</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>No Whatsapp</label>
                                 <input type="text" name="whatsapp" id="whatsapp" value="${no_whatsapp}" class="form-control form-control-sm" placeholder="Nomor Whatsapp"  />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>Mulai Berlangganan</label>
                                 <input type="date" name="mulai_berlangganan" id="mulai_berlangganan" value="${mulai_berlangganan}"  class="form-control form-control-sm" placeholder="Mulai Berlangganan"  />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>Akhir Berlangganan</label>
                                 <input type="date" name="akhir_berlangganan" id="akhir_berlangganan" value="${akhir_berlangganan}"  class="form-control form-control-sm" placeholder="Akhir Berlangganan" disabled  />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group mb-2">
                                 <label>Durasi</label>
                                 <input type="number" id="durasi" name="durasi" value="0" onKeyup="HitWaktuBerlanggan()" class="form-control form-control-sm" placeholder="Durasi Waktu Berlangganan dalam Bulan"/>
                              </div>
                           </div>
                           <div class="col-8">
                              <div class="form-group mb-2">
                                 <label>Email</label>
                                 <input type="email" name="email" id="email" value="${email}" class="form-control form-control-sm" placeholder="Email Perusahaan"  />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Saldo</label>
                                 <input type="text" name="saldo" id="saldo" value="Rp ${numberFormat(saldo)}" class="form-control form-control-sm currency" placeholder="Saldo Perusahaan"  />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>Password</label>
                                 <input type="password" name="password" id="password" value="" class="form-control form-control-sm" placeholder="Password Perusahaan"  />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>Konfirmasi Password</label>
                                 <input type="password" name="password_conf" id="password_conf" value="" class="form-control form-control-sm" placeholder="Konfirmasi Password"  />
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

// hitung waktu berlangganan
function HitWaktuBerlanggan(){
   var mulai_berlangganan = $('#mulai_berlangganan').val();
   var durasi = $('#durasi').val();
   // filter
   if( durasi != '' && mulai_berlangganan != '' ) {
      ajax_x_t2(
         baseUrl + "Superman/hit_waktu_berlangganan",
         function (e) {
            if (e["error"] == false) {
               $('#akhir_berlangganan').val(e.data);
            } else {
               frown_alert(e["error_msg"]);
            }
         },
         [{mulai_berlangganan:mulai_berlangganan, durasi:durasi}]
      );
   }
}

// delete perusahaan
function delete_perusahaan(id){
   ajax_x_t2(
      baseUrl + "Superman/delete_perusahaan",
      function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_daftar_perusahaan(20);
         } else {
            frown_alert(e["error_msg"]);
         }
      },
      [{id:id}]
   );
}

function tambah_waktu_berlangganan_perusahaan(id){
   ajax_x_t2(
      baseUrl + "Superman/get_info_tambah_waktu_by_berlangganan",
      function (e) {
         if (e["error"] == false) {

            $.confirm({
               title: "Tambah Waktu Berlangganan Perusahaan",
               theme: "material",
               columnClass: "col-4",
               content: formTambahWaktuBerlanggananPerusahaan( id,  JSON.stringify( e.data ) ),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                    return true;
                  },
                  simpan: {
                     text: 'Tambah Waktu Berlangganan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var ajax = ajax_submit_r("#form_utama");
                        if ( ajax['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_perusahaan(20);
                        }
                     }
                  }
               },
            });
         } else {
            frown_alert(e["error_msg"]);
         }
      },
      [{id:id}]
   );
}


function formTambahWaktuBerlanggananPerusahaan(id, JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl}Superman/proses_tambah_waktu_berlangganan_per_perusahaan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Nama Perusahaan</label>
                                 <input type="text" value="${json.name}" class="form-control form-control-sm" disabled/>
                                 <input type="hidden" value="${json.id}" name="id" id="id"/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Start Date</label>
                                 <input type="text" name="start_date" id="start_date" value="${json.start_date_subscribtion}" class="form-control form-control-sm" placeholder="Durasi" disabled/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>End Date</label>
                                 <input type="text" name="end_date" id="end_date" value="${json.end_date_subscribtion}" class="form-control form-control-sm" placeholder="Durasi" disabled/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Durasi</label>
                                 <input type="number" onKeyup="CountDurasiBerlangganan()" name="durasi" id="durasi" class="form-control form-control-sm" placeholder="Durasi"  />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html; 
}


function CountDurasiBerlangganan(){
   var id = $('#id').val();
   var durasi = $('#durasi').val();
   if( durasi != '' ) {
      ajax_x_t2(
         baseUrl + "Superman/CountDurasiBerlangganan",
         function (e) {
            if ( e["error"] == false ) {
               $('#start_date').val(e.data.start_date);
               $('#end_date').val(e.data.end_date);
            } else {
               frown_alert(e["error_msg"]);
            }
         },
         [{id:id, durasi:durasi}]
      );
   }
}




   function logout_superman() {
      ajax_x(
         baseUrl + "Auth/logout",
         function(e) {
            if (e['error'] == false) {
               window.location.href = baseUrl + "Auth";
            }
         },
         []
      );
   }