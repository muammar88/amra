function investor_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarInvestor">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_investor()">
                        <i class="fas fa-hand-holding-usd"></i> Tambah Investor
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_investor( 20)" id="searchAllDaftarInvestor" name="searchAllDaftarInvestor" placeholder="Nama & Nomor Identitas Investor" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_investor( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:30%;">Nama & Nomor Identitas Investor</th>
                              <th style="width:40%;">Nomor Kontak & Alamat</th>
                              <th style="width:20%;">Investasi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_investor">
                           <tr>
                              <td colspan="4">Daftar investor tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_investor"></div>
                  </div>
               </div>
            </div>`;
}

function investor_getData(){
   get_daftar_investor(20);
}

// daftar investor
function get_daftar_investor(perpage){
   get_data( perpage,
      { url : 'Investor/daftar_investor',
        pagination_id: 'pagination_daftar_investor',
        bodyTable_id: 'bodyTable_daftar_investor',
        fn: 'ListDaftarInvestor',
        warning_text: '<td colspan="4">Daftar investor tidak ditemukan</td>',
        param : { search : $('#searchAllDaftarInvestor').val() } } );
}

function ListDaftarInvestor(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.nama}<br>(${json.nomor_identitas})</td>
                  <td>${json.no_hp}<br>${json.alamat}</td>
                  <td>${kurs} ${numberFormat(json.investasi)}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Investor"
                        onclick="edit_investor('${json.id}')" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Cabut Investasi"
                        onclick="cabut_investor('${json.id}')" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-hand-holding-usd" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Investor"
                        onclick="delete_investor('${json.id}')" style="margin:.15rem .1rem  !important">
                        <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`
   return html;
}

function cabut_investor(id){
   ajax_x(
      baseUrl + "Investor/cabut_investor", function(e) {
         if( e['error'] == false ){
            get_daftar_investor(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id: id}]
   );
}

function delete_investor(id){
   ajax_x(
      baseUrl + "Investor/delete_investor", function(e) {
         if( e['error'] == false ){
            get_daftar_investor(20);
        }
        e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id: id}]
   );
}

function edit_investor(id){
   ajax_x(
      baseUrl + "Investor/get_info_edit_investor", function(e) {
      if( e['error'] == false ){
         $.confirm({
            columnClass: 'col-4',
            title: 'Edit Investor',
            theme: 'material',
            content: formaddupdate_investor(JSON.stringify(e['data']) ),
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
                           get_daftar_investor(20);
                        }
                     });
                  }
               }
            }
         });
        }else{
         frown_alert(e['error_msg']);
        }
      },[{id: id}]
   );
}

function add_investor(){
   ajax_x(
      baseUrl + "Investor/get_investor", function(e) {
      if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Investor',
               theme: 'material',
               content: formaddupdate_investor(),
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
                              get_daftar_investor(20);
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

function formaddupdate_investor(JSONValue){
   var id = '';
   var nama = '';
   var nomor_identitas = '';
   var no_hp = '';
   var alamat = '';
   var investasi = kurs + ' 0';
   var saham = 0;
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" name="id" value="${value.id}">`;
      nama = value.nama;
      nomor_identitas = value.nomor_identitas;
      no_hp = value.no_hp;
      alamat = value.alamat;
      investasi = kurs + ' ' + numberFormat(value.investasi);
      saham = value.saham;
   }
   var html = `<form action="${baseUrl }Investor/proses_addupdate_investor" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id}
                                 <label>Nama Investor</label>
                                 <input type="text" name="nama" value="${nama}" class="form-control form-control-sm" placeholder="Nama Investor" />
                              </div>
                           </div>
                           <div class="col-10">
                              <div class="form-group">
                                 <label>Nomor Identitas Investor</label>
                                 <input type="text" name="nomor_identitas" value="${nomor_identitas}" class="form-control form-control-sm" placeholder="Nomor Identitas Investor" />
                              </div>
                           </div>
                           <div class="col-10">
                              <div class="form-group">
                                 <label>Nomor Kontak Investor</label>
                                 <input type="text" name="no_hp" value="${no_hp}" class="form-control form-control-sm" placeholder="Nomor Kontak Investor" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alamat</label>
                                 <textarea class="form-control form-control-sm" name="alamat" placeholder="Alamat"  style="resize:none;">${alamat}</textarea>
                              </div>
                           </div>
                           <div class="col-7">
                              <div class="form-group">
                                 <label>Investasi</label>
                                 <input type="text" name="investasi" value="${investasi}" class="currency form-control form-control-sm" placeholder="Investasi" />
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Saham</label>
                                 <input type="text" name="saham" value="${saham}" class="form-control form-control-sm" max="100" maxlength="3" placeholder="Saham" />
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
