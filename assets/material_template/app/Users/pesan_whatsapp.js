function pesan_whatsapp_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentPesanWhatsapp">
                  <span>Perangkat tidak ditemukan.</span>
               </div>
            </div>`;
}


function pesan_whatsapp_getData(){
   ajax_x(
      baseUrl + "Pengaturan_perangkat_whatsapp/get_info_perangkat", function(e) {
         if( e['error'] == false ){
            var html = `<div class="col-6 col-lg-8 my-3 ">
                           <button class="btn btn-default" type="button" onclick="kirim_pesan_whatsapp()">
                              <i class="fab fa-whatsapp"></i> Kirim Pesan Whatsapp
                           </button>
                           <label class="float-right py-2 my-0">Filter :</label>
                        </div>
                        <div class="col-6 col-lg-4 my-3 text-right">
                           <div class="input-group ">
                              <input class="form-control form-control-sm" type="text" onkeyup="get_pesan_whatsapp(20)" id="searchNomorTujuan" name="searchNomorTujuan" placeholder="Nomor Tujuan" style="font-size: 12px;">
                              <div class="input-group-append">
                                 <button class="btn btn-default" type="button" onclick="get_pesan_whatsapp(20)">
                                    <i class="fas fa-search"></i> Cari
                                 </button>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-12">
                           <table class="table table-hover tablebuka">
                              <thead>
                                 <tr>
                                    <th style="width:10%;">Nomor Asal</th>
                                    <th style="width:10%;">Jenis Pesan</th>
                                    <th style="width:30%;">Template Pesan</th>
                                    <th style="width:15%;">Status Pesan</th>
                                    <th style="width:10%;">Total Pesan</th>
                                    <th style="width:15%;">Tanggal Pengiriman</th>
                                    <th style="width:10%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_pesan_whatsapp">
                                 <tr>
                                    <td colspan="7">Pesan Whatsapp Tidak Ditemukan</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-3" >
                           <div class="row" id="pagination_pesan_whatsapp"></div>
                        </div>`;
            $('#contentPesanWhatsapp').html(html);            
            get_pesan_whatsapp(20);
         }else{
            $('#contentPesanWhatsapp').html(e['error_msg']);
         }
      },[]
   );
}

function get_pesan_whatsapp(perpage){
   get_data( perpage,
    { url : 'Pesan_whatsapp/daftar_pesan_whatsapp',
      pagination_id: 'pagination_pesan_whatsapp',
      bodyTable_id: 'body_pesan_whatsapp',
      fn: 'ListDaftarPesanWhatsapp',
      warning_text: '<td colspan="7">Pesan Whatsapp Tidak Ditemukan</td>',
      param : { search : $('#searchNomorTujuan').val() } } );
}


function ListDaftarPesanWhatsapp(JSONData){
   var json = JSON.parse(JSONData);

   var html    = `<tr>
                     <td>${json.nomor_asal}</td>
                     <td>${json.jenis_pesan}</td>
                     <td>${json.pesan}</td>
                     <td>${json.status_pesan}</td>
                     <td>${json.total_pesan} Pesan</td>
                     <td>${json.tanggal_input}</td>
                     <td>
                        <button type="button" class="btn btn-default btn-action" title="Detail Pesan Whatsapp" onclick="detail_pesan_whatsapp(${json.id})" style="margin:.15rem .1rem  !important">
                           <i class="fas fa-th-list" style="font-size: 11px;"></i>
                        </button>
                     </td>
                 </tr>`;
   return html;
}

function detail_pesan_whatsapp(id){
   ajax_x(
      baseUrl + "Pesan_whatsapp/get_detail_pesan_whatsapp", function(e) {
         if ( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-10',
               title: 'Detail Pengiriman Pesan Whatsapp',
               theme: 'material',
               content: form_detail_pesan_whatsapp(JSON.stringify(e.data)),
               closeIcon: false,
               buttons: {
                  tutup:function () {
                       return true;
                  },
               }
            });
         } else {
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}

function form_detail_pesan_whatsapp(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<table class="table">
                  <thead>
                     <tr>
                        <th style="width:20%;"><b>Nomor Tujuan</b></th>
                        <th style="width:35%;"><b>Pesan</b></th>
                        <th style="width:20%;"><b>Status Pesan</b></th>
                        <th style="width:25%;"><b>Tanggal Terkirim</b></th>
                     </tr>
                  </thead>
                  <tbody>`;
      for( x in  json ){
         html +=`<tr>
                     <td>${json[x].nomor_tujuan}</td>
                     <td class="text-left">${json[x].pesan}</td>
                     <td>${json[x].status}</td>
                     <td>${json[x].send_at}</td>
                  </tr>`;
      }            
      html +=    `</tbody>
               </table>`;
   return html;
}

function kirim_pesan_whatsapp(){
   ajax_x_t2(
      baseUrl + "Pesan_whatsapp/get_info_kirim_pesan", function(e) {
         if ( e['error'] == false ) {
            $.confirm({
               columnClass: 'col-6',
               title: 'Kirim Pesan Whatsapp',
               theme: 'material',
               content: form_kirim_pesan_whatsapp(JSON.stringify(e.data)),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Kirim Pesan Whatsapp',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_pesan_whatsapp(20);
                           }
                        });
                     }
                  }
               }
            });
         } else {
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function form_kirim_pesan_whatsapp(JSONData){
   var json = JSON.parse(JSONData);
   var form = `<form action="${baseUrl}Pesan_whatsapp/kirimPesanWhatsapp" id="form_utama" class="formName">
                  <div class="row px-0 py-3 mx-0">
                     <div class="col-4" >
                        <div class="form-group">
                           <label>Nomor Asal</label>
                           <input type="text" placeholder="Nomor Tujuan" value="${json}" class="form-control form-control-sm" readonly/>
                        </div>
                     </div>   
                     <div class="col-8" >
                        <div class="form-group">
                           <label>Jenis Pesan</label>
                           <select class="form-control form-control-sm" name="jenis_pesan" id="jenis_pesan" onChange="getNomorTujuanAndTemplateInfo()">
                              <option value="pilih">Pilih Jenis Pesan</option>
                              <option value="pesan_biasa">Pesan Biasa</option>
                              <option value="semua_jamaah">Semua Jamaah</option>
                              <option value="staff">Staff</option>
                              <option value="agen">Agen</option>
                              <option value="jamaah_paket">Jamaah Paket</option>
                              <option value="jamaah_sudah_berangkat">Jamaah Sudah Berangkat</option>
                              <option value="jamaah_tabungan_umrah">Jamaah Tabungan Umrah</option>
                              <option value="jamaah_utang_koperasi">Jamaah Utang Koperasi</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-12" id="area_nomor_tujuan" >
                     </div>
                     <div class="col-12" >
                        <div class="form-group">
                           <label>Template Pesan</label>
                           <select class="form-control form-control-sm" name="template_pesan" id="template_pesan" onChange="get_pesan_template()">
                              <option value="pilih">Pilih Template</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-12" id="pesan_area">
                        <div class="form-group">
                           <label>Pesan</label>
                           <textarea class="form-control form-control-sm" rows="5" name="pesan" style="resize:none"></textarea>
                        </div>
                     </div>
                  </div>
               </form>`;
   return form;
}


function getNomorTujuanAndTemplateInfo(){
   var jenis_pesan = $('#jenis_pesan').val();
   if( jenis_pesan != 'pilih') {
      ajax_x_t2(
         baseUrl + "Pesan_whatsapp/get_info_nomor_tujuan_and_template_info", function(e) {
            if ( e['error'] == false ) {
               if( jenis_pesan == 'pesan_biasa' ) {
                  $('#area_nomor_tujuan').html(`<div class="form-group">
                                                   <label>Nomor Tujuan</label>
                                                   <input type="text" placeholder="Nomor Tujuan" name="nomor_tujuan" id="nomor_tujuan" class="form-control form-control-sm"/>
                                                   <small class="form-text text-muted">Silahkan masukkan nomor tujuan pada kolom diatas. Untuk nomor tujuan yang lebih dari satu, maka setiap nomor dipisahkan oleh tanda koma.</small>
                                                </div>`);
               }else {
                  var html = ``;
                  if( jenis_pesan == 'jamaah_paket' ) {
                     var list_paket = e.data.list_paket;
                     html += `<div class="form-group">
                                 <label>Jenis Paket</label>
                                 <select class="form-control form-control-sm" name="paket" id="paket" onChange="getNomorTujuanByPaket()">
                                    <option value="0">Pilih Paket</option>`;
                        for( x in list_paket ) {
                           html += `<option value="${x}" >${list_paket[x]}</option>`;
                        }
                     html +=    `</select>
                              </div>`;
                  } 
                  html += `<div class="form-group">
                              <label>Nomor Tujuan</label>
                              <div class="form-control form-control-sm" style="background-color: #e1e7ed;">
                                 <span style="color:red;font-size: 11px;font-style: italic;">Terdapat ${e.data.c_nomor_tujuan} nomor whatsapp tujuan yang akan dikirim</span>
                              </div>
                           </div>`; 
                  $('#area_nomor_tujuan').html(html);
               }
               
               var template = e.data.list_template;
               var list_template = '<option value="0">Pilih Template</option>';
               for( y in template ) {
                  list_template += `<option value="${y}">${template[y]}</option>`;
               }
               $('#template_pesan').html(list_template);
            } else {
               frown_alert(e['error_msg']);
            }
         },[{jenis_pesan: jenis_pesan}]
      );
   }else{
       $('#area_nomor_tujuan').html('');
   }
}

// count number whatsapp paket
function countNumberWhatsapp(){
   ajax_x_t2(
      baseUrl + "Pesan_whatsapp/countNumberWhatsappPaket", function(e) {
         if ( e['error'] == false ) {



         } else {
            frown_alert(e['error_msg']);
         }
      },[{status_paket : $('#status_paket')}]
   );
}

// if( jenis_pesan == 'pesan_biasa' ) {
//    $('#area_nomor_tujuan').html(`<div class="form-group">
//                                     <label>Nomor Tujuan</label>
//                                     <input type="text" placeholder="Nomor Tujuan" name="nomor_tujuan" id="nomor_tujuan" class="form-control form-control-sm"/>
//                                     <small class="form-text text-muted">Silahkan masukkan nomor tujuan pada kolom diatas. Untuk nomor tujuan yang lebih dari satu, maka setiap nomor dipisahkan oleh tanda koma.</small>
//                                  </div>`);
// }else {
//    var html = ``;
//    if( jenis_pesan == 'jamaah_paket' ) {
//       var list_paket = e.data.list_paket;
//       html += `<div class="form-group">
//                   <label>Jenis Paket</label>
//                   <select class="form-control form-control-sm" name="paket" id="paket" onChange="getNomorTujuanByPaket()">
//                      <option value="0">Pilih Paket</option>`;
//          for( x in list_paket ) {
//             html += `<option value="${x}" >${list_paket[x]}</option>`;
//          }
//       html +=    `</select>
//                </div>
//                <div class="form-group">
//                   <label>Status Paket</label>
//                   <select class="form-control form-control-sm" name="status_paket" id="status_paket">
//                      <option value="semua">Semua</option>
//                      <option value="belum_berangkat">Belum Berangkat</option>
//                      <option value="sudah_berangkat">Sudah Berangkat</option>
//                   </select>
//                </div>`;
//    } 
//    html += `<div class="form-group">
//                <label>Nomor Tujuan</label>
//                <div class="form-control form-control-sm" style="background-color: #e1e7ed;">
//                   <span style="color:red;font-size: 11px;font-style: italic;">Terdapat ${e.data.c_nomor_tujuan} nomor whatsapp tujuan yang akan dikirim</span>
//                </div>
//             </div>`; 
//    $('#area_nomor_tujuan').html(html);
// }

// var template = e.data.list_template;
// var list_template = '<option value="0">Pilih Template</option>';
// for( y in template ) {
//    list_template += `<option value="${y}">${template[y]}</option>`;
// }
// $('#template_pesan').html(list_template);




// get pesan template
function get_pesan_template(){
   var template_pesan = $('#template_pesan').val();
   if( template_pesan != 0) {
      ajax_x_t2(
         baseUrl + "Pesan_whatsapp/get_pesan_template", function(e) {
            if ( e['error'] == false ) {
               if( e.data != '' ) {
                  $('#pesan_area').html(`<div class="form-group">
                                             <label>Pesan</label>
                                             <div class="form-control form-control-sm" style="resize:none;min-height: 100px;">${ e.data }</div>
                                          </div>`);
               }else{
                  $('#pesan_area').html(`<div class="form-group">
                                             <label>Pesan</label>
                                             <textarea class="form-control form-control-sm" rows="5" name="pesan" style="resize:none"></textarea>
                                          </div>`);
               }
            } else {
               frown_alert(e['error_msg']);
            }
         },[{template_pesan: template_pesan}]
      );
   }else{
       $('#pesan_area').html(`<div class="form-group">
                                       <label>Pesan</label>
                                       <textarea class="form-control form-control-sm" rows="5" name="pesan" style="resize:none"></textarea>
                                     </div>`);
   }
}



function getNomorTujuanByPaket(){
   var selected = $('#paket').val();
   ajax_x_t2(
      baseUrl + "Pesan_whatsapp/get_nomor_tujuan_by_paket", function(e) {
         if ( e['error'] == false ) {
               var list_paket = e.data.list_paket;

               var html = `<div class="form-group">
                              <label>Jenis Pesan</label>
                              <select class="form-control form-control-sm" name="paket" id="paket" onChange="getNomorTujuanByPaket()">
                                 <option value="0">Pilih Paket</option>`;
                     for( x in list_paket ) {
                        html += `<option value="${x}" ${selected == x ? 'selected' : ''}>${list_paket[x]}</option>`;
                     }
                  html +=    `</select>
                           </div>
                           <div class="form-group">
                              <label>Nomor Tujuan</label>
                              <div class="form-control form-control-sm" style="background-color: #e1e7ed;">
                                 <span style="color:red;font-size: 11px;font-style: italic;">Terdapat ${e.data.c_nomor_tujuan} nomor whatsapp tujuan yang akan dikirim</span>
                              </div>
                           </div>`; 
               $('#area_nomor_tujuan').html(html);
         } else {
            frown_alert(e['error_msg']);
         }
      },[{paket_id: $('#paket').val()}]
   );
}