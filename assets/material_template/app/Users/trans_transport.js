function trans_transport_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarTransport">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_transaksi_transport()">
                        <i class="fas fa-subway"></i> Tambah Transaksi Transport
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_transaksi_transport( 20)" id="searchAllDaftarTransport" name="searchAllDaftarTransport" placeholder="Nomor Invoice" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_transaksi_transport( 20 )">
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
                              <th style="width:18%;">Nama/Nomor Identitas <br> Pembayar</th>
                              <th style="width:35%;">Info Transport</th>
                              <th style="width:12%;">Total</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_transport">
                           <tr>
                              <td colspan="6">Daftar transaksi transport tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_transport"></div>
                  </div>
               </div>
            </div>`;
}


function trans_transport_getData(){
   get_daftar_transaksi_transport(20);
}

function get_daftar_transaksi_transport(perpage){
   get_data( perpage,
             { url : 'Trans_transport/daftar_transaksi_transport',
               pagination_id: 'pagination_daftar_transport',
               bodyTable_id: 'bodyTable_daftar_transport',
               fn: 'ListDaftarTransport',
               warning_text: '<td colspan="6">Daftar transaksi transport tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarTransport').val() } } );
}

function ListDaftarTransport(JSONData){
   var json = JSON.parse(JSONData);
   var detail = '';
   for( x in json.detail ) {
      detail +=  `<div class="row">
                     <div class="col-12">
                        <table class="table table-hover">
                           <tbody>
                              <tr>
                                 <td class="text-left py-0" style="width:36%;border:none;">Jenis Mobil</td>
                                 <td class="text-left py-0 px-0" style="width:64%;border:none;">: ${json.detail[x]['jenis_mobil']}</td>
                              </tr>
                              <tr>
                                 <td class="text-left py-0" style="border:none;">Plat Mobil</td>
                                 <td class="text-left py-0 px-0" style="border:none;">: ${json.detail[x]['nomor_plat']}</td>
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
                     <button type="button" class="btn btn-default btn-action" title="Cetak Transaksi Transport"
                        onclick="cetak_transaksi_transport('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Transaksi Transport"
                        onclick="delete_transaksi_transport('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_transaksi_transport(){
   ajax_x(
      baseUrl + "Trans_transport/get_info_transaksi_transport", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Tambah Transaksi Transport',
               theme: 'material',
               content: formaddupdate_trans_transport( e['invoice'], JSON.stringify(e['list_car']) ),
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
                              get_daftar_transaksi_transport(20);
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

function formaddupdate_trans_transport(invoice, JSONCarList){
   var id_trans_transport = '';
   var html = `<form action="${baseUrl }Trans_transport/proses_addupdate_transport" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row ">
                           <div class="col-6 text-left">
                              <label><span class="float-left" style="color:red">(*) Wajib diisi</span></label>
                           </div>
                           <div class="col-6 text-right">
                              <label class="float-right">INVOICE :<span style="color:red"> #${invoice}</span></label>
                              ${id_trans_transport}
                              <input type="hidden" name="invoice" value="${invoice}">
                              <input type="hidden" id="jsondata_list_car" value='${JSONCarList}' >
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-7">
                              <div class="form-group text-left mb-2">
                                 <input type="text" class="form-control form-control-sm" name="nama" value="" placeholder="Nama pelanggan">
                                 ${text_helper('Nama Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group text-left mb-2">
                                 <input type="text" class="form-control form-control-sm" name="nomor_identitas" value="" placeholder="Nomor Identitas Pelanggan">
                                 ${text_helper('Nomor Identitas Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group text-left mb-2">
                                 <textarea class="form-control" name="address" rows="3" style="resize:none;" placeholder="Alamat Pelanggan."></textarea>
                                 ${text_helper('Alamat Pelanggan. <span class="float-right" style="color:red">(*)</span>')}
                              </div>
                           </div>
                           <div class="col-12">
                              <table class="table table-hover tablebuka">
                                 <thead>
                                    <tr>
                                       <th style="width:90%;">Info Mobil</th>
                                       <th style="width:10%;">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody id="bodyTable_daftar_transaksi_transport">
                                    ${rowTransport(JSONCarList)}
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                       <td colspan="2">
                                          <div class="row" style="background-color: beige;">
                                             <div class="col-12 py-3 text-right">
                                                <button type="button" class="btn btn-default" title="Tambah Row Transaksi Transpoort" onclick="add_row_transport(this)">
                                                   <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row Transaksi Transport
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

function rowTransport(JSONCarList){
   var CarList = JSON.parse(JSONCarList);
   var car = '';
   var nomor_plat = '';
   var price = '';
   var html =  `<tr>
                  <td>
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group text-left mb-2">
                              <select class="form-control form-control-sm" name="car_list[]">`;
                     for( y in CarList ) {
                        html += `<option value="${y}" ${car == y ? 'selected' : ''}>${CarList[y]}</option>`;
                     }
                     html += `</select>
                              ${text_helper('Jenis Mobil. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm" name="nomor_plat[]" value="${nomor_plat}" placeholder="Nomor Plat Mobil">
                              ${text_helper('Nomor Plat Mobil. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="form-group text-left mb-2">
                              <input type="text" class="form-control form-control-sm currency harga_paket" name="price[]" value="${price}" placeholder="Harga per Paket">
                              ${text_helper('Harga per Paket. <span class="float-right" style="color:red">(*)</span>')}
                           </div>
                        </div>
                     </div>
                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Delete Row Transaksi Hotel" onclick="delete_row_transport(this)" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
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

function  add_row_transport(e){
   $('#bodyTable_daftar_transaksi_transport').append( rowTransport( $('#jsondata_list_car').val() ) );
}

function delete_row_transport(e){
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

function delete_transaksi_transport(id){
   ajax_x(
      baseUrl + "Trans_transport/delete_transport", function(e) {
         if( e['error'] == false ){
             get_daftar_transaksi_transport(20);
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

function cetak_transaksi_transport(id){
   ajax_x(
      baseUrl + "Trans_transport/cetak_transaksi_transport", function(e) {
         if ( e['error'] == true ) {
            $.alert({
               icon: 'far fa-frown',
               title: 'Peringatan',
               content: 'Kwitansi transaksi transport gagal dicetak.',
               type: 'red',
            });
         } else {
            get_daftar_transaksi_transport(20);
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{id:id}]
   );
}
