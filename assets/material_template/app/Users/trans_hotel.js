function trans_hotel_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarHotel">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_transaksi_hotel()">
                        <i class="fas fa-hotel"></i> Tambah Transaksi Hotel
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_transaksi_hotel( 20)" id="searchAllDaftarHotel" name="searchAllDaftarHotel" placeholder="Nomor Invoice" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_transaksi_hotel( 20 )">
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
                              <th style="width:38%;">Info Transaksi Hotel</th>
                              <th style="width:12%;">Total</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_hotel">
                           <tr>
                              <td colspan="6">Daftar transaksi hotel tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_hotel"></div>
                  </div>
               </div>
            </div>`;
}

function trans_hotel_getData(){
   get_daftar_transaksi_hotel(20);
}

function get_daftar_transaksi_hotel(perpage){
   get_data( perpage,
             { url : 'Trans_hotel/daftar_transaksi_hotel',
               pagination_id: 'pagination_daftar_hotel',
               bodyTable_id: 'bodyTable_daftar_hotel',
               fn: 'ListDaftarTransaksiHotel',
               warning_text: '<td colspan="6">Daftar transaksi hotel tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarHotel').val() } } );
}

function ListDaftarTransaksiHotel(JSONData){
   var json = JSON.parse(JSONData);
   var detail = '';
   for( x in json.detail ) {
      detail +=  `<div class="row">
                     <div class="col-12">
                        <label class="float-left">Hotel : ${json.detail[x]['nama_hotel']}</label>
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
                                 <td class="text-left py-0" style="border:none;">Check In</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['check_in']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Check Out</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['check_out']}</td>
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
                     <button type="button" class="btn btn-default btn-action" title="Cetak Transaksi Hotel"
                        onclick="cetak_transaksi_hotel('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Transaksi Hotel"
                        onclick="delete_transaksi_hotel('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function delete_transaksi_hotel(id){
   ajax_x(
      baseUrl + "Trans_hotel/delete_hotel", function(e) {
         if( e['error'] == false ){
             get_daftar_transaksi_hotel(20);
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

function add_transaksi_hotel(){
   ajax_x(
      baseUrl + "Trans_hotel/get_info_transaksi_hotel", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-12',
               title: 'Tambah Transaksi Hotel',
               theme: 'material',
               content: formaddupdate_trans_hotel( e['invoice'], JSON.stringify(e['list_hotel']), JSON.stringify(e['list_city']) ),
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
                              get_daftar_transaksi_hotel(20);
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

function formaddupdate_trans_hotel(invoice, JSONHotel, JSONCity, JSONValue){
   var id_trans_hotel = '';
   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      id_trans_hotel = `<input type="hidden" name="id" value="${value.id}">`;
      invoice = value.invoice;
   }
   var html = `<form action="${baseUrl }Trans_hotel/proses_addupdate_hotel" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row ">
                           <div class="col-6 text-left">
                              <label><span class="float-left" style="color:red">(*) Wajib diisi</span></label>
                           </div>
                           <div class="col-6 text-right">
                              <label class="float-right">INVOICE :<span style="color:red">#${invoice}</span></label>
                              ${id_trans_hotel}
                              <input type="hidden" name="invoice" value="${invoice}">
                              <input type="hidden" id="jsondata_list_hotel" value='${JSONHotel}' >
                              <input type="hidden" id="jsondata_list_city" value='${JSONCity}' >
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-12">
                              <table class="table table-hover tablebuka">
                                 <thead>
                                    <tr>
                                       <th style="width:35%;">Info Pelanggan</th>
                                       <th style="width:40%;">Info Hotel</th>
                                       <th style="width:15%;">Biaya</th>
                                       <th style="width:10%;">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody id="bodyTable_daftar_transaksi_hotel">
                                    ${rowHotel(JSONHotel, JSONCity)}
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                       <td colspan="4">
                                          <div class="row" style="background-color: beige;">
                                             <div class="col-12 py-3 text-right">
                                                <button type="button" class="btn btn-default" title="Tambah Row Transaksi Hotel" onclick="add_row_hotel(this)">
                                                   <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row Transaksi Hotel
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

function add_row_hotel(){
   $('#bodyTable_daftar_transaksi_hotel').append( rowHotel( $('#jsondata_list_hotel').val(), $('#jsondata_list_city').val()) );
}

function rowHotel( JSONHotel, JSONCity, JSONValue){
   var HotelList = JSON.parse(JSONHotel);
   var CityList = JSON.parse(JSONCity);

   var hotel = '';
   var city = '';
   var nama_pelanggan = '';
   var nomor_identitas = '';
   var tempat_lahir = '';
   var tanggal_lahir = '';
   var check_in_date = '';
   var check_out_date = '';
   var price = '';
   var pembayar = '';

   if(JSONValue != undefined){
      var value = JSON.parse(JSONValue);
      if( value.pembayaran == 1 ){
         pembayar = 'checked';
      }
      hotel = value.hotel;
      city = value.city;
      nama_pelanggan = value.nama;
      nomor_identitas = value.nomor_identitas;
      tempat_lahir = value.tempat_lahir;
      tanggal_lahir = value.tanggal_lahir;
      check_in_date = value.check_in_date;
      check_out_date = value.check_out_date;
      price = 'Rp ' + numberFormat(value.price);
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
                              <select class="form-control form-control-sm" name="city[]">`;
                     for( y in CityList ) {
                        html += `<option value="${y}" ${city == y ? 'selected' : ''}>${CityList[y]}</option>`;
                     }
                     html += `</select>
                              ${text_helper('Nama Kota Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
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
                              <select class="form-control form-control-sm" name="hotel[]">`;
                     for( x in HotelList ) {
                        html += `<option value="${x}" ${hotel == x ? 'selected' : ''}>${HotelList[x]}</option>`;
                     }
                     html += `</select>
                              ${text_helper('Hotel yang dipilih. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="check_in_date[]" value="${check_in_date}" placeholder="Tanggal Check in Hotel">
                              ${text_helper('Tanggal Check in Hotel. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="date" class="form-control form-control-sm" name="check_out_date[]" value="${check_out_date}" placeholder="Tanggal Check Out Hotel">
                              ${text_helper('Tanggal Check out Hotel. <span class="float-right" style="color:red">(*)</span>')}
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
                     <button type="button" class="btn btn-default btn-action" title="Delete Row Transaksi Hotel" onclick="delete_row_hotel(this)" style="margin:.15rem .1rem  !important">
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

function delete_row_hotel(e){
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

function cetak_transaksi_hotel(id){
   ajax_x(
      baseUrl + "Trans_hotel/cetak_kwitansi_hotel", function(e) {
         if( e['error'] == false ){
            window.open(baseUrl + "Kwitansi/", "_blank");
         }else{
            $.alert({
               title: 'Peringatan',
               content: e['error_msg'],
               type: e['error'] == true ? 'red' :'green'
            });
         }
      },[{id:id}]
   );
}
