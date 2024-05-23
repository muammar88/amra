function level_agen_Pages(){
   return  `<div class="col-6 col-lg-8 my-3">
               <button class="btn btn-default" type="button" onclick="add_level_keagenan()">
                  <i class="fas fa-layer-group"></i> Tambah Level Keagenan
               </button>
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-4 my-3">
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_level_agen(20)"
                     id="searchLevelAgen" name="searchLevelAgen" placeholder="Nama Level Agen"
                     style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_level_agen(20)">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:50%;">Nama Level Agen</th>
                        <th style="width:7%;">Level</th>
                        <th style="width:30%;">Default Fee</th>
                        <th style="width:12%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_level_agen">
                     <tr>
                        <td colspan="4">Daftar level agen tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_level_agen"></div>
            </div>`;
}

function level_agen_getData(){
   get_level_agen(20);
}

function get_level_agen(perpage){
   get_data( perpage,
             { url : 'Daftar_agen/level_agen',
               pagination_id: 'pagination_level_agen',
               bodyTable_id: 'bodyTable_level_agen',
               fn: 'ListLevelAgen',
               warning_text: '<td colspan="4">Level agen tidak ditemukan</td>',
               param : { search : $('#searchLevelAgen').val() } } );
}

function ListLevelAgen(JSONData){
   var json = JSON.parse(JSONData);
   var html =  `<tr>
                  <td>${json.nama}</td>
                  <td>${json.level}</td>
                  <td>Rp ${numberFormat(json.default_fee)}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Level Keagenan"
                       onclick="edit_level_keagenan(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Level Keagenan"
                        onclick="delete_level_keagenan(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}


function add_level_keagenan(){
   ajax_x(
     baseUrl + "Daftar_agen/info_level_keagenan", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Tambah Level Keagenan',
            theme: 'material',
            content: formaddupdate_level_keagenan(e['data']),
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
                           get_level_agen(20);
                        }
                     });
                  }
               }

            }
         });
     },[]
   );
}


function edit_level_keagenan(id){
   ajax_x(
     baseUrl + "Daftar_agen/edit_level_keagenan", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Edit Level Keagenan',
            theme: 'material',
            content: formaddupdate_level_keagenan(e['data'], JSON.stringify(e['value'])),
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
                           get_level_agen(20);
                        }
                     });
                  }
               }
            }
         });
     },[{id:id}]
   );
}

function delete_level_keagenan(id){
   ajax_x(
     baseUrl + "Daftar_agen/delete_level_keagenan", function(e) {
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         if ( e['error'] == false ) {
            get_level_agen(20);
         }
     },[{id:id}]
   );
}

function formaddupdate_level_keagenan(level, JSONValue){
   var id = '';
   var nama = '';
   var default_fee_keagenan = '';
   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" value="${value.id}" name="id">`;
      nama = value.nama;
      level = value.level;
      default_fee_keagenan = 'Rp ' + numberFormat(value.default_fee);
   }
   var html = `<form action="${baseUrl}Daftar_agen/proses_addupdate_level_keagenan" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id}
                                 <input type="hidden" name="level_keagenan" value="${level}" />
                                 <label>Nama Level Keagenan</label>
                                 <input type="text" name="nama_level_keagenan" value="${nama}" class="form-control form-control-sm" placeholder="Nama Level Keagenan" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Level</label>
                                 <input type="number" disabled value="${level}" class="form-control form-control-sm" placeholder="Level Keagenan" />
                              </div>
                           </div>
                           <div class="col-8">
                              <div class="form-group">
                                 <label>Default Fee Keagenan</label>
                                 <input type="text" name="default_fee_keagenan" value="${default_fee_keagenan}" class="form-control form-control-sm currency" placeholder="Default Fee Keagenan" />
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
