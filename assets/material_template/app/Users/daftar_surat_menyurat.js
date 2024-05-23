function daftar_surat_menyurat_Pages(){
   return  `<div class="col-6 col-lg-10 my-3">
               <button class="btn btn-default" type="button" onclick="cetak_surat()">
                  <i class="fas fa-print"></i> Cetak Surat
               </button>
               <button class="btn btn-default mx-1" type="button" onclick="setting_surat()">
                  <i class="fas fa-cogs"></i> Pengaturan Surat Menyurat
               </button>
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-2 my-3">
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_surat_menyurat(20)"
                     id="searchSuratMenyurat" name="searchSuratMenyurat" placeholder="Nomor Surat" style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_daftar_surat_menyurat(20)">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:15%;">Nomor Surat</th>
                        <th style="width:15%;">Tipe Surat</th>
                        <th style="width:40%;">Info</th>
                        <th style="width:10%;">Petugas</th>
                        <th style="width:10%;">Tanggal Surat</th>
                        <th style="width:10%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_daftar_surat_menyurat">
                     <tr>
                        <td colspan="6">Daftar surat menyurat tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_daftar_surat_menyurat"></div>
            </div>`;
}

function daftar_surat_menyurat_getData(){
   get_daftar_surat_menyurat(20);
}

function get_daftar_surat_menyurat(perpage){
    get_data( perpage,
             { url : 'Daftar_surat_menyurat/server_surat_menyurat',
               pagination_id: 'pagination_daftar_surat_menyurat',
               bodyTable_id: 'bodyTable_daftar_surat_menyurat',
               fn: 'ListDaftarSuratMenyurat',
               warning_text: '<td colspan="6">Daftar surat menyurat tidak ditemukan</td>',
               param : { search : $('#searchSuratMenyurat').val() } } );
}

function ListDaftarSuratMenyurat(JSONData){
   var json = JSON.parse(JSONData);
   var info = `<table class="table table-hover my-2">
                   <tbody>`;
         if( Object.keys(json.info).length > 0 ){
            var i = 0;
            for( x in json.info ){
               info += ` <tr>
                           <td class="text-left ${ i == 0 ? `border-top` : ''}" colspan="3" style="width:30%;"><b>${x}</b></td>
                           <td class="px-0 ${ i == 0 ? `border-top` : ''}" style="width:1%;">:</td>
                           <td class="text-left ${ i == 0 ? `border-top` : ''}" colspan="3" style="width:69%;">${json.info[x]}</td>
                        </tr>`;
               i++;         
            }
         }else{
            info += `<tr><td class="text-center border-top" colspan="5"><b>Data Tidak Ditemukan</b></td></tr>`;
         }       
     info +=      `</tbody>
                </table>`;

   var html = `<tr>
                  <td>${json.nomor_surat}</td>
                  <td>${json.tipe_surat}</td>
                  <td>${info}</td>
                  <td>${json.nama_petugas}</td>
                  <td>${json.tanggal_surat}</td>
                  <td> 
                     <button type="button" class="btn btn-default btn-action" title="Cetak Riwayat Surat Menyurat"
                         onclick="cetak_riwayat_surat_menyurat('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-print" style="font-size: 11px;"></i>
                     </button>`;
         if( json.edit_status == true ) {
            html += `<button type="button" class="btn btn-default btn-action" title="Delete Surat Menyurat"
                         onclick="delete_surat_menyurat('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>`;
         }            
         html += `</td>
              <tr>`;
   return html;
}

// cetak riwayat surat menyurat
function cetak_riwayat_surat_menyurat(id){
   ajax_x(
       baseUrl + "Daftar_surat_menyurat/cetak_riwayat_surat_menyurat",
       function (e) {
         if (e["error"] == false) {
            window.open(baseUrl + "Kwitansi/", "_blank");
         } else {
           frown_alert(e["error_msg"]);
         }
       },
       [{id: id}]
   );
}

function cetak_surat(){
   ajax_x(
       baseUrl + "Daftar_surat_menyurat/check_konfigurasi_surat",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
                columnClass: "col-5",
                title: "Cetak Surat Menyurat",
                theme: "material",
                content: formCetakSuratMenyurat(),
                closeIcon: false,
                buttons: {
                  cancel: function () {
                    return true;
                  },
                  simpan: {
                    text: "Simpan",
                    btnClass: "btn-blue",
                    action: function () {
                      ajax_submit_t1("#form_utama", function (e) {
                        e["error"] == true
                          ? frown_alert(e["error_msg"])
                          : smile_alert(e["error_msg"]);
                        if (e["error"] == true) {
                          return false;
                        } else {
                           window.open(baseUrl + "Kwitansi/", "_blank");
                           get_daftar_surat_menyurat(20);
                        }
                      });
                    },
                  },
                },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
       []
   );
}

function formCetakSuratMenyurat(){
   var html = `<form action="${baseUrl}Daftar_surat_menyurat/proses_tambah_surat_menyurat" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="form-group">
                           <label>Nomor Surat</label>
                           <input type="text" name="nomor_surat" id="nomor_surat" value="" class="form-control form-control-sm" placeholder="Nomor Surat Menyurat" />
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label>Tanggal Surat</label>
                           <input type="date" name="tanggal_surat" id="tanggal_surat" value="" class="form-control form-control-sm" placeholder="Tanggal Surat Menyurat" />
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                           <label>Tujuan</label>
                           <input type="text" name="tujuan" id="tujuan" value="" class="form-control form-control-sm" placeholder="Tujuan Surat Menyurat" />
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="form-group">
                          <label>Jenis Surat</label>
                          <select class="form-control form-control-sm" name="jenis_surat" id="jenis_surat" onChange="getInfoJenisSurat()">
                            <option value="pilih">Pilih Jenis Surat</option>
                            <option value="rekom_paspor">Rekom Pembuatan Paspor</option>
                            <option value="surat_cuti">Surat Cuti</option>
                          </select>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="row" id="fluid_area">
                        </div>
                     </div>
                  </div>
               </form>`;
  return html;
}

// get info jenis surat
function getInfoJenisSurat(){
   var jenis_surat = $('#jenis_surat').val();
   if( jenis_surat == 'rekom_paspor') {
      ajax_x_t2(
          baseUrl + "Daftar_surat_menyurat/get_jamaah_surat_menyurat",
          function (e) {
            if (e.error == false) {

               var html = `<div class="col-12">
                              <div class="form-group">
                                <label>Jamaah</label>
                                <select class="form-control form-control-sm" name="jamaah" id="jamaah">`;
                        for( x in e.data ) {
                           html += `<option value="${x}">${e.data[x]}</option>`;
                        }
                  html +=      `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Bulan dan Tahun Berangkat</label>
                                 <input type="text" name="bulan_tahun_berangkat" id="bulan_tahun_berangkat" value="" class="form-control form-control-sm" placeholder="Bulan dan Tahun Berangkat" />
                              </div>
                           </div>
                           <script>
                              $("#jamaah").select2({
                                 dropdownParent: $(".jconfirm")
                              });
                           </script>`;
               $('#fluid_area').html(html);            
            } else {
              frown_alert(e["error_msg"]);
            }
          },
          []
      );
   } else if( jenis_surat == 'surat_cuti' ) {
      ajax_x_t2(
          baseUrl + "Daftar_surat_menyurat/get_jamaah_surat_menyurat",
          function (e) {
            if (e.error == false) {

               var html = `<div class="col-12">
                              <div class="form-group">
                                <label>Jamaah</label>
                                <select class="form-control form-control-sm" name="jamaah" id="jamaah">`;
                        for( x in e.data ) {
                           html += `<option value="${x}">${e.data[x]}</option>`;
                        }
                  html +=      `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Jabatan</label>
                                 <input type="text" name="jabatan" id="jabatan" value="" class="form-control form-control-sm" placeholder="Jabatan" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Keberangkatan</label>
                                 <input type="date" name="keberangkatan" id="keberangkatan" value="" class="form-control form-control-sm" placeholder="Keberangkatan" />
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Kepulangan</label>
                                 <input type="date" name="kepulangan" id="kepulangan" value="" class="form-control form-control-sm" placeholder="Kepulangan" />
                              </div>
                           </div>
                           <script>
                              $("#jamaah").select2({
                                 dropdownParent: $(".jconfirm")
                              });
                           </script>`;
               $('#fluid_area').html(html);            
            } else {
              frown_alert(e["error_msg"]);
            }
          },
          []
      );

   }else{
      $('#fluid_area').html('');
   }
}

function setting_surat(){
   ajax_x(
       baseUrl + "Daftar_surat_menyurat/get_info_konfigurasi_surat_menyurat",
       function (e) {
         if (e["error"] == false) {
            $.confirm({
                columnClass: "col-10",
                title: "Pengaturan Surat Menyurat",
                theme: "material",
                content: formUpdateSettingSuratMenyurat(JSON.stringify(e.data)),
                closeIcon: false,
                buttons: {
                  cancel: function () {
                    return true;
                  },
                  simpan: {
                    text: "Simpan",
                    btnClass: "btn-blue",
                    action: function () {
                      ajax_submit_t1("#form_utama", function (e) {
                        e["error"] == true
                          ? frown_alert(e["error_msg"])
                          : smile_alert(e["error_msg"]);
                        if (e["error"] == true) {
                          return false;
                        } else {
                          get_daftar_surat_menyurat(20);
                        }
                      });
                    },
                  },
                },
            });
         } else {
           frown_alert(e["error_msg"]);
         }
       },
       []
   );
}

function delete_surat_menyurat(id){
   ajax_x(
       baseUrl + "Daftar_surat_menyurat/delete_surat_menyurat",
       function (e) {
         if (e["error"] == false) {
            smile_alert(e["error_msg"]);
            get_daftar_surat_menyurat(20);
         } else {
           frown_alert(e["error_msg"]);
         }
       },
       [{id:id}]
   );
}

function formUpdateSettingSuratMenyurat(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl}Daftar_surat_menyurat/proses_update_setting_surat_menyurat" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-4">
                        <div class="form-group">
                           <label>Nama Pejabat</label>
                           <input type="text" name="nama_pejabat" value="${json.nama_tanda_tangan}" class="form-control form-control-sm" placeholder="Nama Pejabat" />
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                           <label>Jabatan</label>
                           <input type="text" name="jabatan" value="${json.jabatan_tanda_tangan}" class="form-control form-control-sm" placeholder="Nama Jabatan" />
                        </div>
                     </div>
                     <div class="col-5">
                        <div class="form-group">
                           <label>Alamat Pejabat</label>
                           <input type="text" name="alamat" value="${json.alamat_tanda_tangan}" class="form-control form-control-sm" placeholder="Alamat Pejabat" />
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="form-group">
                           <label>Nama Perusahaan</label>
                           <input type="text" name="nama_perusahaan" value="${json.nama_perusahaan}" class="form-control form-control-sm" placeholder="Nama Perusahaan" />
                        </div>
                     </div>
                     
                     <div class="col-3">
                        <div class="form-group">
                           <label>Kota Perusahaan</label>
                           <input type="text" name="kota_perusahaan" value="${json.kota_perusahaan}" class="form-control form-control-sm" placeholder="Kota Perusahaan" />
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                           <label>Provinsi Perusahaan</label>
                           <input type="text" name="provinsi_perusahaan" value="${json.provinsi_perusahaan}" class="form-control form-control-sm" placeholder="Provinsi Perusahaan" />
                        </div>
                     </div>

                     <div class="col-8">
                        <div class="form-group">
                           <label>Alamat Perusahaan</label>
                           <input type="text" name="alamat_perusahaan" value="${json.alamat_perusahaan}" class="form-control form-control-sm" placeholder="Alamat Perusahaan" />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>No Kontak Perusahaan</label>
                           <input type="text" name="no_kontak_perusahaan" value="${json.no_kontak_perusahaan}" class="form-control form-control-sm" placeholder="Nomor Kontak Perusahaan" />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Izin Perusahaan</label>
                           <input type="text" name="izin_perusahaan" value="${json.izin_perusahaan}" class="form-control form-control-sm" placeholder="Izin Perusahaan" />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Website Perusahaan</label>
                           <input type="text" name="website_perusahaan" value="${json.website_perusahaan}" class="form-control form-control-sm" placeholder="Website Perusahaan" />
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                           <label>Email Perusahaan</label>
                           <input type="text" name="email_perusahaan" value="${json.email_perusahaan}" class="form-control form-control-sm" placeholder="Email Perusahaan" />
                        </div>
                     </div>
                  </div>
               </form>`;
  return html;
}
