function template_pesan_whatsapp_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentTemplatePesanWhatsapp">
                  <span>Perangkat tidak ditemukan.</span>
               </div>
            </div>`;
}


function template_pesan_whatsapp_getData(){
   ajax_x(
      baseUrl + "Pengaturan_perangkat_whatsapp/get_info_perangkat", function(e) {
         if( e['error'] == false ){
            var html = `<div class="col-6 col-lg-8 my-3 ">
                           <button class="btn btn-default" type="button" onclick="add_template()">
                              <i class="fab fa-whatsapp"></i> Tambah Template Pesan Whatsapp
                           </button>
                           <label class="float-right py-2 my-0">Filter :</label>
                        </div>
                        <div class="col-6 col-lg-4 my-3 text-right">
                           <div class="input-group ">
                              <input class="form-control form-control-sm" type="text" onkeyup="get_template_whatsapp(20)" id="searchNamaTemplate" name="searchNamaTemplate" placeholder="Nama Template" style="font-size: 12px;">
                              <div class="input-group-append">
                                 <button class="btn btn-default" type="button" onclick="get_template_whatsapp(20)">
                                    <i class="fas fa-search"></i> Cari
                                 </button>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-12">
                           <table class="table table-hover tablebuka">
                              <thead>
                                 <tr>
                                    <th style="width:15%;">Nama Template</th>
                                    <th style="width:10%;">Jenis Pesan</th>
                                    <th style="width:30%;">Pesan</th>
                                    <th style="width:35%;">Variable</th>
                                    <th style="width:10%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_template_pesan_whatsapp">
                                 <tr>
                                    <td colspan="5">Template Pesan Whatsapp Tidak Ditemukan</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-3" >
                           <div class="row" id="pagination_template_pesan_whatsapp"></div>
                        </div>`;
            $('#contentTemplatePesanWhatsapp').html(html);            
            get_template_whatsapp(20);
         }else{
            $('#contentTemplatePesanWhatsapp').html(e['error_msg']);
         }
      },[]
   );
}

function get_template_whatsapp(perpage){
    get_data( perpage,
             { url : 'Template_pesan_whatsapp/daftar_template_pesan_whatsapp',
               pagination_id: 'pagination_template_pesan_whatsapp',
               bodyTable_id: 'body_template_pesan_whatsapp',
               fn: 'ListDaftarTemplatePesanWhatsapp',
               warning_text: '<td colspan="5">Template Pesan Whatsapp Tidak Ditemukan</td>',
               param : { search : $('#searchNamaTemplate').val() } } );
}

function ListDaftarTemplatePesanWhatsapp(JSONData){
   var json = JSON.parse( JSONData );
   var html = `<tr>
                  <td>${json.nama_template}</td>
                  <td>${json.jenis_pesan}</td>
                  <td>${json.pesan}</td>
                  <td>${json.variable}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Template" onclick="edit_template(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Template" onclick="delete_template(${json.id})" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;            
}


function delete_template(id){
   ajax_x_t2(
      baseUrl + "Template_pesan_whatsapp/delete_template", function(e) {
         if ( e['error'] == false ) {
            smile_alert(e['error_msg']);
            get_template_whatsapp(20);
         } else {
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}


function add_template(){
   $.confirm({
      columnClass: 'col-6',
      title: 'Tambah Template Pesan Whatsapp',
      theme: 'material',
      content: formaddupdate_template_pesan_whatsapp(),
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
                     get_template_whatsapp(20);
                  }
               });
            }
         }
      }
   });
}

function edit_template(id){
   ajax_x_t2(
      baseUrl + "Template_pesan_whatsapp/get_info_edit_template", function(e) {
         if ( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-6',
               title: 'Edit Template Pesan Whatsapp',
               theme: 'material',
               content: formaddupdate_template_pesan_whatsapp(JSON.stringify(e.data)),
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
                              get_template_whatsapp(20);
                           }
                        });
                     }
                  }
               }
            });
         } else {
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function formaddupdate_template_pesan_whatsapp(JSONValue){
   var id = '';
   var nama_template = '';
   var jenis_pesan = '';
   var pesan = '';
   var variable = '';
   if( JSONValue != undefined ) {
      var value = JSON.parse(JSONValue);
      id = `<input type="hidden" value="${value.id}" name="id">`;
      nama_template = value.nama_template;
      jenis_pesan = value.jenis_pesan;
      pesan = value.pesan;
      if( value.variable != '' ) {
         variable = `<div class="form-group">
                        <label>Variable</label>
                        <div class="form-control form-control-sm" style="height: auto;">
                           ${value.variable}
                        </div>
                     </div>`;
      }
   }

   var html = `<form action="${baseUrl }Template_pesan_whatsapp/add_update_template_pesan_whatsapp" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id}
                                 <label>Nama Template</label>
                                 <input type="text" name="nama_template" value="${nama_template}" class="form-control form-control-sm" placeholder="Nama Template Pesan Whatsapp" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Jenis Pesan</label>
                                 <select class="form-control form-control-sm" name="jenis_pesan" id="jenis_pesan" onChange="getVariableTemplatePesanWhatsapp()">
                                    <option value="pilih" ${jenis_pesan == 'pilih' ? 'selected' : '' }>Pilih Pesan</option>
                                    <option value="pesan_biasa" ${jenis_pesan == 'pesan_biasa' ? 'selected' : '' }>Pesan Biasa</option>
                                    <option value="semua_jamaah" ${jenis_pesan == 'semua_jamaah' ? 'selected' : '' }>Semua Jamaah</option>
                                    <option value="staff" ${jenis_pesan == 'staff' ? 'selected' : '' }>Staff</option>
                                    <option value="agen" ${jenis_pesan == 'agen' ? 'selected' : '' }>Agen</option>
                                    <option value="jamaah_paket" ${jenis_pesan == 'jamaah_paket' ? 'selected' : '' }>Jamaah Paket</option>
                                    <option value="jamaah_sudah_berangkat" ${jenis_pesan == 'jamaah_sudah_berangkat' ? 'selected' : '' }>Jamaah Sudah Berangkat</option>
                                    <option value="jamaah_tabungan_umrah" ${jenis_pesan == 'jamaah_tabungan_umrah' ? 'selected' : '' }>Jamaah Tabungan Umrah</option>
                                    <option value="jamaah_utang_koperasi" ${jenis_pesan == 'jamaah_utang_koperasi' ? 'selected' : '' }>Jamaah Utang Koperasi</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-12" id="variable_area">
                              ${variable}
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Pesan</label>
                                 <textarea class="form-control form-control-sm" name="pesan" id="pesan" rows="5" style="resize:none">${pesan}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}



function getVariableTemplatePesanWhatsapp(){
   var jenis_pesan = $('#jenis_pesan').val();
   if( jenis_pesan != 'pilih' ) {
      ajax_x_t2(
         baseUrl + "Template_pesan_whatsapp/get_variable_template_pesan_whatsapp", function(e) {
            if ( e['error'] == false ) {
               if( e.data != '' ) {
                  $('#variable_area').html( `<div class="form-group">
                                                <label>Variable</label>
                                                <div class="form-control form-control-sm" style="height: auto;">
                                                   ${e.data}
                                                </div>
                                             </div>`);
               }else{
                  $('#variable_area').html(``);
               }
            } else {
               frown_alert(e['error_msg']);
            }
         },[{jenis_pesan:jenis_pesan}]
      );
   }else{
      $('#variable_area').html('');
   }
}