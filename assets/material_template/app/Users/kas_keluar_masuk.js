function kas_keluar_masuk_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarKasKeluarMasuk">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_transaksi_keluar_masuk()">
                        <i class="fas fa-hand-holding-usd"></i> Tambah Transaksi Keluar Masuk
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_kas_keluar_masuk( 20)" id="searchAllDaftarKasKeluarMasuk" name="searchAllDaftarKasKeluarMasuk" placeholder="Nomor Invoice" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_kas_keluar_masuk( 20 )">
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
                              <th style="width:20%;">Dibayar/Diterima Dari</th>
                              <th style="width:30%;">Akun Terlibat</th>
                              <th style="width:15%;">Status Kwitansi</th>
                              <th style="width:15%;">Tanggal Transaksi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_kas_keluar_masuk">
                           <tr>
                              <td colspan="6">Daftar kas keluar masuk tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_kas_keluar_masuk"></div>
                  </div>
               </div>
            </div>`;
}

function kas_keluar_masuk_getData(){
   get_daftar_kas_keluar_masuk(20);
}

function get_daftar_kas_keluar_masuk(perpage){
   get_data( perpage,
             { url : 'Kas_keluar_masuk/daftar_kas_keluar_masuk',
               pagination_id: 'pagination_daftar_kas_keluar_masuk',
               bodyTable_id: 'bodyTable_daftar_kas_keluar_masuk',
               fn: 'ListDaftarKasKeluarMasuk',
               warning_text: '<td colspan="6">Daftar kas keluar masuk tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarKasKeluarMasuk').val() } } );
}

function ListDaftarKasKeluarMasuk(JSONData){
   var json = JSON.parse(JSONData);

   var loop = '<ul class="list">';
      for( x in json.akun_terlibat ){
         loop += `<li>${json.akun_terlibat[x]}</li>`;
      }
       loop += '</ul>';
   var html = `<tr>
                  <td>${json.invoice}</td>
                  <td>${json.dibayar_diterima}</td>
                  <td>${loop}</td>
                  <td>${json.status_kwitansi}</td>
                  <td>${json.input_date}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Cetak Kas Keluar Masuk"
                        onclick="cetak_kas_keluar_masuk('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Edit Kas Keluar Masuk"
                        onclick="edit_kas_keluar_masuk('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Kas Keluar Masuk"
                        onclick="delete_kas_keluar_masuk('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function cetak_kas_keluar_masuk(id){
   ajax_x(
      baseUrl + "Kas_keluar_masuk/cetak_kas_keluar_masuk", function(e) {
         if( e['error'] == false ){
            window.open(baseUrl + "Kwitansi/", "_blank");
         }else{
            $.alert({
               icon: e.error == true ? 'far fa-frown' : 'far fa-smile',
               title: 'Peringatan',
               content: e.error_msg,
               type: e.error == true ? 'red' : 'blue',
            });   
         }
         
      },[{id:id}]
   );
}

function delete_kas_keluar_masuk(id){
   ajax_x(
      baseUrl + "Kas_keluar_masuk/delete_kas_keluar_masuk", function(e) {
         if( e['error'] == false ){
             get_daftar_kas_keluar_masuk(20);
         }
         $.alert({
            icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
            title: 'Peringatan',
            content: e['error_msg'],
            type: e['error'] == true ? 'red' : 'blue',
         });
      },[{id:id}]
   );
}

function add_transaksi_keluar_masuk(){
   ajax_x(
      baseUrl + "Kas_keluar_masuk/get_info_transaksi_keluar_masuk", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Tambah Kas Keluar Masuk',
               theme: 'material',
               content: formaddupdate_kas_keluar_masuk(JSON.stringify(e['data'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var error = 0 ;
                        $('.akun_debet').each(function(index){
                           if( $(this).val() == 0 ){ error = 1; }
                        });
                        $('.akun_kredit').each(function(index){
                           if( $(this).val() == 0 ){ error = 1; }
                        });
                        if( error != 1 ) {
                           ajax_submit_t1("#form_utama", function(e) {
                              $.alert({
                                 title: 'Peringatan',
                                 content: e['error_msg'],
                                 type: e['error'] == true ? 'red' :'blue'
                              });
                              if ( e['error'] == true ) {
                                 return false;
                              } else {
                                 get_daftar_kas_keluar_masuk(20);
                                 window.open(baseUrl + "Kwitansi/", "_blank");
                              }
                           });
                        }else{
                           $.alert({
                              title: 'Peringatan',
                              content: 'Silahkan pilih salah satu akun untuk melanjutkan proses penyimpanan.',
                              type:  'red'
                           });
                           return false;
                        }
                     }
                  }
               }
            });
         }else{
            $.alert({
               title: 'Peringatan',
               content: e['error_msg'],
               type: e['error'] == true ? 'red' :'blue'
            });
         }
      },[]
   );
}

function edit_kas_keluar_masuk(id){
   ajax_x(
      baseUrl + "Kas_keluar_masuk/get_info_edit_transaksi_keluar_masuk", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Tambah Kas Keluar Masuk',
               theme: 'material',
               content: formaddupdate_kas_keluar_masuk(JSON.stringify(e['data']), JSON.stringify(e['value'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        var error = 0 ;

                        $('.akun_debet').each(function(index){
                           if( $(this).val() == 0 ){
                              error = 1;
                           }
                        });

                        $('.akun_kredit').each(function(index){
                           if( $(this).val() == 0 ){
                              error = 1;
                           }
                        });

                        if( error != 1 ) {
                           ajax_submit_t1("#form_utama", function(e) {
                              $.alert({
                                 title: 'Peringatan',
                                 content: e['error_msg'],
                                 type: e['error'] == true ? 'red' :'blue'
                              });
                              if ( e['error'] == true ) {
                                 return false;
                              } else {
                                 get_daftar_kas_keluar_masuk(20);
                              }
                           });
                        }else{
                           $.alert({
                              title: 'Peringatan',
                              content: 'Silahkan pilih salah satu akun untuk melanjutkan proses penyimpanan.',
                              type:  'red'
                           });
                           return false;
                        }
                     }
                  }
               }
            });
         }

      },[{id:id}]
   );
}

function formaddupdate_kas_keluar_masuk(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   var list_akun = json.list_akun;
   var invoice = '';
   var id_kas_keluar_masuk = '';
   var tanggal_transaksi = '';
   var diterima_dibayar = '';
   var ref = '';
   var keterangan = '';

   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id_kas_keluar_masuk =  `<input type="hidden" value="${value.id}" name="id">`;
      invoice = value.invoice;
      tanggal_transaksi = value.input_date;
      diterima_dibayar = value.dibayar_diterima;
      ref = value.ref;
      keterangan = value.ket;
   }else{
      invoice = json.invoice;
   }

   var html = `<form action="${baseUrl }Kas_keluar_masuk/proses_addupdate_kas_keluar_masuk" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row ">
                           <div class="col-12 text-right">
                              <label class="float-right">INVOICE :<span style="color:red">#${invoice}</span></label>
                              <input type="hidden" name="invoice" value="${invoice}">
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-5">
                              <div class="form-group">
                                 <input type="hidden" id="jsondata" value='${JSONData}' >
                                 <label>Tanggal Transaksi</label>
                                 ${id_kas_keluar_masuk}
                                 <input type="datetime-local" name="tanggal_transaksi" value="${tanggal_transaksi}" class="form-control form-control-sm" placeholder="Tanggal Transaksi" />
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Diterima dari / Dibayar Kepada</label>
                                 <input type="text" class="form-control form-control-sm" name="diterima_dibayar" value="${diterima_dibayar}" placeholder="Diterima dari / Dibayar kepada" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Ref</label>
                                 <textarea class="form-control form-control-sm" name="ref" rows="6"
                                    style="resize: none;" placeholder="Ref" required>${ref}</textarea>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Keterangan</label>
                                 <textarea class="form-control form-control-sm" name="keterangan" rows="6"
                                    style="resize: none;" placeholder="Keterangan" required>${ref}</textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-12 my-1">
                              <div class="row">
                                 <div class="col-4 text-center">
                                    <label class="my-0">Akun Debet</label>
                                 </div>
                                 <div class="col-4 text-center">
                                    <label class="my-0">Akun Kredit</label>
                                 </div>
                                 <div class="col-3 text-center">
                                    <label class="my-0">Saldo</label>
                                 </div>
                                 <div class="col-1">

                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row" id="row_debet_kredit">`;
            if( JSONValue == undefined ) {
               html += `<div class="col-12 my-1">
                           <div class="row">
                              <div class="col-4">
                                 <select class="form-control form-control-sm akun_debet" name="akun_debet[]">`;
                          for( x in list_akun ) {
                             html += `<option value="${x}">${list_akun[x]}</option>`;
                          }
                        html += `</select>
                              </div>
                              <div class="col-4">
                                 <select class="form-control form-control-sm akun_kredit" name="akun_kredit[]">`;
                          for( x in list_akun ) {
                             html += `<option value="${x}">${list_akun[x]}</option>`;
                          }
                        html += `</select>
                              </div>
                              <div class="col-3">
                                 <input type="text" name="saldo[]" value="" class="form-control form-control-sm currency saldo" placeholder="Saldo">
                              </div>
                              <div class="col-1 text-right pl-0">
                                 <button class="btn btn-default btn-action" title="Delete" onclick="delete_this_kas(this)">
                                    <i class="fas fa-times" style="font-size: 11px;"></i>
                                 </button>
                              </div>
                           </div>
                        </div>`;
            }else{
               var values = JSON.parse(JSONValue);
               for( y in values.akun_terlibat ){
                  html += `<div class="col-12 my-1">
                              <div class="row">
                                 <div class="col-4">
                                    <select class="form-control form-control-sm akun_debet" name="akun_debet[]">`;
                             for( x in list_akun ) {
                                html += `<option value="${x}" ${ values.akun_terlibat[y]['akun_debet'] == x ? 'selected' : ''}>${list_akun[x]}</option>`;
                             }
                           html += `</select>
                                 </div>
                                 <div class="col-4">
                                    <select class="form-control form-control-sm akun_kredit" name="akun_kredit[]">`;
                             for( x in list_akun ) {
                                html += `<option value="${x}" ${ values.akun_terlibat[y]['akun_kredit'] == x ? 'selected' : ''}>${list_akun[x]}</option>`;
                             }
                           html += `</select>
                                 </div>
                                 <div class="col-3">
                                    <input type="text" name="saldo[]" value="${kurs} ${numberFormat(values.akun_terlibat[y]['saldo'])}" class="form-control form-control-sm currency saldo" placeholder="Saldo">
                                 </div>
                                 <div class="col-1 text-right pl-0">
                                    <button class="btn btn-default btn-action" title="Delete" onclick="delete_this_kas(this)">
                                       <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>`;
               }
            }
      html +=           `</div>
                        <div class="row ">
                           <div class="col-12 py-3 text-right">
                              <button type="button" class="btn btn-default" title="Delete" onclick="add_row_kas(this)">
                                 <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Row
                              </button>
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

function add_row_kas(){
   var json = JSON.parse($('#jsondata').val());
   var html = `<div class="col-12 my-1">
                  <div class="row">
                     <div class="col-4">
                        <select class="form-control form-control-sm akun_debet" name="akun_debet[]">`;
                for( x in json.list_akun ) {
                    html += `<option value="${x}">${json.list_akun[x]}</option>`;
                }
               html += `</select>
                     </div>
                     <div class="col-4">
                        <select class="form-control form-control-sm akun_kredit" name="akun_kredit[]">`;
                for( x in json.list_akun ) {
                    html += `<option value="${x}">${json.list_akun[x]}</option>`;
                }
               html += `</select>
                     </div>
                     <div class="col-3">
                        <input type="text" name="saldo[]" value="" class="form-control form-control-sm currency saldo" placeholder="Saldo">
                     </div>
                     <div class="col-1 text-right pl-0">
                        <button class="btn btn-default btn-action" title="Delete" onclick="delete_this_kas(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>
               </div>`;

   $('#row_debet_kredit').append(html);
}


function delete_this_kas(e){
   var lengthSaldo = $('.saldo').length;
   if( lengthSaldo > 1 ){
      $(e).parent().parent().parent().remove();
   }else{
      $.alert({
         icon: 'far fa-frown',
         title: 'Peringatan',
         content: 'Anda wajib menyisakan minimal 1 row akun',
         type: 'red',
      });
   }
}
