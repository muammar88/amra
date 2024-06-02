function tipe_paket_la_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarTipePaketLA">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_tipe_paket_la()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Tipe Paket LA
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_tipe_paket_la( 20)" id="searchAllTipePaketLA" name="searchAllTipePaketLA" placeholder="Nama Tipe Paket LA" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_tipe_paket_las( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:40%;">Nama Tipe paket</th>
                              <th style="width:50%;">Info Fasilitas Tipe Paket</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_tipe_paket_la">
                           <tr>
                              <td colspan="3">Daftar tipe paket LA tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_tipe_paket_la"></div>
                  </div>
               </div>
            </div>`;
}

function tipe_paket_la_getData(){
   get_tipe_paket_la(20);
}

function get_tipe_paket_la(perpage){
   get_data( perpage,
             { url : 'Tipe_paket_la/daftar_tipe_paket_la',
               pagination_id: 'pagination_tipe_paket_la',
               bodyTable_id: 'bodyTable_tipe_paket_la',
               fn: 'ListDaftarTipePaketLA',
               warning_text: '<td colspan="3">Daftar tipe paket LA tidak ditemukan</td>',
               param : { search : $('#searchAllTipePaketLA').val() } } );
}

function ListDaftarTipePaketLA(JSONData){
   var json = JSON.parse(JSONData);
   var fasilitas = json.fasilitas;
   var html =  `<tr>
                  <td>${json.nama_tipe_paket}</td>
                  <td>
                     <ul class="list my-0">`;
            for( x in fasilitas ) {
               html += `<li>${fasilitas[x]['nama_fasilitas']} (Harga : ${kurs} ${numberFormat(fasilitas[x]['harga'])} | Pax :${fasilitas[x]['pax']}) </li>`;
            }
         html +=    `</ul>
                  </td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Tipe Paket LA"
                        onclick="edit_tipe_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Tipe Paket LA"
                        onclick="delete_tipe_paket_la('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_tipe_paket_la(){
   ajax_x(
      baseUrl + "Tipe_paket_la/get_info_tipe_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-7',
               title: 'Tambah Tipe Paket LA',
               theme: 'material',
               content: formaddupdate_tipe_paket_la(JSON.stringify(e['fasilitas'])),
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
                              type: e['error'] == true ? 'red' :'blue'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_tipe_paket_la(20);
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

function edit_tipe_paket_la(id){
   ajax_x(
      baseUrl + "Tipe_paket_la/get_info_edit_tipe_paket_la", function(e) {
         if( e['error'] == false ){
            $.confirm({
               columnClass: 'col-7',
               title: 'Tambah Tipe Paket LA',
               theme: 'material',
               content: formaddupdate_tipe_paket_la(JSON.stringify(e['fasilitas']), JSON.stringify(e['data'])),
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
                              type: e['error'] == true ? 'red' :'blue'
                           });
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_tipe_paket_la(20);
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

function delete_tipe_paket_la(id){
   ajax_x(
      baseUrl + "Tipe_paket_la/delete_tipe_paket_la", function(e) {
         if( e['error'] == false ){
            get_tipe_paket_la(20);
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


function formaddupdate_tipe_paket_la(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   var id_tipe_paket_la = '';
   var nama_tipe_paket_la = '';
   var fasilitas = {};
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_tipe_paket_la = `<input type="hidden" name="id" value="${value.id}">`;
      nama_tipe_paket_la = value.paket_type_name;
      fasilitas = value.fasilitas;
   }

   console.log(fasilitas);
   console.log(typeof(fasilitas));

   var html = `<form action="${baseUrl }Tipe_paket_la/proses_addupdate_tipe_paket_la" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${id_tipe_paket_la}
                                 <label>Nama Tipe Paket LA</label>
                                 <input type="hidden" id="jsondata" value='${JSONData}' >
                                 <input type="text" name="nama_tipe_paket_la" value="${nama_tipe_paket_la}" class="form-control form-control-sm" placeholder="Nama Tipe Paket LA" />
                              </div>
                           </div>
                        </div>
                        <div class="row" id="list_fasilitas">`;
                  if( JSONValue == undefined ) {
                     html += `<div class="col-12 my-1">
                                 <div class="row">
                                    <div class="col-9">
                                       <select class="form-control form-control-sm" name="fasilitas[]">`;
                                for( x in json ) {
                                   html += `<option value="${json[x]['id']}">${json[x]['nama_fasilitas']} (Harga :${kurs} ${numberFormat(json[x]['harga'])})</option>`;
                                }
                              html += `</select>
                                    </div>
                                    <div class="col-2">
                                       <input type="number" name="pax[]" value="" class="form-control form-control-sm" placeholder="Pax">
                                    </div>
                                    <div class="col-1 text-right">
                                       <button class="btn btn-default btn-action" title="Delete" onclick="delete_this(this)">
                                          <i class="fas fa-times" style="font-size: 11px;"></i>
                                       </button>
                                    </div>
                                 </div>
                              </div>   `;
                  } else {
                     for( y in fasilitas ) {
                        html += `<div class="col-12 my-1">
                                    <div class="row">
                                       <div class="col-9">
                                          <select class="form-control form-control-sm" name="fasilitas[]">`;
                                   for( x in json ) {
                                      html += `<option  ${ fasilitas[y]['fasilitas_id'] } value="${json[x]['id']}" ${ fasilitas[y]['fasilitas_id'] == json[x]['id'] ? 'selected' : '' }>${json[x]['nama_fasilitas']} (Harga :${kurs} ${numberFormat(json[x]['harga'])})</option>`;
                                   }
                                 html += `</select>
                                       </div>
                                       <div class="col-2">
                                          <input type="text" name="pax[]" value="${fasilitas[y]['pax']}" class="form-control form-control-sm" placeholder="Pax">
                                       </div>
                                       <div class="col-1">
                                          <button type="button" class="btn btn-default btn-action" title="Delete" onclick="delete_this(this)">
                                             <i class="fas fa-times" style="font-size: 11px;"></i>
                                          </button>
                                       </div>
                                    </div>
                                 </div>`;
                     }
                  }
               html += `</div>
                        <div class="row">
                           <div class="col-12 my-3">
                              <div class="row">
                                 <div class="col-12 text-right">
                                    <button type="button" class="btn btn-default" title="Delete" onclick="tambah_fasilitas_tipe_paket_la(this)">
                                       <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Fasilitas
                                    </button>
                                 </div>
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

function delete_this(e){
   $(e).parent().parent().parent().remove();
}

function tambah_fasilitas_tipe_paket_la(){
   var json = JSON.parse($('#jsondata').val());
   var html  = `<div class="col-12 my-1">
                  <div class="row">
                     <div class="col-9">
                        <select class="form-control form-control-sm" name="fasilitas[]">`;
                for( x in json ) {
                    html += `<option value="${json[x]['id']}">${json[x]['nama_fasilitas']} (Harga :${kurs} ${numberFormat(json[x]['harga'])})</option>`;
                }
               html += `</select>
                     </div>
                     <div class="col-2">
                        <input type="number" name="pax[]" value="" class="form-control form-control-sm" placeholder="Pax">
                     </div>
                     <div class="col-1 text-right">
                        <button class="btn btn-default btn-action" title="Delete" onclick="delete_this(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>
               </div>   `;
   $('#list_fasilitas').append(html);
}
