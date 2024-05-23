function trans_visa_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarVisa">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_transaksi_visa()">
                        <i class="fab fa-cc-visa"></i> Tambah Transaksi Visa
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_transaksi_visa( 20)" id="searchAllDaftarVisa" name="searchAllDaftarVisa" placeholder="Nomor Invoice" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_transaksi_visa( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Nomor Invoice</th>
                              <th style="width:15%;">Nama/Nomor Identitas <br> Pembayar</th>
                              <th style="width:38%;">Info Visa</th>
                              <th style="width:12%;">Total</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_visa">
                           <tr>
                              <td colspan="6">Daftar transaksi visa tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_visa"></div>
                  </div>
               </div>
            </div>`;
}


function trans_visa_getData(){
   get_daftar_transaksi_visa(20);
}

function get_daftar_transaksi_visa(perpage){
   get_data( perpage,
             { url : 'Trans_visa/daftar_transaksi_visa',
               pagination_id: 'pagination_daftar_visa',
               bodyTable_id: 'bodyTable_daftar_visa',
               fn: 'ListDaftarVisa',
               warning_text: '<td colspan="6">Daftar transaksi visa tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarVisa').val() } } );
}

function ListDaftarVisa(JSONData){
   var json = JSON.parse(JSONData);
   var detail = '';
   for( x in json.detail ){
      detail +=  `<div class="row">
                     <div class="col-12">
                        <label class="float-left">${json.detail[x]['nama_permohonan']}</label>
                     </div>
                     <div class="col-12">
                        <table class="table table-hover">
                           <tbody>
                              <tr>
                                 <td class="text-left py-0" style="width:32%;border:none;">Nama Pelanggan</td>
                                 <td class="text-left py-0 px-0" style="width:68%;border:none;">: ${json.detail[x]['nama_pelanggan']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Nomor Identitas</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['nomor_identitas']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">TTL</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['tempat_lahir']}, ${json.detail[x]['tanggal_lahir']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Nomor Passport</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['nomor_passport']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Berlaku S/D</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['berlaku_sd']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Harga per Paket</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: Rp ${numberFormat(json.detail[x]['price'])}</td>
                              </tr>
                           </tbody>

                        </table>
                     </div>
                  </div>`;
   }

   var html = `<tr>
                  <td>${json.invoice}</td>
                  <td>${json.payer} / <br> ${json.payer_identity}</td>
                  <td>${detail}</td>
                  <td>Rp ${numberFormat(json.total)}</td>
                  <td>${json.tanggal_transaksi}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Cetak Transaksi Visa"
                        onclick="cetak_transaksi_visa('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Transaksi Visa"
                        onclick="delete_transaksi_visa('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_transaksi_visa(){
   ajax_x(
      baseUrl + "Trans_visa/get_info_transaksi_visa", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-12',
               title: 'Tambah Transaksi Visa',
               theme: 'material',
               content: formaddupdate_trans_visa( e['invoice'], JSON.stringify(e['request_type']), JSON.stringify(e['list_city']) ),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           $.alert({
                              title: 'Peringatan',
                              content: e['error_msg'],
                              type: e['error'] == true ? 'red' :'green'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_transaksi_visa(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            });
         }else{
            $.alert({
               title: 'Peringatan',
               content: e['error_msg'],
               type: e['error'] == true ? 'red' :'green'
            });
         }
      },[]
   );
}

function delete_transaksi_visa(id){
   ajax_x(
      baseUrl + "Trans_visa/delete_transaksi_visa", function(e) {
         if( e['error'] == false ){
             get_daftar_transaksi_visa(20);
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'green',
         });
      },[{id:id}]
   );
}

function cetak_transaksi_visa(id){
   ajax_x(
      baseUrl + "Trans_visa/cetak_transaksi_visa", function(e) {
         if( e['error'] == false ) {
            window.open(baseUrl + "Kwitansi/", "_blank");
         }else{
            $.alert({
               icon: 'far fa-frown' ,
               title: 'Peringatan',
               content: e['error_msg'],
               type: 'red',
            });
         }
      },[{id:id}]
   );
}

function formaddupdate_trans_visa(invoice, request_type, list_city, JSONValue){
   var id_trans_visa = '';
   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      id_trans_visa = `<input type="hidden" name="id" value="${value.id}">`;
      invoice = value.invoice;
   }
   var html = `<form action="${baseUrl }Trans_visa/proses_addupdate_visa" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row ">
                           <div class="col-6 text-left">
                              <label><span class="float-left" style="color:red">(*) Wajib diisi</span></label>
                           </div>
                           <div class="col-6 text-right">
                              <label class="float-right">INVOICE :<span style="color:red">#${invoice}</span></label>
                              ${id_trans_visa}
                              <input type="hidden" name="invoice" value="${invoice}">
                              <input type="hidden" id="jsondata_request_type" value='${request_type}' >
                              <input type="hidden" id="jsondata_list_city" value='${list_city}' >
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-12">
                              <table class="table table-hover tablebuka">
                                 <thead>
                                    <tr>
                                       <th style="width:30%;">Info Pelanggan</th>
                                       <th style="width:45%;">Info Permohonan Visa</th>
                                       <th style="width:15%;">Biaya</th>
                                       <th style="width:10%;">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody id="bodyTable_daftar_transaksi_visa">`;
                        if ( JSONValue != undefined ) {
                           var values = JSON.parse(JSONValue);
                           for( x in values.list_transaksi_visa ) {
                              html += rowVisa( request_type, list_city, JSON.stringify(values.list_transaksi_visa[x]) );
                           }
                        } else {
                           html += rowVisa(request_type, list_city);
                        }
                        html += `</tbody>
                                 <tfoot>
                                    <tr>
                                       <td colspan="4">
                                          <div class="row" style="background-color: beige;">
                                             <div class="col-12 py-3 text-right">
                                                <button type="button" class="btn btn-default" title="Tambah Row Transaksi Visa" onclick="add_row_visa(this)">
                                                   <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row Transaksi Visa
                                                </button>
                                             </div>
                                          </div>
                                       </td>
                                    </tr>
                                 </tfoot>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function add_row_visa(){
   $('#bodyTable_daftar_transaksi_visa').append( rowVisa( $('#jsondata_request_type').val(), $('#jsondata_list_city').val()) );
}

function text_helper(text){
   return `<small class="form-text text-muted">${text}</small>`;
}

function delete_row_visa(e){
   var hargaPaket = $('.harga_paket').length;
   if( hargaPaket > 1 ){
      $(e).parent().parent().remove();
   }else{
      $.alert({
         icon: 'far fa-frown',
         title: 'Peringatan',
         content: 'Anda wajib menyisakan minimal 1 row transaksi visa',
         type: 'red',
      });
   }
}

function rowVisa(JSONRequestType, JSONCityList, JSONValue){
   var RequestTypes = JSON.parse(JSONRequestType);
   var CityList = JSON.parse(JSONCityList);

   var nama_pelanggan = '';
   var nomor_identitas = '';
   var gender = '';
   var tempat_lahir = '';
   var tanggal_lahir = '';
   var kewarganegaraan = '';
   var jenis_permohonan = '';
   var jenis_permohonan = '';
   var tanggal_permohonan = '';
   var no_passport = '';
   var release_date_passport = '';
   var release_place_passport = '';
   var passport_valid_date = '';
   var profession_idn = '';
   var profession_ln = '';
   var profession_address = '';
   var pos_code = '';
   var city = '';
   var country = '';
   var telephone = '';
   var harga_paket = '';
   var pembayar = '';

   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      if( value.pembayaran == 1 ){
         pembayar = 'checked';
      }
      nama_pelanggan = value.nama_pelanggan;
      nomor_identitas = value.nomor_identitas;
      gender = value.gender;
      tempat_lahir = value.tempat_lahir;
      tanggal_lahir = value.tanggal_lahir;
      kewarganegaraan = value.kewarganegaraan;
      jenis_permohonan = value.jenis_permohonan;
      tanggal_permohonan = value.tanggal_permohonan;
      no_passport = value.no_passport;
      release_date_passport = value.release_date_passport;
      release_place_passport = value.release_place_passport;
      passport_valid_date = value.passport_valid_date;
      profession_idn = value.profession_idn;
      profession_ln = value.profession_ln;
      profession_address = value.profession_address;
      pos_code = value.pos_code;
      city = value.city;
      country = value.country;
      telephone = value.telephone;
      harga_paket = value.harga_paket;
   }

   var pembayaran_hidden = new Array();
   $('.pembayar_hidden').each(function(index){
      if( $(this).val() != 0 ){
         pembayaran_hidden.push($(this).val());
      }
   });
   var numberPayer = 1;
   var condition = true;
   while ( condition ) {
      if( pembayaran_hidden.includes(numberPayer.toString()) ){
         numberPayer++;
      }else{
         condition = false;
      }
   }

   var html = `<tr>
                  <td>
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="nama[]" value="${nama_pelanggan}" placeholder="Nama Pelanggan">
                              ${text_helper('Nama Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="nomor_identitas[]" value="${nomor_identitas}" placeholder="Nomor Identitas Pelanggan">
                              ${text_helper('Nomor Identitas Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <select class="form-control form-control-sm akun_debet" name="gender[]">
                                 <option value="laki-laki" ${ gender == 'laki-laki' ? 'selected' : '' }>Laki-laki</option>
                                 <option value="perempuan" ${ gender == 'perempuan' ? 'selected' : '' }>Perempuan</option>
                              </select>
                              ${text_helper('Jenis Kelamin Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="tempat_lahir[]" value="${tempat_lahir}" placeholder="Tempat Lahir Pelanggan">
                              ${text_helper('Tempat Lahir Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="tanggal_lahir[]" value="${tanggal_lahir}" placeholder="Tanggal Lahir Pelanggan">
                              ${text_helper('Tanggal Lahir Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="kewarganegaraan[]" value="${kewarganegaraan}" placeholder="Kewarganegaraan">
                              ${text_helper('Kewarganegaraan Pelanggan.  <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
                  </td>
                  <td>
                     <div class="row">
                        <div class="col-8">
                           <div class="form-group text-left mb-2">
                              <select class="form-control form-control-sm akun_debet" name="jenis_permohonan[]">`;
                     for( y in RequestTypes ) {
                        html += `<option value="${y}" ${jenis_permohonan == y ? 'selected' : ''}>${RequestTypes[y]}</option>`;
                     }
                     html += `</select>
                              ${text_helper('Jenis Permohonan Visa. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-4">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="tanggal_permohonan[]" value="${tanggal_permohonan}" placeholder="Tanggal Permohonan Visa">
                              ${text_helper('Tanggal Permohonan Visa. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="no_passport[]" value="${no_passport}" placeholder="Nomor Passport Pelanggan">
                              ${text_helper('Nomor Passport Pelanggan.  <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="release_date_passport[]" value="${release_date_passport}" placeholder="Tanggal Dikeluarkan Passport Pelanggan">
                              ${text_helper('Tanggal Dikeluarkan Passport Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="release_place_passport[]" value="${release_place_passport}" placeholder="Tempat Dikeluarkan Passport Pelanggan">
                              ${text_helper('Tempat Dikeluarkan Passport Pelanggan. <span class="float-right" style="color:red">(*)</span>s')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="passport_valid_date[]" value="${passport_valid_date}" placeholder="Tanggal Berlaku Passport Pelanggan">
                              ${text_helper('Tanggal Berlaku Passport Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="profession_idn[]" value="${profession_idn}" placeholder="Pekerjaan Pelanggan Diindonesia">
                              ${text_helper('Pekerjaan Pelanggan Diindonesia. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="profession_ln[]" value="${profession_ln}" placeholder="Pekerjaan Pelanggan Diluar Negeri.">
                              ${text_helper('Pekerjaan Pelanggan Diluar Negeri. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <textarea class="form-control" name="profession_address[]" rows="3" style="resize:none;" placeholder="Alamat Pekerjaan.">${profession_address}</textarea>
                              ${text_helper('Alamat Pekerjaan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="pos_code[]" value="${pos_code}" placeholder="Kode Pos Pelanggan">
                              ${text_helper('Kode Pos Alamat Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <select class="form-control form-control-sm akun_debet" name="city[]">`;
                     for( x in  CityList ) {
                        html += `<option value="${x}" ${city == x ? 'selected' : ''}>${CityList[x]}</option>`;
                     }
                     html += `</select>
                              ${text_helper('Kota Alamat Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="country[]" value="${country}" placeholder="Negara Asal Pelanggan">
                              ${text_helper('Negara Asal Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="telephone[]" value="${telephone}" placeholder="Nomor Telephone Pelanggan">
                              ${text_helper('Nomor Telephone Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
                  </td>
                  <td>
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm currency harga_paket" name="harga_paket[]" value="${harga_paket}" placeholder="Harga Per Paket">
                              ${text_helper('Harga Per Paket. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
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
                     </script>
                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Delete Row Transaksi Visa" onclick="delete_row_visa(this)" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                     <div class="form-check mt-3">
                        <label class="form-check-label">
                           <input type="radio" style="margin-top: 1px;" class="form-check-input" name="pembayar" value="${numberPayer}" ${pembayar}>
                           <input type="hidden" class="pembayar_hidden" name="pembayar_hidden[]" value="${numberPayer}">
                           Pembayar
                        </label>
                    </div>
                  </td>
               </tr>`;
   return html;
}
