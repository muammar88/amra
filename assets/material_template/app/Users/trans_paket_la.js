function trans_paket_la_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentTransPaketLA">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_trans_paket_la()">
                        <i class="fas fa-box"></i> Tambah Transaksi Paket LA
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_trans_paket_la(20)" id="searchNamaKlien" name="searchNamaKlien" placeholder="Nomor Registrasi" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_trans_paket_la(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:13%;">Nomor Register</th>
                              <th style="width:27%;">Info Klien</th>
                              <th style="width:30%;">Info Transaksi</th>
                              <th style="width:22%;">Info Harga</th>
                              <th style="width:8%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody class="bodyTable" id="bodyTable_trans_paket_la">
                           <tr>
                              <td colspan="5">Daftar transaksi paket la tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_trans_paket_la"></div>
                  </div>
               </div>
            </div>`;
}


function trans_paket_la_getData(){
   get_trans_paket_la(20);
}

function get_trans_paket_la(perpage){
   get_data( perpage,
             { url : 'Trans_paket_la/daftar_trans_paket_la',
               pagination_id: 'pagination_trans_paket_la',
               bodyTable_id: 'bodyTable_trans_paket_la',
               fn: 'ListDaftarTransPaketLA',
               warning_text: '<td colspan="5">Daftar transaksi paket la tidak ditemukan</td>',
               param : { search : $('#searchNamaKlien').val() } } );
}

function ListDaftarTransPaketLA(JSONData){
   var json = JSON.parse(JSONData);
   var tipe_paket = 'tipe paket tidak ditemukan.';
   var fasilitas = `<ul class="list">`;
   for( x in json.fasilitas ){
      fasilitas += `<li>${json.fasilitas[x]['name']} (Pax: ${json.fasilitas[x]['pax']} - Harga : Rp ${numberFormat(json.fasilitas[x]['harga'])})</li>`;
   }
   fasilitas += `</ul>`;

   var html =  `<tr>
                  <td>${json.register_number}</td>
                  <td>
                     <ul class="pl-2 list">
                        <li>Nama Klien : ${json.client_name}</li>
                        <li>Nomor HP Klien : ${json.client_hp_number}</li>
                        <li>Alamat Klien : ${json.client_address}</li>
                     </ul>
                  </td>
                  <td>
                     <ul class="pl-2 list">
                        <li>Tipe Paket : ${json.tipe_paket}</li>
                        <li>Tanggal Keberangkatan : ${json.departure_date}</li>
                        <li>Tanggal Kepulangan : ${json.arrival_date}</li>
                        <li>Deskripsi : ${json.description}</li>
                        <li>Fasilitas : ${fasilitas}</li>
                     </ul>
                  </td>
                  <td>
                     <ul class="pl-2 list">
                        <li>Total Harga : Rp ${numberFormat(json.total_price)}</li>
                        <li>Diskon : Rp ${numberFormat(json.discount)}</li>
                        <li>Sudah Dibayar : Rp ${numberFormat(json.sudah_dibayar)}</li>
                        <li>Sisa : Rp ${numberFormat(json.sisa)}</li>
                     </ul>
                  </td>
                  <td>`;

         if( json.status == 'close' ) {
            html += `<span class="float-center">Paket La ini sudah ditutup.</span>`;
         }else{
            html += `<button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Awal Paket LA"
                        onclick="cetak_kwitansi_awal('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Refund Paket LA"
                        onclick="refund_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-undo-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Pembayaran Paket LA"
                        onclick="pembayaran_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="far fa-money-bill-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Edit Paket LA"
                        onclick="edit_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="K & T Paket LA"
                        onclick="k_t_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-list-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Paket LA"
                        onclick="delete_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>`;
         }
         
         html += `</td>
               </tr>`;
   return html;
}

function cetak_kwitansi_awal(id){
   ajax_x(
      baseUrl + "Trans_paket_la/cetak_kwitansi_pertama_paket_la", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if ( e['error'] == true ) {
            return false;
         } else {
            get_trans_paket_la(20);
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{id:id}]
   );
}

function refund_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/info_refund_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Refund Transaksi Paket LA',
               theme: 'material',
               content: form_refund_trans_paket_la(id, e['invoice'], e['wasPaid']),
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
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_trans_paket_la(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function form_refund_trans_paket_la(id, invoice, wasPaid){
   var html = `<form action="${baseUrl }Trans_paket_la/proses_refund_trans_paket_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <label class="float-right">Nomor Invoice : <b style="color:red;">#${invoice}</b></label>
                        <input type="hidden" name="invoice" value="${invoice}">
                        <input type="hidden" name="id" value="${id}">
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Sudah Dibayar</label>
                                 <input type="text" id="sudah_dibayar" value="Rp ${numberFormat(wasPaid)}" class="form-control form-control-sm" placeholder="Sudah Dibayar" disabled/>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Refund</label>
                                 <input type="text" name="refund" id="refund" class="form-control form-control-sm currency" placeholder="Refund" onKeyup="countRefund()" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nama Penyetor</label>
                                 <input type="text" name="nama_penyetor" id="nama_penyetor" class="form-control form-control-sm" placeholder="Nama Penyetor"/>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nomor HP Penyetor</label>
                                 <input type="text" name="no_hp_penyetor" id="no_hp_penyetor" class="form-control form-control-sm" placeholder="Nomor HP Penyetor"/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alamat Penyetor</label>
                                 <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3"
                                    style="resize: none;" placeholder="Alamat Penyetor" required></textarea>
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


function edit_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/info_edit_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-10',
               title: 'Edit Transaksi Paket LA',
               theme: 'material',
               content: formaddupdate_trans_paket_la('', JSON.stringify(e['tipe_paket_la']), JSON.stringify(e['fasilitas']), JSON.stringify(e['value'])),
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
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              window.open(baseUrl + "Kwitansi/", "_blank");
                              get_trans_paket_la(20);
                           }
                        });
                     }
                  }

               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function add_trans_paket_la(){
   ajax_x(
      baseUrl + "Trans_paket_la/get_info_trans_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-10',
               title: 'Tambah Transaksi Paket LA',
               theme: 'material',
               content: formaddupdate_trans_paket_la(e['nomor_register'], JSON.stringify(e['paket_type']), JSON.stringify(e['fasilitas'])),
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
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              window.open(baseUrl + "Kwitansi/", "_blank");
                              get_trans_paket_la(20);
                           }
                        });
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

function formaddupdate_trans_paket_la(nomor_register, JSONPaketType, JSONFasilitas, JSONValue){
   var paket_type_json = JSON.parse(JSONPaketType);
   var paket_la_id = '';
   var nama_klien = '';
   var nomor_hp_klien = '';
   var diskon = '';
   var jamaah = '';
   var paket_type_selected = '';
   var tanggal_keberangkatan = '';
   var tanggal_kepulangan = '';
   var alamat_klien = '';
   var discription = '';
   var fasilitas_selected = '';
   var no_register = '';
   var total_price = '';
   var no_register = `<input type="hidden" name="no_register" value='${nomor_register}' >`;

   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      paket_la_id = `<input type="hidden" name="paket_la_id" value="${value.id}">`;
      no_register = `<input type="hidden" name="no_register" value='${value.register_number}' >`;
      nomor_register = value.register_number;
      nama_klien = value.client_name;
      nomor_hp_klien = value.client_hp_number;
      diskon = value.discount;
      jamaah = value.jamaah;
      paket_type_selected = value.tipe_paket_la;
      tanggal_keberangkatan = value.departure_date;
      tanggal_kepulangan = value.arrival_date;
      discription = value.description;
      alamat_klien = value.client_address;
      discription = value.description;
      total_price = 'Rp ' + numberFormat(value.total_price);
      for( x in value.facilities ) {
         fasilitas_selected += row_fasilitas(JSONFasilitas, value.facilities[x]['id'], value.facilities[x]['pax'], 'Rp '+numberFormat(value.facilities[x]['harga']) );
      }
   }
   var html = `<form action="${baseUrl }Trans_paket_la/proses_addupdate_trans_paket_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        ${paket_la_id}
                        ${no_register}
                        <input type="hidden" id="jsonfasilitas" value='${JSONFasilitas}' >
                        <input type="hidden" id="jsonpakettype" value='${JSONPaketType}' >
                        <label class="float-right">Nomor Register : <b style="color:red;">#${nomor_register}</b></label>
                     </div>
                     <div class="col-7">
                        <div class="row">
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Nama Klien</label>
                                 <input type="text" name="nama_klien" value="${nama_klien}" class="form-control form-control-sm" placeholder="Nama Klien" />
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nomor HP Klien</label>
                                 <input type="text" name="nomor_hp" value="${nomor_hp_klien}" class="form-control form-control-sm" placeholder="Nomor HP Klien" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Jenis Paket</label>
                                 <select class="form-control form-control-sm" name="jenis_paket" id="jenis_paket" onChange="getListFasilitasTipePaketLA()">
                                    <option value="0">Pilih jenis paket</option>`;
                        for( x in paket_type_json ) {
                           html += `<option value="${x}" ${x == paket_type_selected ? 'selected' : '' }>${paket_type_json[x]['name']}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Diskon</label>
                                 <input type="text" onKeyup="sumTotalFasilitas()" name="diskon" value="${diskon}" class="form-control form-control-sm currency" placeholder="Diskon" />
                              </div>
                           </div>
                           <div class="col-2">
                              <div class="form-group">
                                 <label>J. Jamaah</label>
                                 <input type="number" onKeyup="sumTotalFasilitas()" name="jamaah" value="${jamaah}" class="form-control form-control-sm" placeholder="J. Jamaah" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Tanggal Keberangkatan</label>
                                 <input type="date" name="tanggal_keberangkatan" value="${tanggal_keberangkatan}" class="form-control form-control-sm" placeholder="Tanggal Keberangkatan" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Tanggal Kepulangan</label>
                                 <input type="date" name="tanggal_kepulangan" value="${tanggal_kepulangan}" class="form-control form-control-sm currency" placeholder="Diskon" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Total</label>
                                 <input type="text" name="total" id="total" value="${total_price}" class="form-control form-control-sm" placeholder="Total" disabled />
                              </div>
                           </div>
                           <div class="col-12">
                              <label>Daftar Fasilitas</label>
                              <div class="row">
                                 <div class="col-12 mb-3 pb-2 pt-3 text-center" style="border-bottom: 1px solid #ced4da;font-size: 11px;color: #736f6f;">
                                    <div class="row">
                                       <div class="col-5">
                                          Fasilitas
                                       </div>
                                       <div class="col-2">
                                          Pax
                                       </div>
                                       <div class="col-4">
                                          Harga
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-12 py-2 text-center" id="list_fasilitas">`;
                     if(fasilitas_selected != '' ){
                        html += fasilitas_selected;
                     }else{
                        html +=   `<label style="font-size: 11px;font-weight: normal;">Daftar fasilitas tidak ditemukan</label>`;
                     }
                     html +=    `</div>
                                 <div class="col-12 pb-4 pt-2 text-center" id="total_harga_fasilitas" style="border-top: 1px solid #ced4da;font-size: 11px;color: #736f6f;">`;
                     if( total_price != '' ){
                        html += `<div class="row"><div class="col-7 text-left">Total Harga Fasilitas</div><div class="col-5 pl-3 text-left">${total_price}</div></div>`;
                     }
                     html +=    `</div>
                              </div>
                           </div>
                           <div class="col-12">
                              <button type="button" class="btn btn-default float-right" title="Tambah Fasilitas" onclick="tambah_fasilitas_tipe_paket_la2(this)">
                                 <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Fasilitas
                              </button>
                           </div>
                        </div>
                     </div>
                     <div class="col-5">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alamat Klien</label>
                                 <textarea class="form-control form-control-sm" name="alamat_klien" rows="5"
                                    style="resize: none;" placeholder="Alamat Klien" required>${alamat_klien}</textarea>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Dekripsi</label>
                                 <textarea class="form-control form-control-sm" name="description" rows="5"
                                    style="resize: none;" placeholder="Deskripsi" required>${discription}</textarea>
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

function row_fasilitas(JSONFasilitas, fasilitas_id, pax, harga ){
   var jsonfasilitas = JSON.parse(JSONFasilitas);
   var html = `<div class="row rowFasilitas">
                  <div class="col-5">
                     <div class="form-group">
                        <select class="form-control form-control-sm jenisFasilitas" name="jenis_fasilitas[]" onChange="checkDistincJenisPaket(this)" >
                           <option value="0">Pilih jenis paket</option>`;
               for( x in jsonfasilitas ) {
                  html += `<option value="${x}" ${ x == fasilitas_id ? 'selected' : '' }>${jsonfasilitas[x]}</option>`;
               }
               html += `</select>
                     </div>
                  </div>
                  <div class="col-2">
                     <div class="form-group">
                        <input type="number" name="pax[]" value="${pax}" class="form-control form-control-sm" placeholder="Pax" onkeyup="sumTotalFasilitas()"/>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="form-group">
                        <input type="text" name="harga[]" value="${harga}" class="form-control form-control-sm currency" placeholder="Harga" onkeyup="sumTotalFasilitas()" />
                     </div>
                  </div>
                  <div class="col-1">
                     <button type="button" class="btn btn-default" title="Delete" onclick="delete_row_paket_la(this)">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </div>
               </div>`;
   return html;
}

function tambah_fasilitas_tipe_paket_la2(){
   var html = row_fasilitas($('#jsonfasilitas').val(), '','','');
   if( $('.rowFasilitas').length > 0 ) {
      $('#list_fasilitas').append(html);
   }else{
      $('#list_fasilitas').html(html);
   }
}

// get list fasilitas tipe paket la
function getListFasilitasTipePaketLA(){
   var jenis_paket = $('#jenis_paket').val();
   var html = '';
   var total = 0;
   if( jenis_paket != 0 ) {
      var jsonpakettype = JSON.parse($('#jsonpakettype').val());
      var fasilitas = jsonpakettype[jenis_paket]['list_fasilitas'];
      for( x in fasilitas ) {
         html += row_fasilitas($('#jsonfasilitas').val(), fasilitas[x][0], fasilitas[x][1], 'Rp ' + numberFormat(fasilitas[x][3]));
      }
   } else {
      html += '<label style="font-size: 11px;font-weight: normal;">Daftar fasilitas tidak ditemukan</label>';
   }
   $('#list_fasilitas').html(html);
   sumTotalFasilitas();
}

// jumlah total fasilitas
function sumTotalFasilitas(){
   var jumlah_jamaah = $('input[name^=jamaah]').val();
   if(jumlah_jamaah == '' ){
      jumlah_jamaah = 0;
   }
   var diskon = hide_currency($('input[name^=diskon]').val());
   var pax = $('input[name^=pax]').map(function(idx, elem) {
      return $(elem).val();
   }).get();
   var harga = $('input[name^=harga]').map(function(idx, elem) {
      return hide_currency($(elem).val());
   }).get();
   var total = 0;
   for( x in pax ){
      total = total + (pax[x] * harga[x]);
   }
   $('#total').val( 'Rp ' + numberFormat((total * jumlah_jamaah) - diskon) );
   $('#total_harga_fasilitas').html(`<div class="row"><div class="col-7 text-left">Total Harga Fasilitas</div><div class="col-5 pl-3 text-left">Rp ${numberFormat(total)}</div></div>`);
}

function pembayaran_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/get_info_pembayaran", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-9',
               title: 'Form Pembayaran Transaksi Paket LA',
               theme: 'material',
               content: formpembayaran_trans_paket_la(id, JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                      return true;
                  },
                  cetak:{
                     text: 'Cetak Kwitansi Terakhir',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_x(
                           baseUrl + "Trans_paket_la/lastKwitansiPembayaran", function(e) {
                             window.open(baseUrl + "Kwitansi/", "_blank");
                           },[{id: id}]
                        );
                     }
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_trans_paket_la(20);
                              window.open(baseUrl + "Kwitansi/", "_blank");
                           }
                        });
                     }
                  }

               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function formpembayaran_trans_paket_la(id, JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Trans_paket_la/proses_addupdate_pembayaran_trans_paket_la" id="form_utama" class="formName">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <label class="float-right">Nomor Invoice : <b style="color:red;">#${json.invoice}</b></label>
                        <input type="hidden" name="invoice" value="${json.invoice}">
                        <input type="hidden" name="id" value="${id}">
                     </div>
                     <div class="col-12">
                        <table class="table table-hover tablebuka">
                           <thead>
                              <tr>
                                 <th style="width:15%;">Nomor Invoice</th>
                                 <th style="width:20%;">Biaya</th>
                                 <th style="width:20%;">Status</th>
                                 <th style="width:20%;">Tanggal Transaksi</th>
                                 <th style="width:20%;">Penerima</th>
                                 <th style="width:5%;">Aksi</th>
                              </tr>
                           </thead>
                           <tbody id="bodyTable_trans_paket_la">`;
               if( json.riwayat.length > 0 ) {
                  for( x in json.riwayat ) {
                     html += `<tr>
                                 <td>${json.riwayat[x]['invoice']}</td>
                                 <td>Rp ${numberFormat(json.riwayat[x]['paid'])}</td>
                                 <td>${json.riwayat[x]['status']}</td>
                                 <td>${json.riwayat[x]['tanggal_transaksi']}</td>
                                 <td>${json.riwayat[x]['receiver']}</td>
                                 <td>
                                    <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Paket LA"
                                        onclick="cetak_kwitansi_paket_la('${json.riwayat[x]['invoice']}')">
                                        <i class="fas fa-print" style="font-size: 11px;"></i>
                                    </button>
                                 </td>
                              </tr>`;
                  }
               }else{
                  html += `<tr>
                              <td colspan="6">Daftar riwayat transaksi paket la tidak ditemukan</td>
                           </tr>`;
               }
               html +=    `</tbody>
                        </table>
                     </div>
                     <div class="col-12">
                        <div class="row">
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Total Harga</label>
                                 <input type="text" id="total_harga" value="Rp ${numberFormat(json.total_harga)}" class="form-control form-control-sm" placeholder="Total Harga" disabled/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Bayar</label>
                                 <input type="text" name="bayar" id="bayar" class="form-control form-control-sm currency" placeholder="Bayar" onKeyup="countPembayaran()" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sudah Dibayar</label>
                                 <input type="text" id="sudah_dibayar" value="Rp ${numberFormat(json.total_bayar)}" class="form-control form-control-sm" placeholder="Sudah Dibayar" disabled/>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Sisa</label>
                                 <input type="text" id="sisa" value="Rp ${numberFormat(json.sisa)}" class="form-control form-control-sm" placeholder="Sisa" disabled/>
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nama Penyetor</label>
                                 <input type="text" name="nama_penyetor" class="form-control form-control-sm" placeholder="Nama Penyetor" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nomor HP Penyetor</label>
                                 <input type="text" name="no_hp_penyetor" id="no_hp_penyetor" value="" class="form-control form-control-sm" placeholder="No HP Penyetor" />
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Alamat Penyetor</label>
                                 <textarea class="form-control form-control-sm" name="alamat_penyetor" rows="3"
                                    style="resize: none;" placeholder="Alamat Penyetor" required></textarea>
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

function cetak_kwitansi_paket_la( invoice ){

   ajax_x(
      baseUrl + "Trans_paket_la/cetak_kwitansi_paket_la", function(e) {
         if( e['error'] == false ){
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
            if ( e['error'] == true ) {
               return false;
            } else {
               get_trans_paket_la(20);
               window.open(baseUrl + "Kwitansi/", "_blank");
            }

            // $.confirm({
            //    columnClass: 'col-6',
            //    title: 'Refund Transaksi Paket LA',
            //    theme: 'material',
            //    content: form_refund_trans_paket_la(id, e['invoice'], e['wasPaid']),
            //    closeIcon: false,
            //    buttons: {
            //       cancel:function () {
            //           return true;
            //       },
            //       simpan: {
            //          text: 'Simpan',
            //          btnClass: 'btn-blue',
            //          action: function () {
            //             ajax_submit_t1("#form_utama", function(e) {
            //                e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
            //                if ( e['error'] == true ) {
            //                   return false;
            //                } else {
            //                   get_trans_paket_la(20);
            //                   window.open(baseUrl + "Kwitansi/", "_blank");
            //                }
            //             });
            //          }
            //       }
            //    }
            // });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{invoice:invoice}]
   );

}

function countPembayaran(){
   var total_harga = hide_currency($('#total_harga').val());
   var sudah_dibayar = hide_currency($('#sudah_dibayar').val());
   var pembayaran = hide_currency($('#bayar').val());
   if( total_harga >= (pembayaran + sudah_dibayar) ) {
      var sisa = total_harga - (sudah_dibayar + pembayaran);
      $('#sisa').val('Rp ' + numberFormat(sisa));
   }else{
      var minPembayaran = pembayaran.toString().length - 1;
      var realPembayaran = pembayaran.toString().substring(0, parseInt(minPembayaran));
      $('#bayar').val('Rp ' + numberFormat(realPembayaran) );
      frown_alert('Pembayaran tidak boleh lebih dari total harga');
   }
}

function delete_row_paket_la(e){
   var lengthSaldo = $('.rowFasilitas').length;
   if( lengthSaldo > 1 ){
      $(e).parent().parent().remove();
      sumTotalFasilitas();
   }else{
      frown_alert('Anda wajib menyisakan minimal 1 row fasilitas');
   }
}

function checkDistincJenisPaket(e){
   var list_jenis_fasilitas = new Array();
   $('.jenisFasilitas').each(function(index) {
      if( $(this).val() != 0 ) {
         list_jenis_fasilitas.push($(this).val());
      }
   });
   var unique_jenis_fasilitas = list_jenis_fasilitas.filter((v, i, a) => a.indexOf(v) === i);
   if(list_jenis_fasilitas.length != unique_jenis_fasilitas.length ) {
      $(e).val(0);
      frown_alert('Tidak dapat memilih fasilitas yang sama.');
   }
}

function form_k_t_paket_la(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<div class="content" id="contents">
                     <div class="col-12 col-lg-12 my-3 px-0">
                        <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                           <tbody>
                              <tr>
                                 <td style="width:45%;" class="text-left">KEGIATAN ANGGARAN CLIENT ${json.client_name.toUpperCase()}</td>
                                 <td style="width:1%;">:</td>
                                 <td style="width:54%;" class="text-left total_anggaran" id="total_anggaran">Rp ${numberFormat(json.total_price)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">AKTUALISASI BELANJA CLIENT ${json.client_name.toUpperCase()}</td>
                                 <td>:</td>
                                 <td class="text-left undefined" id="aktualisasi_anggaran">Rp ${numberFormat(json.total_aktualisasi)}</td>
                              </tr>
                              <tr>
                                 <td class="text-left">KEUNTUNGAN PROGRAM CLIENT ${json.client_name.toUpperCase()}</td>
                                 <td>:</td>
                                 <td class="text-left undefined" id="keuntungan">Rp ${numberFormat(json.keuntungan)}</td>
                              </tr>
                           </tbody>
                         </table>
                     </div>
                     <div class="col-12 my-3 px-0">
                        <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                           <thead>
                              <tr>
                                 <th style="width:5%;">NO</th>
                                 <th style="width:38%;">URAIAN</th>
                                 <th style="width:5%;">QT</th>
                                 <th style="width:19%;">HARGA</th>
                                 <th style="width:19%;">JUMLAH HARGA</th>
                                 <th style="width:14%;">AKSI</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr style="font-weight:bold;">
                                 <td></td>
                                 <td class="text-left">Potensi Pendapatan Paket LA Client ${json.client_name}</td>
                                 <td></td>
                                 <td></td>
                                 <td class="text-right">Rp ${numberFormat(json.total_price)}</td>
                                 <td></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-12 my-3 px-0">
                        <table class="table table-hover tablebuka" style="border: 1px solid #dee1e6 !important;">
                           <tbody id="table_rincian">
                              <tr style="background-color: #f1f5fd;font-weight:bold;">
                                 <td style="width:5%;">A</td>
                                 <td style="width:38%;" class="text-left">Rincian Pendapatan Paket LA Client ${json.client_name}</td>
                                 <td style="width:5%;"></td>
                                 <td style="width:19%;"></td>
                                 <td style="width:19%;" class="text-right">Rp ${numberFormat(json.total_price)}</td>
                                 <td style="width:14%;"></td>
                              </tr>
                              <tr>
                                 <td >1</td>
                                 <td class="text-left">Sudah Dibayar</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right">Rp ${numberFormat(json.sudah_bayar)}</td>
                                 <td ></td>
                              </tr>
                              <tr>
                                 <td >2</td>
                                 <td class="text-left">Diskon</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right">Rp ${numberFormat(json.discount)}</td>
                                 <td ></td>
                              </tr>
                              <tr>
                                 <td >3</td>
                                 <td class="text-left">Piutang</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right">Rp ${numberFormat( ( json.total_price -json.discount ) - json.sudah_bayar )}</td>
                                 <td ></td>
                              </tr>
                              <tr style="background-color: #f1f5fd;font-weight:bold;">
                                 <td style="vertical-align: middle;">B</td>
                                 <td colspan="3" style="vertical-align: middle;" class="text-left">Aktualisasi Kegiatan Anggaran Paket LA Client ${json.client_name}</td>
                                 <td style="vertical-align: middle;" class="text-right">Rp ${numberFormat(json.total_aktualisasi)}</td>
                                 <td style="vertical-align: middle;">
                                    <button class="btn btn-default btn-action" title="Tambah uraian aktualisasi anggaran" onclick="add_aktualisasi('${json.id}')">
                                        <i class="fas fa-plus" style="font-size: 11px;"></i>
                                    </button>
                                 </td>
                              </tr>`;
                  let i = 1;
                  for( x in json.aktualisasi ){
                     html += `<tr>
                                 <td class="position">${i}</td>
                                 <td class="text-left" id="u${i}">${json.aktualisasi[x]['name']}</td>
                                 <td ></td>
                                 <td ></td>
                                 <td class="text-right" id="h${i}">Rp ${numberFormat( json.aktualisasi[x]['harga'] )}</td>
                                 <td class="px-0" id="btn${i}">
                                    <button class="btn btn-default btn-action mx-1" title="Edit Kas Transaksi" onclick="edit_kas_transaksi_paket_la( ${i}, '${json.aktualisasi[x]['id']}', '${json.aktualisasi[x]['ket']}', '${json.aktualisasi[x]['action']}', '${json.id}')">
                                        <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                                    </button>`;
                        if( json.aktualisasi[x]['ket'] == "add" ){
                           html +=    `<button class="btn btn-default btn-action mx-1" title="Delete Kas Transaksi" onclick="delete_kas_transaksi_paket_la('${json.aktualisasi[x]['id']}', '${json.id}')">
                                           <i class="fas fa-times" style="font-size: 11px;"></i>
                                       </button>`;
                        }
                        html += `</td>
                              </tr>`;
                     i++;
                  }
               html +=    `</tbody>
                        </table>
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
               </script>`;
   return html;
}

// close paket la
// function close_paket_la(id){
//    ajax_x(
//       baseUrl + "Trans_paket_la/close_paket_la", function(e) {
//          if( e['error'] == false ){
            
//             get_trans_paket_la(20);
//             smile_alert(e['error_msg']);


//          }else{
//             frown_alert(e['error_msg']);
//          }
//       },[{id:id}]
//    );
// }

function edit_kas_transaksi_paket_la(position, id, ket, action, paket_la_id) {
   if( $('.input_harga').length == 0  ) {
      if( ket == 'add' ) {
         $('#u'+position).html(`<input type="text" id="uraian${position}" value="${$('#u'+position).text()}" class="form-control form-control-sm" placeholder="Uraian">`);
      }
      $('#h'+position).html(`<input type="text" value="${$('#h'+position).text()}" id="harga${position}" class="form-control form-control-sm currency" placeholder="Harga">`);
      var btn =  `<button class="btn btn-default btn-action mx-1 input_harga" title="Simpan Kas Transaksi"
                        onclick="save_kas_transaksi_paket_la( ${position}, '${id}', '${ket}', '${action}', '${paket_la_id}')">
                     <i class="fas fa-save" style="font-size: 11px;"></i>
                  </button>`;
      if( ket == "add" ) {
          btn += `<button class="btn btn-default btn-action mx-1" title="Delete Kas Transaksi"
                        onclick="delete_kas_transaksi_paket_la('${id}', '${paket_la_id}')">
                     <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>`;
      }
      $('#btn'+position).html(btn);
   }else{
      frown_alert('Anda tidak dapat mengedit kolom ini, karena masih terdapat kolom yang belum disimpan');
   }
}

function delete_kas_transaksi_paket_la(id, paket_la_id){
   ajax_x(
      baseUrl + "Trans_paket_la/delete_kas_transaksi_paket_la", function(e) {
         if( e['error'] == false ){
            $('#contents').replaceWith(form_k_t_paket_la(JSON.stringify(e['data'])));
         }else{
            frown_alert(e['error_msg']);
         }
      },[{paket_la_id:paket_la_id, id:id}]
   );
}

function add_aktualisasi(paket_la_id){
   if( $('.input_harga').length == 0  ) {
      var position = $('.position').length + 1;
      var html = `<tr>
                  <td class="position">${position}</td>
                  <td class="text-left" id="u${position}">
                     <input type="text" id="uraian${position}" value="" class="form-control form-control-sm" placeholder="Uraian"></td>
                  <td ></td>
                  <td ></td>
                  <td class="text-right" id="h${position}">
                     <input type="text" value="" id="harga${position}" class="form-control form-control-sm currency" placeholder="Harga">
                  </td>
                  <td class="px-0" id="btn${position}">
                     <button class="btn btn-default btn-action mx-1 input_harga" title="Simpan Kas Transaksi"
                           onclick="save_kas_transaksi_paket_la( ${position}, '', 'add', 'insert', '${paket_la_id}')">
                        <i class="fas fa-save" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
      $('#table_rincian').append(html);
   }else{
      frown_alert('Anda tidak dapat mengedit kolom ini, karena masih terdapat kolom yang belum disimpan');
   }
}

function save_kas_transaksi_paket_la(position, id, ket, action, paket_la_id){
   var uraian = '';
   var harga = $('#harga'+position).val();
   if( ket == 'add'){
      uraian = $('#uraian'+position).val();
   }
   ajax_x(
      baseUrl + "Trans_paket_la/proses_addupdate_kas_transaksi_paket_la", function(e) {
         if( e['error'] == false ){
            $('#contents').replaceWith(form_k_t_paket_la(JSON.stringify(e['data'])));
         }else{
            frown_alert(e['error_msg']);
         }
      },[{paket_la_id:paket_la_id, id:id, ket:ket, action:action, harga:harga, uraian:uraian}]
   );
}

function k_t_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/get_info_kas_transaksi", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-8',
               title: 'K & T Paket LA',
               theme: 'material',
               content: form_k_t_paket_la(JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  tutup:function () {
                      return true;
                  },
                  tutup_paket: {
                     text: 'Tutup Paket',
                     btnClass: 'btn-red',
                     action: function () {

                        $.confirm({
                           columnClass: 'col-3',
                           title: 'K & T Paket LA',
                           theme: 'material',
                           content: 'Apakah anda yakin ingin menutup Paket La ini?',
                           closeIcon: false,
                           buttons: {
                              tidak:function () {
                                  return true;
                              },
                              iya: {
                                 text: 'Iya',
                                 btnClass: 'btn-red',
                                 action: function () {
                                    ajax_x(
                                       baseUrl + "Trans_paket_la/close_paket_la", function(e) {
                                          if( e['error'] == false ){
                                             get_trans_paket_la(20);
                                             smile_alert(e['error_msg']);
                                          }else{
                                             frown_alert(e['error_msg']);
                                          }
                                       },[{id:id}]
                                    );
                                 }
                              }
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function delete_paket_la(id){
   ajax_x(
      baseUrl + "Trans_paket_la/delete_paket_la", function(e) {
         if( e['error'] == false ){
             get_trans_paket_la(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}
