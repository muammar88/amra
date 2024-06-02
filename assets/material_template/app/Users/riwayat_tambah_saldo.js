function riwayat_tambah_saldo_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-10 my-3 ">
                  	<button class="btn btn-default" type="button" onclick="tambah_saldo_perusahaan()">
                        <i class="fas fa-money-bill-wave"></i> Request Tambah Saldo
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-3 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_riwayat_tambah_saldo(20)" id="searchAllRiwayatTambahSaldo" name="searchAllRiwayatTambahSaldo" placeholder="Kode Transaksi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_riwayat_tambah_saldo(20)">
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
                              <th style="width:32%;">Info Bank</th>
                              <th style="width:13%;">Total Biaya Tambah Saldo</th>
                              <th style="width:10%;">Status</th>
                              <th style="width:10%;">Status Kirim</th>
                              <th style="width:13%;">Waktu Kirim</th>
                              <th style="width:12%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_riwayat_tambah_saldo">
                           <tr>
                              <td colspan="7">Daftar riwayat tambah saldo tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_riwayat_tambah_saldo"></div>
                  </div>
               </div>
            </div>`;
}

function riwayat_tambah_saldo_getData(){
   get_riwayat_tambah_saldo(20);
}

function get_riwayat_tambah_saldo(perpage){
	get_data( perpage,
             { url : 'Saldo_perusahaan/riwayat_tambah_saldo',
               pagination_id: 'pagination_daftar_riwayat_tambah_saldo',
               bodyTable_id: 'bodyTable_daftar_riwayat_tambah_saldo',
               fn: 'ListDaftarRiwayatTambahSaldo',
               warning_text: '<td colspan="7">Daftar riwayat tambah saldo tidak ditemukan</td>',
               param : { search : $('#searchAllRiwayatTambahSaldo').val() } } );
}

function ListDaftarRiwayatTambahSaldo(JSONData){
	var json = JSON.parse( JSONData );
	var html = `<tr>
					<td><b>#${json.kode}</b></td>
					<td>
						<table class="table mb-0">
							<tr>
								<td style="width:30%" class="text-left border-0"><b>NAMA BANK</b></td>
								<td style="width:1%" class="border-0"> : </td>
								<td class="text-left border-0"><b>${json.bank}</b></td>
							</tr>
							<tr>
								<td class="text-left border-0"><b>NAMA AKUN BANK</b></td>
								<td class="border-0"> : </td>
								<td class="text-left border-0"><b>${json.nama_akun_bank}</b></td>
							</tr>
							<tr>
								<td class="text-left border-0"><b>NOMOR AKUN BANK</b></td>
								<td class="border-0"> : </td>
								<td class="text-left border-0"><b>${json.nomor_akun_bank}</b></td>
							</tr>
						</table>
					</td>
					<td>${kurs} ${numberFormat(json.total_biaya )}</td>
					<td style="text-transform:uppercase; font-weight:bold;color: ${json.status == 'proses' ? 'orange' : ( json.status == 'disetujui' ? 'green' : 'red' ) }!important;">${json.status}</td>
					<td style="text-transform:uppercase;">${json.status_kirim == 'belum_dikirim' ? 'belum dikirim' : "sudah dikirim"}</td>
					<td>${json.waktu_kirim}</td>
					<td>`;
		if( json.status_kirim == 'belum_dikirim' ) {
			html += `<button type="button" class="btn btn-default btn-action" title="Batal Request Tambah Saldo"
                     onclick="batal_request_tambah_saldo('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Edit Riwayat Tambah Saldo"
                     onclick="edit_riwayat_tambah_saldo('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Konfirmasi Pengiriman Tambah Saldo"
                     onclick="konfirmasi_pengiriman_tambah_saldo('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-check-double" style="font-size: 11px;"></i>
                  </button>`;
		}else{
         if( json.status == 'ditolak') {
            html += `<span>${json.alasan_tolak}</span>`;
         }else{
            html += `<span>Biaya Sudah Dikirim</span>`;   
         }
			
		}
		html +=	   `</td>
				</tr>`;
	return html;			
}

function batal_request_tambah_saldo(id){
	ajax_x(
      baseUrl + "Saldo_perusahaan/delete_request_tambah_saldo", function(e) {
         if( e['error'] == false ){
         	smile_alert(e['error_msg']);
         }else{
            frown_alert(e['error_msg']);
         }
         get_riwayat_tambah_saldo(20);
      },[ { id : id }]
   );
}

// tambah saldo perusahaan
function tambah_saldo_perusahaan(){
	ajax_x(
      baseUrl + "Saldo_perusahaan/get_info_tambah_saldo", function(e) {
         if( e['error'] == false ){
				$.confirm({
			      columnClass: 'col-4',
			      title: 'Tambah Saldo Perusahaan',
			      theme: 'material',
			      content: formatambah_saldoperusahaan( JSON.stringify( e.list_bank ) ),
			      closeIcon: false,
			      buttons: {
			         cancel:function () {
			             return true;
			         },
			         simpan: {
			            text: 'Request Tambah Saldo',
			            btnClass: 'btn-blue',
			            action: function () {
								var ajax = ajax_submit_r("#form_utama");
                        if ( ajax['error'] == true ) {
                           return false;
                        } else {
                           get_riwayat_tambah_saldo(20);
                           return true;
                        }
                        ajax['error'] == true ? frown_alert(ajax['error_msg']) : smile_alert(ajax['error_msg']);
			            }
			         }

			      }
			   });
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function formatambah_saldoperusahaan(JSONData, JSONValue){
	var json = JSON.parse( JSONData );
	var id = '';
	var bank_id = '';
	var nominal = '';
	if( JSONValue != undefined ){
		var value = JSON.parse( JSONValue );
		id = `<input type="hidden" name="id" value="${value.id}">`;
		bank_id = value.id;
		nominal = kurs + ' ' + numberFormat(value.biaya);
	}
 	var html = `<form action="${baseUrl }Saldo_perusahaan/proses_request_tambah_saldo_perusahaan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                        	${id}
                        	<div class="col-12">
                        		<div class="form-group">
                                 <label>Bank Transfer</label>
                                 <select class="form-control form-control-sm" name="bank_transfer"> `;
				for( x in json ) {
					html += `<option value="${json[x].id}" ${ bank_id == json[x].id ? 'selected' : ''}>${json[x].bank_name} -> ${json[x].account_bank_name} | No Rek : ${json[x].account_bank_number}</option>`;
				}
            html +=             `</select>
                              </div>
                        	</div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nominal Yang Ingin Ditambah</label>
                                 <input type="text" name="nominal" value="${nominal}" class="form-control form-control-sm currency" placeholder="Nominal Yang Ditambahkan" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
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

function edit_riwayat_tambah_saldo( id ) {
	ajax_x(
      baseUrl + "Saldo_perusahaan/get_info_edit_riwayat_tambah_saldo", function(e) {
   		$.confirm({
		      columnClass: 'col-4',
		      title: 'Edit Saldo Perusahaan',
		      theme: 'material',
		      content: formatambah_saldoperusahaan( JSON.stringify( e.list_bank ), JSON.stringify( e.value ) ),
		      closeIcon: false,
		      buttons: {
		         cancel:function () {
		             return true;
		         },
		         simpan: {
		            text: 'Request Tambah Saldo',
		            btnClass: 'btn-blue',
		            action: function () {
							var ajax = ajax_submit_r("#form_utama");
                     if ( ajax['error'] == true ) {
                        return false;
                     } else {
                        get_riwayat_tambah_saldo(20);
                        return true;
                     }
                     ajax['error'] == true ? frown_alert(ajax['error_msg']) : smile_alert(ajax['error_msg']);
		            }
		         }

		      }
		   });
      },[ { id : id }]
   );
}

function konfirmasi_pengiriman_tambah_saldo(id){
	$.confirm({
      columnClass: 'col-4',
      title: 'Konfirmasi Pengiriman Tambah Saldo',
      theme: 'material',
      content: 'Apakah anda yakin untuk melakukan konfirmasi pengiriman biaya?',
      closeIcon: false,
      buttons: {
         tidak:function () {
             return true;
         },
         ya: {
            btnClass: 'btn-blue',
            action: function () {
            	ajax_x(
				      baseUrl + "Saldo_perusahaan/konfirmasi_pengiriman_tambah_saldo", function(e) {
				      	if ( e['error'] == true ) {
                        frown_alert(e['error_msg']);
                     } else {
                     	smile_alert(e['error_msg']);
                        get_riwayat_tambah_saldo(20);
                     }
				      },[ { id : id }]
				   );
            }
         }
      }
   });
}

// var ajax = ajax_submit_r("#form_utama");
// if ( ajax['error'] == true ) {
//    return false;
// } else {
//    get_riwayat_tambah_saldo(20);
//    return true;
// }
// ajax['error'] == true ? frown_alert(ajax['error_msg']) : smile_alert(ajax['error_msg']);