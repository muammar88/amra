function trans_passport_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarPassport">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_transaksi_passport()">
                        <i class="fas fa-passport"></i> Tambah Transaksi Passport
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_transaksi_passport( 20)" id="searchAllDaftarPassport" name="searchAllDaftarPassport" placeholder="Nomor Invoice" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_transaksi_passport( 20 )">
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
                              <th style="width:38%;">Info Transaksi Passport</th>
                              <th style="width:12%;">Total</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_passport">
                           <tr>
                              <td colspan="6">Daftar transaksi passport tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_passport"></div>
                  </div>
               </div>
            </div>`;
}

function trans_passport_getData(){
   get_daftar_transaksi_passport(20);
}

function get_daftar_transaksi_passport(perpage){
   get_data( perpage,
             { url : 'Trans_passport/daftar_transaksi_passport',
               pagination_id: 'pagination_daftar_passport',
               bodyTable_id: 'bodyTable_daftar_passport',
               fn: 'ListDaftarPassport',
               warning_text: '<td colspan="6">Daftar transaksi passport tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarPassport').val() } } );
}

function ListDaftarPassport(JSONData){
   var json = JSON.parse(JSONData);
   var detail = '';
   for( x in json.detail ) {
      detail +=  `<div class="row">
                     <div class="col-12">
                        <table class="table table-hover">
                           <tbody>
                              <tr>
                                 <td class="text-left py-0" style="width:36%;border:none;">Nama Pelanggan</td>
                                 <td class="text-left py-0 px-0" style="width:64%;border:none;">: ${json.detail[x]['nama_pelanggan']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Nomor Identitas</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['nomor_identitas']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Nomor KK</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['kartu_keluarga_number']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">TTL</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['tempat_lahir']}, ${json.detail[x]['tanggal_lahir']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Alamat Pelanggan</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['address']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Harga per Paket</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${kurs} ${numberFormat(json.detail[x]['price'])}</td>
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
                  <td>${kurs} ${numberFormat(json.total)}</td>
                  <td>${json.tanggal_transaksi}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Cetak Transaksi Passport"
                        onclick="cetak_transaksi_passport('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Transaksi Passport"
                        onclick="delete_transaksi_passport('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_transaksi_passport(){
   ajax_x(
      baseUrl + "Trans_passport/get_info_transaksi_passport", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-12',
               title: 'Tambah Transaksi Passport',
               theme: 'material',
               content: formaddupdate_trans_passport( e['invoice'], JSON.stringify(e['list_city']) ),
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
                              get_daftar_transaksi_passport(20);
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

function formaddupdate_trans_passport(invoice, JSONCity){
   var id_trans_passport = '';
   var html = `<form action="${baseUrl }Trans_passport/proses_addupdate_passport" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row ">
                           <div class="col-6 text-left">
                              <label><span class="float-left" style="color:red">(*) Wajib diisi</span></label>
                           </div>
                           <div class="col-6 text-right">
                              <label class="float-right">INVOICE :<span style="color:red"> #${invoice}</span></label>
                              ${id_trans_passport}
                              <input type="hidden" name="invoice" value="${invoice}">
                              <input type="hidden" id="jsondata_list_city" value='${JSONCity}' >
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-12">
                              <table class="table table-hover tablebuka">
                                 <thead>
                                    <tr>
                                       <th style="width:35%;">Info Pelanggan</th>
                                       <th style="width:35%;">Info Alamat Pelanggan</th>
                                       <th style="width:20%;">Biaya</th>
                                       <th style="width:10%;">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody id="bodyTable_daftar_transaksi_passport">
                                    ${rowPassport(JSONCity)}
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                       <td colspan="4">
                                          <div class="row" style="background-color: beige;">
                                             <div class="col-12 py-3 text-right">
                                                <button type="button" class="btn btn-default" title="Tambah Row Transaksi Passport" onclick="add_row_passport(this)">
                                                   <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row Transaksi Passport
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

function rowPassport(JSONCity){

   var CityList = JSON.parse(JSONCity);

   var city = '';
   var nama_pelanggan = '';
   var nomor_identitas = '';
   var tempat_lahir = '';
   var tanggal_lahir = '';
   var nomor_kartu_keluarga = '';
   var address = '';
   var price = '';
   var pembayar = '';

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

   var html =  `<tr>
                  <td>
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="nama[]" value="${nama_pelanggan}" placeholder="Nama Pelanggan">
                              ${text_helper('Nama Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="nomor_identitas[]" value="${nomor_identitas}" placeholder="Nomor Identitas Pelanggan">
                              ${text_helper('Nomor Identitas Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="nomor_kartu_keluarga[]" value="${nomor_kartu_keluarga}" placeholder="Nomor Kartu Keluarga">
                              ${text_helper('Nomor Kartu Keluarga Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>

                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="tempat_lahir[]" value="${tempat_lahir}" placeholder="Tempat Lahir">
                              ${text_helper('Tempat Lahir Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="tanggal_lahir[]" value="${tanggal_lahir}" placeholder="Tanggal Lahir">
                              ${text_helper('Tanggal Lahir Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
                  </td>
                  <td>
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <select class="form-control form-control-sm" name="city[]">`;
                     for( y in CityList ) {
                        html += `<option value="${y}" ${city == y ? 'selected' : ''}>${CityList[y]}</option>`;
                     }
                     html += `</select>
                              ${text_helper('Nama Kota Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <textarea class="form-control" name="address[]" rows="3" style="resize:none;" placeholder="Alamat Pelanggan.">${address}</textarea>
                              ${text_helper('Alamat Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
                  </td>
                  <td>
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm currency harga_paket" name="price[]" value="${price}" placeholder="Harga per Paket">
                              ${text_helper('Harga per Paket. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Delete Row Transaksi Passport" onclick="delete_row_passport(this)" style="margin:.15rem .1rem  !important">
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
               </tr>
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

function delete_row_passport(e){
   var hargaPaket = $('.harga_paket').length;
   if( hargaPaket > 1 ){
      $(e).parent().parent().remove();
   }else{
      $.alert({
         icon: 'far fa-frown',
         title: 'Peringatan',
         content: 'Anda wajib menyisakan minimal 1 row transaksi hotel',
         type: 'red',
      });
   }
}

function add_row_passport(){
   $('#bodyTable_daftar_transaksi_passport').append( rowPassport( $('#jsondata_list_city').val() ) );
}

function delete_transaksi_passport(id){
   ajax_x(
      baseUrl + "Trans_passport/delete_passport", function(e) {
         if( e['error'] == false ){
             get_daftar_transaksi_passport(20);
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

function cetak_transaksi_passport(id){
   ajax_x(
      baseUrl + "Trans_passport/cetak_transaksi_passport", function(e) {
         if ( e['error'] == true ) {
            $.alert({
               icon: 'far fa-frown',
               title: 'Peringatan',
               content: 'Kwitansi transaksi passport gagal dicetak.',
               type: 'red',
            });
         } else {
            get_daftar_transaksi_passport(20);
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{id:id}]
   );
}
