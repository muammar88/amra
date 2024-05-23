function markup_produk_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarMarkupPr">
                  <div class="col-6 col-lg-8 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="tipe" id="tipe" onChange="get_markup_produk(20)" title="Status Transaksi">
                           <option value="prabayar" >Prabayar</option>
                           <option value="pascabayar" >Pascabayar</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_markup_produk(20)" id="searchMarkupPPOB" name="searchMarkupPPOB" placeholder="Nama Produk / Kode Produk" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_markup_produk(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                         <button class="btn btn-default float-left ml-1" onClick="setGeneralSettingMarkupCompany()" type="button" title="General Setting Markup Company">
                           <i class="fas fa-cogs"></i>
                        </button>
                     </div>

                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:20%;">Nama Produk</th>
                              <th style="width:15%;">Kode Produk</th>
                              <th style="width:15%;">Tipe Produk</th>
                              <th style="width:20%;">Harga Aplikasi</th>
                              <th style="width:20%;">Markup Perusahaan</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_markup_produk">
                           <tr>
                              <td colspan="6">Daftar markup produk tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_markup_produk"></div>
                  </div>
               </div>
            </div>`;
}

function markup_produk_getData() {
   get_markup_produk(20);
}

function get_markup_produk(perpage){
   get_data( perpage,
          { url : 'PPOB/markup_server_side',
            pagination_id: 'pagination_daftar_markup_produk',
            bodyTable_id: 'bodyTable_daftar_markup_produk',
            fn: 'ListDaftarMarkupPPOB',
            warning_text: '<td colspan="6">Daftar markup produk  tidak ditemukan</td>',
            param : { search : $('#searchMarkupPPOB').val(), tipe : $('#tipe').val() } } );
}

function ListDaftarMarkupPPOB(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.product_name}</td>
                  <td>${json.product_code}</td>
                  <td>${json.tipe}</td>
                  <td>Rp ${numberFormat(json.harga_aplikasi)}</td>
                  <td>Rp ${numberFormat(json.markup_perusahaan)}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Markup PPOB"
                        onclick="edit_markup_ppob('${json.product_code}', '${json.tipe}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Markup PPOB"
                        onclick="delete_markup_ppob('${json.product_code}', '${json.tipe}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function edit_markup_ppob(product_code, tipe){
   ajax_x(
      baseUrl + "PPOB/get_edit_ppob", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Markup PPOB',
               theme: 'material',
               content: formaddupdate_markup_ppob(product_code, tipe, e['data']),
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
                              get_markup_produk(20);
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[{product_code:product_code, tipe:tipe}]
   );
}

function formaddupdate_markup_ppob(product_code, tipe, value){
   var html = `<form action="${baseUrl }PPOB/proses_edit_markup_ppob" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <input type="hidden" name="product_code" value="${product_code}">
                                 <input type="hidden" name="tipe" value="${tipe}">
                                 <label>Markup Perusahaan</label>
                                 <input type="text" name="markup_perusahaan" value="Rp ${numberFormat(value)}" class="form-control form-control-sm currency" placeholder="Markup Perusahaan" />
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

function delete_markup_ppob(product_code, tipe){
   ajax_x(
      baseUrl + "PPOB/delete_edit_ppob", function(e) {
         if( e['error'] == false ){
            get_markup_produk(20);
         }else{
            frown_alert(e['error_msg']);
         }
      },[{product_code:product_code, tipe:tipe}]
   );
}

// set general setting markup
function setGeneralSettingMarkupCompany(){
   ajax_x(
      baseUrl + "PPOB/get_edit_markup_company", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-6',
               title: 'Edit Markup Default Perusahaan',
               theme: 'material',
               content: formaddupdate_markup_perusahaan(e['data']),
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
                              get_markup_produk(20);
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

// form markup perusahaan
function formaddupdate_markup_perusahaan(markup){
 var html = `<form action="${baseUrl }PPOB/proses_edit_markup_perusahaan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Markup Default Perusahaan</label>
                                 <input type="text" name="markup_perusahaan" value="Rp ${numberFormat(markup)}" class="form-control form-control-sm currency" placeholder="Markup Perusahaan" />
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