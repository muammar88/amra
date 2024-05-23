function hitTotalRefund(e, id){
   var jsonData = JSON.parse($('#jsondata').val());
   var total_refund = 0;
   if( $('.refund').length > 0 ){
      $('.refund').each(function(index){
         if( hide_currency($(this).val()) != 0 ){
            total_refund = total_refund + hide_currency($(this).val());
         }
      });
   }
   var total_fee = 0;
   if( $('.fee').length > 0 ){
      $('.fee').each(function(index){
         if( hide_currency($(this).val()) != 0 ){
            total_fee = total_fee + hide_currency($(this).val());
         }
      });
   }
   if( (total_refund + total_fee) > jsonData.total_pembayaran ){
      var thisVal = hide_currency($(e).val());
      var leng = thisVal.toString().length;
      $(e).val(thisVal.toString().substring((leng-1),-2));
      frown_alert('Total refund ditambah dengan total fee tidak boleh lebih besar dari total pembayaran');
   }else{
      var refund = hide_currency($('#refund_'+ id).val());
      var fee = hide_currency($('#fee_'+ id).val());
      var total_per_code_booking = 0;
      for ( x in jsonData.list_detail ){
         if( jsonData.list_detail[x]['id'] == id ){
            total_per_code_booking = total_per_code_booking + (jsonData.list_detail[x]['costumer_price'] * jsonData.list_detail[x]['pax']);
         }
      }
      if( (refund + fee) > total_per_code_booking ){
         var thisVal = hide_currency($(e).val());
         var leng = thisVal.toString().length;
         $(e).val(thisVal.toString().substring((leng-1),-2));
         frown_alert('Total refund ditambah dengan total fee tidak boleh lebih besar dari total harga tiket');
      } else {
         $('#direfund').val('Rp ' + numberFormat(total_refund));
         $('#fee').val('Rp ' + numberFormat(total_fee));
      }
   }
}

function formRefundTiket(id, JSONData, invoice){
   var json = JSON.parse(JSONData)
   var html   = `<form action="${baseUrl }Trans_tiket/refund_tiketing_prosess" id="form_utama" class="formName ">
                  <input type="hidden" name="tiket_transaction_id" id="tiket_transaction_id" value="${id}">
                  <input type="hidden" id="sudah_dibayar" value="${json.total_pembayaran}">
                  <input type="hidden" id="jsondata" value='${JSONData}' >
                  <div class="row px-0 mx-0">
                     <div class="col-6 pt-2"><label>Info Tiket</label></div>
                     <div class="col-6 text-right"></div>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:5%;">Pax</th>
                                 <th style="width:35%;">Info Tiket</th>
                                 <th style="width:35%;">Info Tiket</th>
                                 <th style="width:25%;">Refund</th>
                              </tr>
                           </thead>
                           <tbody>`;
                  if( json.list_detail.length > 0 ) {
                     for( x in json.list_detail ) {
                        html += `<tr>
                                    <td>
                                       ${json.list_detail[x]['pax']}
                                       <input type="hidden" value="${json.list_detail[x]['id']}" name="id[${json.list_detail[x]['id']}]">
                                       <input type="hidden" id="pax${json.list_detail[x]['id']}" value="${json.list_detail[x]['pax']}" name="pax[${json.list_detail[x]['id']}]">
                                       <input type="hidden" id="costumerprice${json.list_detail[x]['id']}" value="${json.list_detail[x]['costumer_price']}"  name="costumerprice[${json.list_detail[x]['id']}]">
                                    </td>
                                    <td>
                                       <ul class="list pl-3">
                                          <li>Code Booking : ${json.list_detail[x]['code_booking']}</li>
                                          <li>Maskapai : ${json.list_detail[x]['airlines_name']}</li>
                                          <li>Tanggal Berangkat : ${json.list_detail[x]['departure_date']}</li>
                                       </ul>
                                    </td>
                                    <td>
                                       <ul class="list pl-3">
                                          <li>Harga Travel : Rp ${numberFormat(json.list_detail[x]['travel_price'])}</li>
                                          <li>Harga Costumer : Rp ${numberFormat(json.list_detail[x]['costumer_price'])}</li>
                                          <li>Total Harga Tiket : Rp ${numberFormat(json.list_detail[x]['pax'] * json.list_detail[x]['costumer_price'])}</li>
                                          <li>Total Fee Tiket : Rp ${numberFormat(json.list_detail[x]['pax'] * (json.list_detail[x]['costumer_price'] - json.list_detail[x]['travel_price']))}</li>
                                       </ul>
                                    </td>
                                    <td>
                                       <input type="text" name="refund[${json.list_detail[x]['id']}]" id="refund_${json.list_detail[x]['id']}" class="form-control form-control-sm currency refund mb-2" placeholder="Refund" onkeyup="hitTotalRefund(this, ${json.list_detail[x]['id']})"/>
                                       <input type="text" name="fee[${json.list_detail[x]['id']}]" id="fee_${json.list_detail[x]['id']}" class="form-control form-control-sm currency fee" placeholder="Fee" onkeyup="hitTotalRefund(this, ${json.list_detail[x]['id']})" />
                                    </td>
                                 </tr>`;
                     }
                  } else {
                     html += `<td colspan="4">
                                 <span> Data detail transaksi tiket tidak ditemukan </span>
                              </td>`;
                  }
                  html += `</tbody>
                        </table>
                     </div>
                  </div>
                  <div class="row px-0 mx-0">
                     <div class="col-12 text-right" >
                        <label class="p-1 px-2" style="color: #ff6767;width: 250px;border: 1px solid #f5adad;border-radius: 4px;">NO INVOICE : #${invoice}</label>
                        <input type="hidden" name="no_invoice" value="${invoice}">
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nama Pelanggan</label>
                                 <input type="text" name="nama_pelanggan" class="form-control form-control-sm" placeholder="Nama Pelanggan" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Total Direfund</label>
                                 <input type="text" id="direfund" class="form-control form-control-sm currency" placeholder="Direfund" value="Rp 0" readonly/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Total Fee</label>
                                 <input type="text" id="fee" class="form-control form-control-sm currency" placeholder="Fee" value="Rp 0" readonly/>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nomor Identitas Pelanggan</label>
                                 <input type="text" name="nomor_identitas" class="form-control form-control-sm" placeholder="Nomor Identitas Pelanggan" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sudah dibayar</label>
                                 <input type="text" name="dibayar" id="dibayar" class="form-control form-control-sm" placeholder="Dibayar" value="Rp ${numberFormat(json.total_pembayaran)}" readonly/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sisa Pembayaran</label>
                                 <input type="text" name="sisa_pembayaran" id="sisa_pembayaran" class="form-control form-control-sm" placeholder="Sisa Pembayaran" value="Rp ${numberFormat(json.sisa)}" readonly/>
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

// refund tiket
function refundTiket(id){
   ajax_x(
      baseUrl + "Trans_tiket/get_info_refund_tiket", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-8',
               title: 'Form Refund Tiket',
               theme: 'material',
               content: formRefundTiket(id, JSON.stringify(e['data']), e['invoice']),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  refund: {
                     text: 'Refund',
                     btnClass: 'btn-blue',
                     action: function () {
                        var error = 0;
                        var error_msg = '';
                        var jsonData = JSON.parse($('#jsondata').val());
                        var total_refund = 0;
                        if( $('.refund').length > 0 ) {
                           $('.refund').each(function(index) {
                              if( hide_currency($(this).val()) != 0 ){
                                 total_refund = total_refund + hide_currency($(this).val());
                              }
                           });
                        }
                        var total_fee = 0;
                        if( $('.fee').length > 0 ) {
                           $('.fee').each(function(index) {
                              if( hide_currency($(this).val()) != 0 ){
                                 total_fee = total_fee + hide_currency($(this).val());
                              }
                           });
                        }
                        if( ( total_refund + total_fee ) > jsonData.total_pembayaran ) {
                           error = 1;
                           error_msg = 'Total refund ditambah dengan total fee tidak boleh lebih besar dari total pembayaran';
                        } else {
                           for ( x in jsonData.list_detail ) {
                              var refund = hide_currency($( '#refund_' + jsonData.list_detail[x]['id']).val() );
                              var fee = hide_currency($( '#fee_'+ jsonData.list_detail[x]['id']).val() );
                              var total_per_code_booking = jsonData.list_detail[x]['costumer_price'] * jsonData.list_detail[x]['pax'];
                              // filter
                              if ( ( refund + fee ) > total_per_code_booking ) {
                                 error = 1;
                                 error_msg = 'Total refund ditambah dengan total fee tidak boleh lebih besar dari total harga tiket';
                              }
                           }
                        }
                        if( error == 0 ) {
                           ajax_submit_t1("#form_utama", function(e) {
                              $.alert({
                                 icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
                                 title: 'Peringatan',
                                 content: e['error_msg'],
                                 type: e['error'] == true ? 'red' : 'green',
                              });
                              if ( e['error'] == true ) {
                                 return false;
                              } else {
                                 get_daftar_tiket_transaction(20);
                                 window.open(baseUrl + "Kwitansi/", "_blank");
                              }
                           });
                        } else {
                           frown_alert(error_msg);
                           return false;
                        }
                     }
                  }
               }
            });
         }else{
            $.alert({
               icon: 'far fa-frown',
               title: 'Peringatan',
               content: e['error_msg'],
               type: 'red'
            });
         }
      },[{id:id}]
   );
}


function hitungReschedule(id){
   var harga_costumer = hide_currency($('#hargacostumer'+id).val());
   var sudah_dibayar = hide_currency($('#dibayar').val());
   var pax = $('#pax'+id).val();
   var subtotal = pax * harga_costumer;
   $('#subtotal'+id).html('Rp ' + numberFormat(subtotal));
   $('#subtotalhidden'+id).val(subtotal);
   var total = 0;
   if( $('.subtotal_hidden').length > 0 ){
      $('.subtotal_hidden').each(function(index){
         if( $(this).val() != 0 ){
            console.log("This");
            console.log($(this).val());
            console.log("This");
            total = total + hide_currency($(this).val());
         }
      });
   }
   $('#total').text('Rp ' + numberFormat(total) );
   $('#sisa').val('Rp ' + numberFormat(total - sudah_dibayar) );
}

function formReschedule(JSONData, id){
   random_number = Math.floor(Math.random() * 10000000);
   var json = JSON.parse(JSONData);
   var total = 0;
   var html = `<form action="${baseUrl }Trans_tiket/reschedule_tiketing_prosess" id="form_utama" class="formName ">
                  <input type="hidden" name="id" value=${id}>
                  <div class="row px-0 mx-0">
                     <div class="col-6 pt-2"><label>Info Tiket</label></div>
                     <div class="col-6 text-right"></div>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:5%;">Pax</th>
                                 <th style="width:20%;">Maskapai</th>
                                 <th style="width:15%;">Kode Booking</th>
                                 <th style="width:15%;">Tgl Berangkat</th>
                                 <th style="width:15%;">H. Travel</th>
                                 <th style="width:15%;">H. Kostumer</th>
                                 <th style="width:15%;">Total</th>
                              </tr>
                           </thead>
                           <tbody>`;
                  for( x in json.riwayat_pembayaran ) {
                     html += `<tr>
                                 <td>
                                    ${json.riwayat_pembayaran[x]['pax']}
                                    <input type="hidden" id="pax${json.riwayat_pembayaran[x]['id']}" value="${json.riwayat_pembayaran[x]['pax']}">
                                    <input type="hidden" name="tiket_transaction_detail_id[${json.riwayat_pembayaran[x]['id']}]" value="${json.riwayat_pembayaran[x]['id']}" >
                                 </td>
                                 <td>${json.riwayat_pembayaran[x]['airlines_name']}</td>
                                 <td>
                                    <input type="text" class="form-control form-control-sm" name="code_booking[${json.riwayat_pembayaran[x]['id']}]" value="${json.riwayat_pembayaran[x]['code_booking']}"/>
                                 </td>
                                 <td>
                                    <input type="date" class="form-control form-control-sm" name="departure_date[${json.riwayat_pembayaran[x]['id']}]" value="${json.riwayat_pembayaran[x]['departure_date']}"/>
                                 </td>
                                 <td>
                                    <input type="text" class="form-control form-control-sm currency harga_travel" id="hargatravel${json.riwayat_pembayaran[x]['id']}" placeholder="Harga Travel" name="harga_travel[${json.riwayat_pembayaran[x]['id']}]" value="Rp ${numberFormat(json.riwayat_pembayaran[x]['travel_price'])}" />
                                 </td>
                                 <td>
                                    <input type="text" class="form-control form-control-sm currency harga_costumer" id="hargacostumer${json.riwayat_pembayaran[x]['id']}" placeholder="Harga Kostumer" name="harga_costumer[${json.riwayat_pembayaran[x]['id']}]" value="Rp ${numberFormat(json.riwayat_pembayaran[x]['costumer_price'])}" onkeyup="hitungReschedule(${json.riwayat_pembayaran[x]['id']})"/>
                                 </td>
                                 <td>
                                    <span id="subtotal${json.riwayat_pembayaran[x]['id']}">Rp ${numberFormat(json.riwayat_pembayaran[x]['subtotal'])}</span>
                                    <input type="hidden" id="subtotalhidden${json.riwayat_pembayaran[x]['id']}" class="subtotal_hidden" value="${json.riwayat_pembayaran[x]['subtotal']}">
                                 </td>
                              </tr>`;
                     total = total + json.riwayat_pembayaran[x]['subtotal'];
                  }
                  html += `</tbody>
                           <tfoot>
                              <tr>
                                 <td colspan="6" class="text-right"><b>TOTAL</b></td>
                                 <td id="total">Rp ${numberFormat(total)}</td>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
                  <div class="row px-0 mx-0">
                     <div class="col-12 text-right" >
                        <label class="p-1 px-2" style="color: #ff6767;width: 250px;border: 1px solid #f5adad;border-radius: 4px;">NO INVOICE : #${json.invoice}</label>
                        <input type="hidden" name="no_invoice" value="${json.invoice}">
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nama Pelanggan</label>
                                 <input type="text" name="nama_pelanggan" class="form-control form-control-sm" placeholder="Nama Pelanggan" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nomor Identitas Pelanggan</label>
                                 <input type="text" name="nomor_identitas" class="form-control form-control-sm" placeholder="Nomor Identitas Pelanggan" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sudah dibayar</label>
                                 <input type="text" id="dibayar" class="form-control form-control-sm" placeholder="Dibayar" value="Rp ${numberFormat(json.pembayaran.total_pembayaran)}" readonly/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sisa</label>
                                 <input type="text" class="form-control form-control-sm" value="Rp ${numberFormat(json.pembayaran.sisa)}" id="sisa" readonly />
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


function rescheduleTiket( id ){
   ajax_x(
      baseUrl + "Trans_tiket/get_info_reschedule_tiket_transaction", function(e) {
         if( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-12',
               title: 'Form Reschelude Tiket',
               theme: 'material',
               content: formReschedule(JSON.stringify(e['data']), id),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  bayar: {
                     text: 'Bayar',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           $.alert({
                              icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
                              title: 'Peringatan',
                              content: e['error_msg'],
                              type: e['error'] == true ? 'red' : 'green',
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_tiket_transaction(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            });
         }else{
            $.alert({
               icon: 'far fa-frown',
               title: 'Peringatan',
               content: e['error_msg'],
               type: 'red'
            });
         }
      },[{id:id}]
   );
}

function hitungSubAndTotal(random_num){
   var pax = $('#pax'+ random_num).val();
   var harga_travel = $('#hargatravel'+ random_num).val();
   var harga_costumer = $('#hargacostumer'+ random_num).val();
   $('#subtotal'+random_num).text('Rp ' + numberFormat(pax * hide_currency(harga_costumer)));
   $('#subtotalhidden'+random_num).val(pax * hide_currency(harga_costumer));
   hitungTotal();
}

function hitungTotal() {
   var pax = $('.pax').val();
   var hargatravel = $('.harga_travel').val();
   var hargacostumer = $('.harga_costumer').val();
   var dibayar = hide_currency($('#dibayar').val());
   var total = 0;
   if( $('.subtotal').length > 0 ){
       $('.subtotal').each(function(index){
          if( $(this).val() != 0 ){
             total = total + hide_currency($(this).val());
          }
       });
   }
   var sisa = total - dibayar;
   if( sisa < 0 ){
      var lengDibayar = dibayar.length;
      if( lengDibayar < 4 ){
         dibayar = 'Rp 0';
      }else{
         dibayar = dibayar.substring((lengDibayar-1),-2);
      }
      $('#dibayar').val(dibayar);
   }else{
      $('#total').text('Rp ' + numberFormat(total));
      $('#sisa').val('Rp ' + numberFormat(sisa));
   }
}

function add_row_tiket_transaction(){
   var listAirlines = $('#dataAirlines').val();
   var html = form_row_tiket_transaction(listAirlines);
   $('#body_tiket_transaction').append(html);
}

function form_row_tiket_transaction(JSONData){
   var airlines = JSON.parse(JSONData);
   random_number = Math.floor(Math.random() * 10000000);
   var form = `<tr>
                  <td>
                     <button type="button" class="btn btn-default btn-action"
                        title="Hapus Row Transaksi Tiket" onclick="deleteRowTransaksiTiket()">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
                  <td>
                     <input type="number" name="pax[]" class="form-control form-control-sm pax" id="pax${random_number}" onkeyup="hitungSubAndTotal(${random_number})" value="0" min="0">
                  </td>
                  <td>
                     <select class="form-control form-control-sm" name="airlines[]">`;
               for( x in airlines ) {
                  form += `<option value="${airlines[x]['id']}">${airlines[x]['name']}</option>`;
               }
         form +=    `</select>
                  </td>
                  <td>
                     <input type="text" class="form-control form-control-sm" name="kode_booking[]" placeholder="Kode Booking" />
                  </td>
                  <td>
                     <input type="date" class="form-control form-control-sm" name="departure_date[]"/>
                  </td>
                  <td>
                     <input type="text" class="form-control form-control-sm currency harga_travel" id="hargatravel${random_number}" placeholder="Harga Travel" name="harga_travel[]" value="Rp 0" />
                  </td>
                  <td>
                     <input type="text" class="form-control form-control-sm currency harga_costumer" id="hargacostumer${random_number}" placeholder="Harga Kostumer" name="harga_costumer[]" value="Rp 0" onkeyup="hitungSubAndTotal(${random_number})"/>
                  </td>
                  <td class="pt-3">
                     <span id="subtotal${random_number}">Rp 0</span>
                     <input type="hidden" class="subtotal" id="subtotalhidden${random_number}" value="Rp 0">
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
   return form;
}

function form_tiket_transaction(JSONData){
   var data = JSON.parse(JSONData);
   var form = `<form action="${baseUrl }Trans_tiket/tiketing_prosess" id="form_utama" class="formName ">
                  <input type="hidden" id="dataAirlines" value='${JSON.stringify(data.airlines)}'>
                  <div class="row px-0 mx-0">
                     <div class="col-6 pt-2" >
                        <label>Info Tiket</label>
                     </div>
                     <div class="col-6 text-right" >
                        <label class="p-1 px-2" style="color: #ff6767;width: 250px;border: 1px solid #f5adad;border-radius: 4px;">NO REGISTER : #${data.no_register}</label>
                        <input type="hidden" name="no_register" value="${data.no_register}">
                     </div>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:10%;">Aksi</th>
                                 <th style="width:8%;">Pax</th>
                                 <th style="width:20%;">Maskapai</th>
                                 <th style="width:13%;">Kode Booking</th>
                                 <th style="width:13%;">Tanggal Berangkat</th>
                                 <th style="width:13%;">Harga Travel</th>
                                 <th style="width:13%;">Harga Kostumer</th>
                                 <th style="width:10%;">Total</th>
                              </tr>
                           </thead>
                           <tbody id="body_tiket_transaction">`;
                    form +=  form_row_tiket_transaction(JSON.stringify(data.airlines));
               form +=    `</tbody>
                           <tfoot>
                              <tr>
                                 <td colspan="2">
                                    <button type="button" class="btn btn-default" title="Tambah Row" onclick="add_row_tiket_transaction()">
                                       <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row
                                    </button>
                                 </td>
                                 <td colspan="5" class="text-right pt-3"><b>TOTAL</b></td>
                                 <td class="pt-3"><span id="total">Rp 0</span></td>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
                  <div class="row px-0 mx-0">
                     <div class="col-12 text-right" >
                        <label class="p-1 px-2" style="color: #ff6767;width: 250px;border: 1px solid #f5adad;border-radius: 4px;">NO INVOICE : #${data.no_invoice}</label>
                        <input type="hidden" name="no_invoice" value="${data.no_invoice}">
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nama Pelanggan</label>
                                 <input type="text" name="nama_pelanggan" class="form-control form-control-sm" placeholder="Nama Pelanggan" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nomor Identitas Pelanggan</label>
                                 <input type="text" name="nomor_identitas" class="form-control form-control-sm" placeholder="Nomor Identitas Pelanggan" />
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label>Dibayar</label>
                                 <input type="text" name="dibayar" id="dibayar" class="form-control form-control-sm currency" placeholder="Dibayar" onkeyup="hitungTotal()" value="Rp. 0"/>
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label>Sisa</label>
                                 <input type="text" class="form-control form-control-sm" value="Rp. 0" id="sisa" readonly />
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
         return form;
}

function start_transaction_ticketing(){
   ajax_x(
      baseUrl + "Trans_tiket/get_info_tiket_transaction", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-12',
               title: 'Form Transaksi Tiket',
               theme: 'material',
               content: form_tiket_transaction(JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  bayar: {
                     text: 'Bayar',
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
                              get_daftar_tiket_transaction(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                        	}
                        });
                     }
                  }
               }
            });
         }else{
            $.alert({
               icon: 'far fa-frown',
               title: 'Peringatan',
               content: e['error_msg'],
               type: 'red'
            });
         }
      },[]
   );
}


function trans_tiket_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarTiket">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="start_transaction_ticketing()">
                        <i class="fas fa-money-bill-wave"></i> Memulai Transaksi Tiket
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_tiket_transaction( 20)" id="searchAllDaftarTransaksiTiket" name="searchAllDaftarTransaksiTiket" placeholder="Nomor Register" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_tiket_transaction( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">No Register</th>
                              <th style="width:50%;">Info Tiket </th>
                              <th style="width:35%;">Info Pembayaran</th>
                              <th style="width:5%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_all_daftar_transaksi_tiket">
                           <tr>
                              <td colspan="5">Daftar transaksi tiket tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_all_daftar_transaksi_tiket"></div>
                  </div>
               </div>
            </div>`;
}

function trans_tiket_getData(){
   get_daftar_tiket_transaction(1);
}

function get_daftar_tiket_transaction(perpage){
   get_data( perpage,
             { url : 'Trans_tiket/daftar_all_daftar_transaksi_tiket',
               pagination_id: 'pagination_all_daftar_transaksi_tiket',
               bodyTable_id: 'bodyTable_all_daftar_transaksi_tiket',
               fn: 'ListDaftarTransaksiTiket',
               warning_text: '<td colspan="5">Daftar transaksi tiket tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarTransaksiTiket').val() } } );
}

function ListDaftarTransaksiTiket(JSONData){
   var json = JSON.parse(JSONData);
   var detail_transaction =  `<table class="table mb-0">
                                 <tbody>`;
         for( x in json.detail_transaction ) {
            detail_transaction +=  `<tr>
                                       <td style="width:20%;border:none" class="text-left px-0 pb-0"><b>PAX</b></td>
                                       <td style="width:20%;border:none" class="text-left px-1 pb-0">: ${json.detail_transaction[x]['pax']}</td>
                                       <td style="width:30%;border:none" class="text-left px-0 pb-0"><b>NAMA AIRLINES</b></td>
                                       <td style="width:30%;border:none" class="text-left px-1 pb-0">: ${json.detail_transaction[x]['airlines_name']}</td>
                                    </tr>
                                    <tr>
                                       <td style="border:none" class="text-left px-0 py-0"><b>KODE BOOKING</b></td>
                                       <td style="border:none" class="text-left px-1 py-0">: <span style="color:red"><b>${json.detail_transaction[x]['code_booking']}</b></span></td>
                                       <td style="border:none" class="text-left px-0 py-0"><b>TANGGAL BERANGKAT</b></td>
                                       <td style="border:none" class="text-left px-1 py-0">: ${json.detail_transaction[x]['departure_date']}</td>
                                    </tr>
                                    <tr>
                                       <td style="border:none" class="text-left px-0 pt-0"><b>HARGA TRAVEL</b></td>
                                       <td style="border:none" class="text-left px-1 pt-0">: Rp ${numberFormat(json.detail_transaction[x]['travel_price'])}</td>
                                       <td style="border:none" class="text-left px-0 pt-0"><b>HARGA KOSTUMER</b></td>
                                       <td style="border:none" class="text-left px-1 pt-0">: Rp ${numberFormat(json.detail_transaction[x]['costumer_price'])}</td>
                                    </tr>
                                    <tr style="background-color: #ffcbcb;">
                                       <td style="border:none" class="text-right mb-1 pl-0 py-1" colspan="3"><b>SUBTOTAL</b></td>
                                       <td class="text-left mb-1 px-1 py-1" style="background-color: #f59393;border:none">: Rp ${numberFormat(json.detail_transaction[x]['total'])}</td>
                                    </tr>
                                    <tr><td style="border:none" class="py-2" colspan="4"></td></tr>`;
         }
   detail_transaction    +=      `</tbody>
                              </table>`;

   var info_pembayaran  = `<table class="table table-hover mb-1">
                              <tbody>
                                 <tr>
                                    <td style="width:50%;border:none" class="text-left px-0 py-0"><b>TOTAL TRANSAKSI TIKET</b></td>
                                    <td style="width:50%;border:none" class="text-left px-1 py-0">: Rp ${numberFormat(json.total)} </td>
                                 </tr>
                                 <tr>
                                    <td style="border:none" class="text-left px-0 py-0"><b>TOTAL PEMBAYARAN</b></td>
                                    <td style="border:none" class="text-left px-1 py-0">: Rp ${numberFormat(json.total_sudah_bayar)}</td>
                                 </tr>
                                 <tr>
                                    <td style="border:none" class="text-left px-0 py-0"><b>SISA PEMBAYARAN</b></td>
                                    <td style="border:none" class="text-left px-1 py-0">: Rp ${numberFormat(json.sisa)}</td>
                                 </tr>
                              </tbody>
                           </table>
                           <div class="row">
                              <div class="col-12 text-left">
                                 <label class="mb-0">RIWAYAT PEMBAYARAN <span style="color:red;font-style:italic;font-size:10px">(Tiga transaksi terakhir)</span></label>
                                 <ul class="list mt-0 mb-1 pl-3">`;
         if( json.riwayat_transaksi_tiket.length > 0 ){
            for( y in json.riwayat_transaksi_tiket){
               info_pembayaran +=  `<li style="border-bottom: 1px dashed #c3bdbd;" class="mb-1">${ json.riwayat_transaksi_tiket[y]['ket'] == 'refund' ? '<span style="color:red"><b>[REFUND]</b></span>' : '' } Tanggal Transaksi: ${json.riwayat_transaksi_tiket[y]['tanggal_transaksi']} | No Invoice: <b style="color:red">${json.riwayat_transaksi_tiket[y]['invoice']} </b> | Biaya: Rp ${numberFormat(json.riwayat_transaksi_tiket[y]['biaya'])} | Nama Petugas: ${json.riwayat_transaksi_tiket[y]['nama_petugas']} | Nama Pelanggan : ${json.riwayat_transaksi_tiket[y]['nama_pelanggan']} | Nomor Identitas : ${json.riwayat_transaksi_tiket[y]['nomor_identitas']} </li>`;
            }
         }else{
            info_pembayaran += `<li style="color:red;" class="mb-1 text-center">Riwayat pembayaran tiket tidak ditemukan </li>`;
         }
         info_pembayaran +=     `</ul>
                              </div>
                           </div>`;
   var html =  `<tr>
                  <td>
                     <b>${json.nomor_register}</b><br><br>${json.transaction_date}
                  </td>
                  <td>${detail_transaction}</td>
                  <td>${info_pembayaran}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Refund Transaksi Tiket" onclick="refundTiket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-undo-alt" style="font-size: 11px;"></i>
                     </button>`;
         if( json.sisa != 0 ){
            html +=    `<button type="button" class="btn btn-default btn-action" title="Bayar Transaksi Tiket"
                           onclick="bayarTiket('${json.id}')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-money-bill-alt" style="font-size: 11px;"></i>
                        </button>`;
         }
         html +=    `<button type="button" class="btn btn-default btn-action" title="Reschedule Transaksi Tiket"
                        onclick="rescheduleTiket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-calendar-times" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Detail Riwayat Pembayaran Tiket" onclick="DetailRiwayatPembayaranTiket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-tasks" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Transaksi Tiket" onclick="deleteTransaksiTiket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function deleteTransaksiTiket( id ) {
   ajax_x(
      baseUrl + "Trans_tiket/delete_transaksi_tiket", function(e) {
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'green',
         });
         get_daftar_tiket_transaction(20);
      },[{id:id}]
   );
}

function formDetailRiwayatPembayaranTiket(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<table class="table table-hover mb-1">
               <thead>
                  <tr>
                     <th style="width:20%;">Invoice</th>
                     <th style="width:30%;">Nama/Identitas Pelanggan</th>
                     <th style="width:20%;">Biaya</th>
                     <th style="width:20%;">Nama Petugas</th>
                     <th style="width:10%;">Ket</th>
                  </tr>
               </thead>
               <tbody>`;
      for( x in json ){
         html += `<tr>
                     <td><b style="color:red">${json[x]['invoice']}</b> <br> ${json[x]['tanggal_transaksi']}</td>
                     <td>${json[x]['nama_pelanggan']} <br> (${json[x]['nomor_identitas']})</td>
                     <td>Rp ${numberFormat(json[x]['biaya'])}</td>
                     <td>${json[x]['nama_petugas']}</td>
                     <td style="text-transform:uppercase">${json[x]['ket']}</td>
                  </tr>`;
      }
      html += `</tbody>
            </table>`;
   return html;
}

function DetailRiwayatPembayaranTiket(id){
   ajax_x_t2(
      baseUrl + "Trans_tiket/info_detail_riwayat_pembayaran_tiket", function(e) {
         $.confirm({
            columnClass: 'col-8',
            title: 'Detail Riwayat Pembayaran Tiket <span style="color:red">No Register : #123123123</span>',
            theme: 'material',
            content: formDetailRiwayatPembayaranTiket(JSON.stringify(e['data'])),
            closeIcon: false,
            buttons: {
               tutup:function () {
                    return true;
               },
            }
         });
      },[{id:id}]
   );
}

function form_bayar_tiket_transaction(JSONData, invoice){
   var data = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Trans_tiket/proses_pembayaran_tiket" id="form_utama" class="formName ">
                  <input type="hidden" name="id" value="${data.id}">
                  <input type="hidden" id="total_pembayaran" value="${data.total_pembayaran}">
                  <input type="hidden" id="total_harga" value="${data.total_harga}">
                  <input type="hidden" name="invoice" value="${invoice}">
                  <div class="row px-0 mx-0">
                     <div class="col-6 pt-2"><label>Riwayat Pembayaran Tiket</label></div>
                     <div class="col-6 text-right">
                        <label class="p-1 px-2" style="color: #ff6767;width: 250px;border: 1px solid #f5adad;border-radius: 4px;">NO INOVICE : #${invoice}</label>
                     </div>
                     <div class="col-12" >
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th style="width:10%;">Invoice</th>
                                 <th style="width:20%;">Tanggal Transaksi</th>
                                 <th style="width:20%;">Diterima dari</th>
                                 <th style="width:20%;">Nama Petugas</th>
                                 <th style="width:10%;">Ket</th>
                                 <th style="width:20%;">Saldo</th>
                              </tr>
                           </thead>
                           <tbody id="body_tiket_transaction">`;
            if( data.riwayat_pembayaran.length > 0 ) {
               for( x in data.riwayat_pembayaran ) {
                  html += `<tr>
                              <td>${data.riwayat_pembayaran[x]['invoice']}</td>
                              <td>${data.riwayat_pembayaran[x]['input_date']}</td>
                              <td>${data.riwayat_pembayaran[x]['costumer_name']}</td>
                              <td>${data.riwayat_pembayaran[x]['receiver']}</td>
                              <td>${data.riwayat_pembayaran[x]['ket']}</td>
                              <td>Rp ${numberFormat(data.riwayat_pembayaran[x]['biaya'])}</td>
                           </tr>`;
               }
               html     +=`<tr style="background-color: #e8e8e8;">
                              <td colspan="5" class="text-right"><b>TOTAL PEMBAYARAN</b></td>
                              <td><b>Rp ${numberFormat(data.total_pembayaran)}</b></td>
                           </tr>`;
            } else {
               html += `<tr><td colspan="7">Data riwayat pembayaran tidak ditemukan</td></tr>`;
            }
            html +=       `</tbody>
                        </table>
                     </div>
                  </div>
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nama Pelanggan</label>
                                 <input type="text" name="nama_pelanggan" class="form-control form-control-sm" placeholder="Nama Pelanggan" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nomor Identitas</label>
                                 <input type="text" name="nomor_identitas" class="form-control form-control-sm" placeholder="Nomor Identitas Pelanggan" />
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label>Dibayar</label>
                                 <input type="text" name="dibayar" id="dibayar" class="form-control form-control-sm currency" placeholder="Dibayar" onkeyup="hitungSisaPembayaranTiket()" value="Rp 0"/>
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label>Sisa</label>
                                 <input type="text" class="form-control form-control-sm" value="Rp ${numberFormat(data.sisa)}" id="sisa" readonly />
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label>Total Tiket</label>
                                 <input type="text" class="form-control form-control-sm" value="Rp ${numberFormat(data.total_harga)}" id="total" readonly />
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

function hitungSisaPembayaranTiket(){
   var total_harga = hide_currency($('#total_harga').val());
   var total_pembayaran = hide_currency($('#total_pembayaran').val());
   var dibayar = hide_currency($('#dibayar').val());
   var sisa = total_harga - (total_pembayaran + dibayar);
   if( sisa < 0 ){
      $.alert({
         icon: 'far fa-frown' ,
         title: 'Peringatan',
         content: 'Sisa tidak boleh lebih kecil dari NOL',
         type: 'red',
      });
   }
   $('#sisa').val('Rp ' +numberFormat(sisa));
}

function bayarTiket(id){
   ajax_x_t2(
      baseUrl + "Trans_tiket/info_bayar_tiket", function(e) {
         $.confirm({
            columnClass: 'col-8',
            title: 'Form Bayar Tiket',
            theme: 'material',
            content: form_bayar_tiket_transaction(JSON.stringify(e['data']), e['invoice'] ),
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               bayar: {
                  text: 'Bayar',
                  btnClass: 'btn-blue',
                  action: function () {
                     var total_harga = hide_currency($('#total_harga').val());
                     var total_pembayaran = hide_currency($('#total_pembayaran').val());
                     var dibayar = hide_currency($('#dibayar').val());
                     if( (total_harga - (total_pembayaran + dibayar )) < 0 ){
                        $.alert({
                           icon: 'far fa-frown' ,
                           title: 'Peringatan',
                           content: 'Total pembayaran tidak boleh melebihi total tiket',
                           type: 'red',
                        });
                        return  false;
                     }else{
                        ajax_submit_t1("#form_utama", function(e) {
                           $.alert({
                              title: 'Peringatan',
                              content: e['error_msg'],
                              type: e['error'] == true ? 'red' :'green'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_tiket_transaction(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            }
         });
      },[{id:id}]
   );
}
