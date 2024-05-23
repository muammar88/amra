function daftar_jamaah_Pages(){
   return  `<div class="col-6 col-lg-8 my-3">
               <button class="btn btn-default" type="button" onclick="add_jamaah()">
                  <i class="fas fa-user-plus"></i> Tambah Jamaah
               </button>
               <button class="btn btn-default mx-1" type="button" onclick="download_all_jamaah_to_excel()">
                  <i class="fas fa-download"></i> Download Excel Daftar Jamaah
               </button>
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-4 my-3">
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_jamaah( 20 )"
                     id="searchDaftarJamaah" name="searchDaftarJamaah" placeholder="Nomor Identitas/Nama Jamaah"
                     style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_daftar_jamaah( 20 )">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:12%;">Nomor Identitas</th>
                        <th style="width:23%;">Nama Jamaah</th>
                        <th style="width:15%;">Tempat/Tanggal Lahir</th>
                        <th style="width:10%;">Nomor Passport</th>
                        <th style="width:10%;">Nama Agen</th>
                        <th style="width:10%;">Jumlah Pembelian Paket</th>
                        <th style="width:10%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_daftar_jamaah">
                     <tr>
                        <td colspan="6">Daftar jamaah tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_daftar_jamaah"></div>
            </div>`;
}

function daftar_jamaah_getData(){
   get_daftar_jamaah( 20 );
}

function get_daftar_jamaah(perpage){
   get_data( perpage,
             { url : 'Trans_paket/daftar_jamaah_trans_paket',
               pagination_id: 'pagination_daftar_jamaah',
               bodyTable_id: 'bodyTable_daftar_jamaah',
               fn: 'ListDaftarJamaah',
               warning_text: '<td colspan="6">Daftar jamaah tidak ditemukan</td>',
               param : { search : $('#searchDaftarJamaah').val() } } );
}

function ListDaftarJamaah(JSONData){
   var json = JSON.parse(JSONData);
   return  `<tr>
               <td>${json["nomor_identitas"]}</td>
               <td>${json["fullname"]}</td>
               <td>${json["tempat_lahir"]} / ${json["tanggal_lahir"]}</td>
               <td>${json["nomor_passport"]}</td>
               <td>${json["nama_agen"]!= null ? json["nama_agen"] : '-'}</td>
               <td>${json["total_pembelian"]}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Edit Jamaah"
                     onClick="editJamaah('${json["id"]}', 'jamaah')">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Jamaah"
                     onClick="deleteJamaah('${json["id"]}')">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}


function getKabupaten(){
   var provinsi =  $('#provinsi :selected').val();
   var list_kabupaten_kota = {0:'-- Pilih Kabupaten / Kota --'};
   var list_kecamatan = {0:'-- Pilih Kecamatan --'};
   var list_kelurahan = {0:'-- Pilih Kelurahan --'};
   if( provinsi != 0 ){
       ajax_x(
         baseUrl + "Trans_paket/get_kabupaten_kota", function(e) {
            $('#kabupaten_kota').parent().replaceWith(simpleSelectForm('Kabupaten/Kota', 'kabupaten_kota', JSON.stringify(e['kabupaten_kota']), 'onChange="getKecamatan()"', 0, 'py-1'));
            $('#kecamatan').parent().replaceWith(simpleSelectForm('Kecamatan', 'kecamatan', JSON.stringify(list_kecamatan), 'onChange="getKelurahan()"', 0, 'py-1'));
            $('#kelurahan').parent().replaceWith(simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(list_kelurahan), '', 0, 'py-1'));
         },[{provinsi:provinsi}]
      );
   }else{
      $('#kabupaten_kota').parent().replaceWith(simpleSelectForm('Kabupaten/Kota', 'kabupaten_kota', JSON.stringify(list_kabupaten_kota), 'onChange="getKecamatan()"', 0, 'py-1'));
      $('#kecamatan').parent().replaceWith(simpleSelectForm('Kecamatan', 'kecamatan', JSON.stringify(list_kecamatan), 'onChange="getKelurahan()"', 0, 'py-1'));
      $('#kelurahan').parent().replaceWith(simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(list_kelurahan), '', 0, 'py-1'));
   }
}

function getKecamatan(){
   var kabupaten_kota =  $('#kabupaten_kota :selected').val();
   var provinsi =  $('#provinsi :selected').val();
   var list_kecamatan = {0:'-- Pilih Kecamatan --'};
   var list_kelurahan = {0:'-- Pilih Kelurahan --'};
   if( kabupaten_kota != 0 ){
       ajax_x(
         baseUrl + "Trans_paket/get_kecamatan", function(e) {
            $('#kecamatan').parent().replaceWith(simpleSelectForm('Kecamatan', 'kecamatan', JSON.stringify(e['kecamatan']), 'onChange="getKelurahan()"', 0, 'py-1'));
            $('#kelurahan').parent().replaceWith(simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(list_kelurahan), '', 0, 'py-1'));
         },[{provinsi:provinsi, kabupaten_kota:kabupaten_kota}]
      );
   }else{
      $('#kecamatan').parent().replaceWith(simpleSelectForm('Kecamatan', 'kecamatan', JSON.stringify(list_kecamatan), 'onChange="getKelurahan()"', 0, 'py-1'));
      $('#kelurahan').parent().replaceWith(simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(list_kelurahan), '', 0, 'py-1'));
   }
}

function getKelurahan(){
   var kecamatan =  $('#kecamatan :selected').val();
   var list_kelurahan = {0:'-- Pilih Kelurahan --'};
   if( kabupaten_kota != 0 ) {
       ajax_x(
         baseUrl + "Trans_paket/get_kelurahan", function(e) {
            $('#kelurahan').parent().replaceWith(simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(e['kelurahan']), '', 0, 'py-1'));
         },[{kecamatan:kecamatan}]
      );
   }else{
      $('#kelurahan').parent().replaceWith(simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(list_kelurahan), '', 0, 'py-1'));
   }
}






function form_add_update_jamaah(JSONData, JSONValue){

   var value = JSON.parse(JSONValue);
   var e = JSON.parse(JSONData);
   var personal_id = '';
   var address = '';
   var alamat_keluarga = '';
   var birth_date = '';
   var birth_place = '';
   var blood_type = '';
   var pekerjaan = '';
   var jenis_identitas = '';
   var kewarganegaraan = '';
   var title = '';
   var provinsi_selected = '';
   var kabupaten_kota_selected = '';
   var kecamatan_selected = '';
   var kelurahan_selected = '';
   var departing_from = '';
   var desease = '';
   var email = '';
   var father_name = '';
   var agen = '';
   var fullname = '';
   var nama_pasport= '';
   var gender = '';
   var hajj_experience = '';
   var hajj_year = '';
   var identity_number = '';
   var jamaah_id = '';
   var keterangan = '';
   var last_education = '';
   var mahramStatus = '';
   var nama_keluarga = '';
   var passport_dateissue = '';
   var passport_number = '';
   var passport_place = '';
   var photo = '';
   var pos_code = '';
   var profession_intance_address = '';
   var profession_intance_name = '';
   var profession_intance_telephone = '';
   var status_nikah = '';
   var tanggal_nikah = '';
   var telephone = '';
   var telephone_keluarga = '';
   var umrah_experience = '';
   var umrah_year = '';
   var validity_period = '';
   var nomor_whatsapp = '';
   var checkBoxKelengkapanVal = {};
   
   if( Object.keys(value).length > 0 ){
      nama_pasport = value.nama_pasport;
      address = value.address;
      alamat_keluarga = value.alamat_keluarga;
      birth_date = value.birth_date;
      birth_place = value.birth_place;
      blood_type = value.blood_type;
      jenis_identitas = value.jenis_identitas;
      kewarganegaraan = value.kewarganegaraan;
      title = value.title;
      departing_from = value.departing_from;
      desease = value.desease;
      email = value.email;
      father_name = value.father_name;
      fullname = value.fullname;
      gender = value.gender;
      hajj_experience = value.hajj_experience;
      hajj_year = value.hajj_year;
      identity_number = value.identity_number;
      jamaah_id = value.jamaah_id;
      keterangan = value.keterangan;
      last_education = value.last_education;
      mahramStatus = value.mahramStatus;
      nama_keluarga = value.nama_keluarga;
      agen = value.agen_id;
      pekerjaan = value.pekerjaan;
      passport_dateissue = value.passport_dateissue;
      passport_number = value.passport_number;
      passport_place = value.passport_place;
      photo = value.photo;
      pos_code = value.pos_code;
      profession_intance_address = value.profession_instantion_address;
      profession_intance_name = value.profession_instantion_name;
      profession_intance_telephone = value.profession_instantion_telephone;

      provinsi_selected = value.provinsi_id;
      kabupaten_kota_selected = value.kabupaten_kota_id;
      kecamatan_selected = value.kecamatan_id;
      kelurahan_selected = value.kelurahan_id;

      status_nikah = value.status_nikah;
      tanggal_nikah = value.tanggal_nikah;
      telephone = value.telephone;
      telephone_keluarga = value.telephone_keluarga;
      umrah_experience = value.umrah_experience;
      umrah_year = value.umrah_year;
      validity_period = value.validity_period;
      nomor_whatsapp = value.nomor_whatsapp;
      personal_id = value.personal_id;
      checkBoxKelengkapanVal = { akte_lahir: value.akte_lahir,
                                 photo_3_4: value.photo_3_4,
                                 photo_4_6: value.photo_4_6,
                                 fc_kk: value.fc_kk,
                                 fc_ktp:value.fc_ktp,
                                 fc_passport: value.fc_passport,
                                 buku_kuning: value.buku_kuning,
                                 buku_nikah: value.buku_nikah};
   }

   var html   =  `<div class="col-lg-12 mt-0 pt-0" >
                     <form class="py-2" action="${baseUrl }Trans_paket/add_update_jamaah" id="form_utama" onsubmit="addupdate_jamaah(event)">
                        <div class="row mb-3">
                           <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                              <div class="row">
                                 <div class="col-12">
                                    <span class="float-left mt-2 alern_add_jamaah" style="color:red;font-style:italic;font-size: 11px;"></span>
                                    <button type="button" class="btn btn-default" onclick="menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul')">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row" id="formAreas">
                           <div class="col-12 col-lg-1">
                              ${simpleSelectForm('Title', 'title', JSON.stringify(e['title']), '', title, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">`;
                     if( jamaah_id != ''){
                        html += `<input type="hidden" name="jamaah_id" id="jamaah_id" value="${jamaah_id}">`;
                     }
                     if(personal_id != ''){
                        html += `<input type="hidden" name="personal_id" id="personal_id" value="${personal_id}">`;
                     }
                  html +=    `${inputTextForm('Nama Jamaah', 'nama_jamaah', fullname, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Nama Pasport', 'nama_pasport', nama_pasport, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Nomor Identitas', 'no_identitas', identity_number, 'onKeyup="checkingPersonal(this)"', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-1">
                              ${simpleSelectForm('Jenis Identitas', 'jenis_identitas', JSON.stringify(e['jenis_identitas']), '', jenis_identitas, 'py-1')}
                           </div>
                           
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Kewarganegaraan', 'kewarganegaraan', JSON.stringify(e['kewarganegaraan']), '', kewarganegaraan, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Jenis Kelamin', 'jenis_kelamin', JSON.stringify(e['gender']), '', gender, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Golongan Darah', 'golongan_darah', JSON.stringify(e['golongan_darah']), '', blood_type, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Tempat Lahir', 'tempat_lahir', birth_place, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputDateForm('Tanggal Lahir', 'tanggal_lahir', birth_date, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Alamat', 'alamat', address, '', '<span class="red">*</span>')}
                           </div>
                           <div class="col-12 col-lg-1">
                              ${inputTextForm('Kode Post', 'kode_pos', pos_code, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Telephone', 'telephone', telephone, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Email', 'email', email, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Nomor Passport', 'nomor_passport', passport_number, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Tempat Dikeluarkan', 'tempat_dikeluarkan', passport_place, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputDateForm('Tanggal Dikeluarkan Passport', 'tanggal_dikeluarkan', passport_dateissue, 'col-12 col-lg-3', '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputDateForm('Masa Berlaku', 'masa_berlaku', validity_period, 'col-12 col-lg-2', '', '')}
                           </div>
                           ${formAddMahramPaket(JSON.stringify(e['jamaah']), JSON.stringify(e['status_mahram']), JSON.stringify(mahramStatus))}
                           <div class="col-12 col-lg-6">
                              <div class="row">
                                  <div class="col-12 col-lg-6">
                                    ${inputTextForm('Nama Ayah Kandung', 'nama_ayah', father_name, '', '')}
                                 </div>
                                 <div class="col-12 col-lg-6">
                                    ${inputTextForm('Nama Keluarga', 'nama_keluarga', nama_keluarga, '', '')}
                                 </div>
                                 <div class="col-12 col-lg-4">
                                    ${inputTextForm('Telephone Keluarga', 'telephone_keluarga', telephone_keluarga, '', '')}
                                 </div>
                                 <div class="col-12 col-lg-8">
                                    ${textAreaFrom('Alamat Keluarga', 'alamat_keluarga', alamat_keluarga, 'Alamat Keluarga', 'rows="3"')}
                                 </div>
                              </div>
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Provinsi', 'provinsi', JSON.stringify(e['provinsi']), 'onChange="getKabupaten()" ', provinsi_selected, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Kabupaten/Kota', 'kabupaten_kota', JSON.stringify(e['kabupaten_kota']), 'onChange="getKecamatan()"', kabupaten_kota_selected, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Kecamatan', 'kecamatan', JSON.stringify(e['kecamatan']), 'onChange="getKelurahan()"', kecamatan_selected, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(e['kelurahan']), '', kelurahan_selected, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Status Nikah', 'status_nikah', JSON.stringify(e['status_nikah']), '', status_nikah, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputDateForm('Tanggal Nikah', 'tanggal_nikah', tanggal_nikah, 'col-12 col-lg-2', '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Pengalaman Haji', 'pengalaman_haji', JSON.stringify(e['pengalaman_haji_umrah']), '', hajj_experience, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputYearForm('Tahun Haji', 'tahun_haji', hajj_year, 'col-12 col-lg-3', '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Pengalaman Umrah', 'pengalaman_umrah', JSON.stringify(e['pengalaman_haji_umrah']), '', umrah_experience, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputYearForm('Tahun Umrah', 'tahun_umrah', umrah_year, 'col-12 col-lg-3', '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Berangkat Dari', 'berangkat_dari', departing_from, '', '')}
                           </div>
                            <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Pekerjaan', 'pekerjaan', JSON.stringify(e['pekerjaan']), '', pekerjaan, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-6">
                              ${inputTextForm('Alamat Instansi Pekerjaan', 'alamat_instansi', profession_intance_address, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${inputTextForm('Nama Instansi Pekerjaan', 'nama_instansi', profession_intance_name, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Telephone  Pekerjaan', 'telephone_instansi', profession_intance_telephone, '', '')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${simpleSelectForm('Pendidikan Terakhir', 'pendidikan_terakhir', JSON.stringify(e['pendidikan']), '', last_education, 'py-1')}
                           </div>
                           <div class="col-12 col-lg-2">
                              ${inputTextForm('Penyakit', 'penyakit', desease, '', '')}
                           </div>
                           <div class="col-12 col-lg-3">
                              ${simpleSelectForm('Agen', 'agen', JSON.stringify(e['list_agen']), '', agen, 'py-1')}
                           </div>
                           <div class="col-6" >
                              <div class="row" >
                                 <label for="staticEmail" class="col-sm-12 col-form-label pb-0">Ambil Photo Jamaah</label>
                                 <div class="col-6 mt-3 text-center" id="my_camera" ></div>
                                 <div class="col-6 mt-3 text-center" id="results" style="padding-top: 0rem!important;height: 206px;">`;
                        if( photo != '' ) {
                           html +=    `<img src="${baseUrl + 'image/' + photo}" class="img-fluid" alt="Responsive image" style="max-height: 179px;">`;
                        } else {
                           // <div class="py-5" style="width:100%;height:100%;background-color: #e6e6e6;" >
                           //                <div class="mx-auto my-4 py-2 text-center" style="font-weight: 700;color: #909090;">HASIL PHOTO</div>
                           //             </div>
                           html +=    `<div class="py-5" style="width: 100%;height: 231px;background-color: #e6e6e6;">
                                          <div class="mx-auto my-5 py-2 text-center" style="font-weight: 700;color: #909090;">
                                             HASIL PHOTO
                                          </div>
                                       </div>`;
                        }
                        html += `</div>
                                 <div class="col-6" >
                                    <button type="button" class="btn btn-primary" onclick="take_snapshot()" style="width:100%">Ambil Gambar</button>
                                 </div>
                                 <div class="col-6" >
                                    <button type="button" class="btn btn-danger" onclick="delete_snapshot()" style="width:100%">Hapus Gambar</button>
                                 </div>
                              </div>
                           </div>
                           <div class="col-6 pt-3" >
                              <div class="row" >
                                 ${checkBoxKelengkapan('Kelengkapan','col-lg-6', checkBoxKelengkapanVal )}
                                 <div class="col-lg-6">
                                    ${textAreaFrom('Keterangan', 'keterangan', keterangan, 'Keterangan')}
                                 </div>
                                 <div class="col-12 col-lg-8">
                                    ${inputTextForm('Nomor Whatsapp', 'nomor_whatsapp', nomor_whatsapp, 'onKeyup="checkNomorWA()"', '')}
                                 </div>
                                 <div class="col-lg-4" id="warning">
                                 </div>
                                 <div class="col-6" >
                                    ${inputPasswordForm('Password Aplikasi', 'password', 'Password Aplikasi', '')}
                                 </div>
                                 <div class="col-6" >
                                    ${inputPasswordForm('Password Konfirmasi Aplikasi', 'confirm_password', 'Password Konfirmasi', '')}
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row mt-3">
                           <div class="col-12 col-12 p-2 text-right" style="background-color: #e9ecef;">
                              <div class="row">
                                 <div class="col-12">
                                    <span class="float-left mt-2 alern_add_jamaah" style="color:red;font-style:italic;font-size: 11px;"></span>
                                    <button type="button" class="btn btn-default" onclick="menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul')">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
                  <script>
                     Webcam.set({ width: 308, height: 233, image_format: 'jpeg', jpeg_quality: 90});
                     Webcam.attach( '#my_camera' );
                     $('#my_camera').children( "div" ).addClass( "doting" );
                  </script>`;
   return html;
}


function addupdate_jamaah(e){
   e.preventDefault();
   var nomor_whatsapp = $('#nomor_whatsapp').val();
   var password = $('#password').val();
   if( ('#id').length == 0 ){
      if( (nomor_whatsapp != '' && password == '') ||  (nomor_whatsapp == '' && password != '') ){
         if( nomor_whatsapp == '' ){
            var msg = 'Untuk menambahkan akun, nomor_whatsapp tidak boleh kosong';
         }else{
            var msg = 'Untuk menambahkan akun, password tidak boleh kosong';
         }
         frown_alert(msg);
      }else{
         ajax_submit_base64(e, "#form_utama", function(e) {
            if( e['error'] != true ){
               menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
            }
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         });
      }
   }else{
      if( nomor_whatsapp == '' && password != '' ){
         frown_alert('Untuk menambahkan akun, nomor_whatsapp tidak boleh kosong');
      }else{
         ajax_submit_base64(e, "#form_utama", function(e) {
            if( e['error'] != true ){
               menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
            }
            e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         });
      }
   }
}

// add jamaah
function add_jamaah(){
   $.confirm({
      columnClass: 'col-6',
      title: 'Pilih aksi tambah jamaah',
      theme: 'material',
      type: 'blue',
      content: `Silahkan pilih salah satu metode tambah jamaah?`,
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         tambah_jamaah: {
            text: 'Tambah Jamaah Baru',
            btnClass: 'btn-blue',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/info_jamaah", function(e) {
                     html = form_add_update_jamaah(JSON.stringify(e['data']),JSON.stringify([]));
                     $('#content_daftar_jamaah').html(html);
                  },[]
               );
            }
         },
         tambah_jamaah_dari_member: {
            text: 'Tambah Jamaah Dari Member',
            btnClass: 'btn-blue',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/get_member_not_jamaah", function(e) {
                     $.confirm({
                        columnClass: 'col-5',
                        title: 'Pilih member',
                        theme: 'material',
                        content: formListMember(JSON.stringify(e.data)),
                        closeIcon: false,
                        buttons: {
                           cancel: function () {
                                return true;
                           },
                           ya: {
                              text: 'Pilih Member',
                              btnClass: 'btn-blue',
                              action: function () {
                                 if( $('#member_id').val() == 0 ){
                                    frown_alert('Untuk melanjutkan, anda wajib memilih salah satu member.');
                                    return false;
                                 }else{
                                    ajax_x(
                                       baseUrl + "Trans_paket/info_jamaah_by_member", function(e) {
                                          html = form_add_jamaah_by_member(JSON.stringify(e['data']),JSON.stringify([]));
                                          $('#content_daftar_jamaah').html(html);
                                       },[{member_id: $('#member_id').val() }]
                                    );
                                 }
                              }
                           }
                        }
                     });
                  },[]
               );
            }
         }
      }
   });
}

// http://malemdiwa.com/amra/image/personal/default.png

function form_add_jamaah_by_member(JSONData, JSONValue){
      
      var json = JSON.parse(JSONData);

      var title = '';
      var kewarganegaraan = '';
      var gender = '';
      var blood_type = '';
      var pos_code = '';
      var telephone = '';
      var passport_number = '';
      var passport_place = '';
      var passport_dateissue = '';
      var jenis_identitas = '';
      var validity_period = '';
      var nama_pasport = '';
      var mahramStatus = '';
      var father_name = '';
      var nama_keluarga = '';
      var telephone_keluarga = '';
      var alamat_keluarga = '';
      var provinsi_selected = '';
      var kabupaten_kota_selected = '';
      var kecamatan_selected = '';
      var kelurahan_selected = '';
      var status_nikah = '';
      var tanggal_nikah = '';
      var hajj_experience = '';
      var hajj_year = '';
      var umrah_experience = '';
      var umrah_year = '';
      var departing_from = '';
      var pekerjaan = '';
      var profession_intance_address = '';
      var profession_intance_name = '';
      var profession_intance_telephone = '';
      var last_education = '';
      var desease = '';
      var agen = '';
      var photo = '';
      var checkBoxKelengkapanVal = {};
      var keterangan = '';

      var html = `<div class="col-lg-12 mt-0 pt-0" >
                     <form class="py-2" action="${baseUrl }Trans_paket/add_update_jamaah_by_member" id="form_utama" onsubmit="addupdate_jamaah_member(event)">
                        <div class="row mb-3">
                           <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                              <div class="row">
                                 <div class="col-12">
                                    <span class="float-left mt-2" style="color:red;font-style:italic;font-size: 11px;" id="alern_add_jamaah"></span>
                                    <button type="button" class="btn btn-default mx-2" onclick="menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul')">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div id="formAreas">
                           <input type="hidden" name="personal_id" value="${json.member_info.personal_id}">
                           <div class="row " >
                              <div class="col-7 py-3 px-2" style="border-radius: 4px;background-color: #f7f7f7;">
                                 <div class="row" >
                                    <div class="col-12 mb-3 text-center ">
                                       <div class="row px-3" >
                                          <div class="col-12 py-2 title-box-jamaah" style="background-color:#e9ecef;">
                                             <label class="my-0">Info Member</label>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-lg-2 text-center pl-3">
                                       <img src="${baseUrl + 'image/personal/' + json.member_info.photo}" class="img-fluid" alt="Photo Santri" style="border: 2px solid #c9ccd7;border-radius: 4px;">
                                    </div>
                                    <div class="col-12 col-lg-10 pr-3">
                                       <div class="row" > 
                                          <div class="col-12 col-lg-4">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Nama Lengkap</label>
                                                <div class="col-sm-12">
                                                   <input type="text" placeholder="Nama Jamaah" class="form-control form-control-sm" value="${json.member_info.fullname}" readonly>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-12 col-lg-3">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Nomor Identitas</label>
                                                <div class="col-sm-12">
                                                   <input type="text" class="form-control form-control-sm" value="${json.member_info.identity_number}" readonly>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-12 col-lg-2">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Jenis Kelamin</label>
                                                <div class="col-sm-12">
                                                   <input type="text" class="form-control form-control-sm" value="${json.member_info.gender == '1' ? 'Perempuan' : 'Laki-laki'}" readonly>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-12 col-lg-3">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Tanggal Lahir</label>
                                                <div class="col-sm-12">
                                                   <input type="text" class="form-control form-control-sm" value="${json.member_info.birth_date }" readonly>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-12 col-lg-5">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Tempat Lahir</label>
                                                <div class="col-sm-12">
                                                   <input type="text" class="form-control form-control-sm" value="${json.member_info.birth_place}" readonly>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-12 col-lg-4">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Email</label>
                                                <div class="col-sm-12">
                                                   <input type="text" class="form-control form-control-sm" value="${json.member_info.email}" readonly>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-12 col-lg-3">
                                             <div class="form-group form-group-input row">
                                                <label class="col-sm-12 col-form-label">Nomor Whatsapp</label>
                                                <div class="col-sm-12">
                                                   <input type="text" class="form-control form-control-sm" value="${json.member_info.nomor_whatsapp}" readonly>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-5 py-0 px-2">
                                 <div class="row h-100 pl-3"> 
                                    <div class="col-12 p-3 h-100" style="background-color: #e9ecef;">
                                       <div class="row px-2" >
                                          <div class="col-12 py-2 title-box-jamaah text-center" style="background-color:white;">
                                             <label class="my-0">Catatan</label>
                                          </div>
                                       </div>
                                       <ol class="px-3">
                                           <li>
                                               <p class="mb-0" style="text-align:left;font-style:italic;">Sebelum menyimpan data jamaah, pastikan semua data penting sudah anda isi.</p>
                                           </li>
                                           <li>
                                               <p class="mb-0" style="text-align:left;font-style:italic;">Pastikan anda mengisi nomor whatsapp dengan nomor yang masih aktif.</p>
                                           </li>
                                           <li>
                                               <p class="mb-0" style="text-align:left;font-style:italic;">Jika jamaah memiliki agen, anda wajib memilih agen terlebih dahulu sebelum melakukan transaksi. Jika tidak mengisi nama agen, maka transaksi yang sudah anda lakukan tidak menambahkan fee kepada Agen.</p>
                                           </li>
                                       </ol>
                                    </div>   
                                 </div>
                              </div>
                              <div class="col-12 py-3 my-3 px-3" style="border: 1px solid #d8d8d9;border-radius: 4px;">
                                 <div class="col-12 py-2 mb-3 text-center " style="background-color:#e9ecef;">
                                    <label class="my-0">Info Jamaah</label>
                                 </div>
                                 <div class="row">
                                    <div class="col-12 col-lg-1">
                                       ${simpleSelectForm('Title', 'title', JSON.stringify(json.title), '', title, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-3">
                                       ${inputTextForm('Nama Pasport', 'nama_pasport', nama_pasport, '', '')}
                                    </div>
                                    <div class="col-12 col-lg-1">
                                       ${simpleSelectForm('Jenis Identitas', 'jenis_identitas', JSON.stringify(json.jenis_identitas), '', jenis_identitas, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-1">
                                       ${simpleSelectForm('Kewarganegaraan', 'kewarganegaraan', JSON.stringify(json.kewarganegaraan), '', kewarganegaraan, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${simpleSelectForm('Golongan Darah', 'golongan_darah', JSON.stringify(json.golongan_darah), '', blood_type, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputTextForm('Kode Post', 'kode_pos', pos_code, '', '')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputTextForm('Telephone', 'telephone', telephone, '', '')}
                                    </div>
                                    ${formAddMahramPaket(JSON.stringify(json.jamaah), JSON.stringify(json.status_mahram), JSON.stringify(mahramStatus))}
                                    <div class="col-12 col-lg-6">
                                       <div class="row">
                                           <div class="col-12 col-lg-6">
                                             ${inputTextForm('Nama Ayah Kandung', 'nama_ayah', father_name, '', '')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${inputTextForm('Nama Keluarga', 'nama_keluarga', nama_keluarga, '', '')}
                                          </div>
                                          <div class="col-12 col-lg-4">
                                             ${inputTextForm('Telephone Keluarga', 'telephone_keluarga', telephone_keluarga, '', '')}
                                          </div>
                                          <div class="col-12 col-lg-8 px-3">
                                             ${textAreaFrom('Alamat Keluarga', 'alamat_keluarga', alamat_keluarga, 'Alamat Keluarga', 'rows="3"')}
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                       <div class="row">
                                          <div class="col-12 col-lg-6">
                                             ${inputTextForm('Nomor Passport', 'nomor_passport', passport_number, '', '')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${inputTextForm('Tempat Dikeluarkan', 'tempat_dikeluarkan', passport_place, '', '')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${inputDateForm('Tanggal Dikeluarkan Passport', 'tanggal_dikeluarkan', passport_dateissue, 'col-12 col-lg-3', '', '')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${inputDateForm('Masa Berlaku', 'masa_berlaku', validity_period, 'col-12 col-lg-2', '', '')}
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                       <div class="row">
                                          <div class="col-12 col-lg-6">
                                             ${simpleSelectForm('Provinsi', 'provinsi', JSON.stringify(json.provinsi), 'onChange="getKabupaten()" ', provinsi_selected, 'py-1')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${simpleSelectForm('Kabupaten/Kota', 'kabupaten_kota', JSON.stringify(json.kabupaten_kota), 'onChange="getKecamatan()"', kabupaten_kota_selected, 'py-1')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${simpleSelectForm('Kecamatan', 'kecamatan', JSON.stringify(json.kecamatan), 'onChange="getKelurahan()"', kecamatan_selected, 'py-1')}
                                          </div>
                                          <div class="col-12 col-lg-6">
                                             ${simpleSelectForm('Kelurahan', 'kelurahan', JSON.stringify(json.kelurahan), '', kelurahan_selected, 'py-1')}
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${simpleSelectForm('Status Nikah', 'status_nikah', JSON.stringify(json.status_nikah), '', status_nikah, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputDateForm('Tanggal Nikah', 'tanggal_nikah', tanggal_nikah, 'col-12 col-lg-2', '', '')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${simpleSelectForm('Pengalaman Haji', 'pengalaman_haji', JSON.stringify(json.pengalaman_haji_umrah), '', hajj_experience, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputYearForm('Tahun Haji', 'tahun_haji', hajj_year, 'col-12 col-lg-3', '', '')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${simpleSelectForm('Pengalaman Umrah', 'pengalaman_umrah', JSON.stringify(json.pengalaman_haji_umrah), '', umrah_experience, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputYearForm('Tahun Umrah', 'tahun_umrah', umrah_year, 'col-12 col-lg-3', '', '')}
                                    </div>
                                    <div class="col-12 col-lg-3">
                                       ${inputTextForm('Berangkat Dari', 'berangkat_dari', departing_from, '', '')}
                                    </div>
                                     <div class="col-12 col-lg-3">
                                       ${simpleSelectForm('Pekerjaan', 'pekerjaan', JSON.stringify(json.pekerjaan), '', pekerjaan, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-6">
                                       ${inputTextForm('Alamat Instansi Pekerjaan', 'alamat_instansi', profession_intance_address, '', '')}
                                    </div>
                                    <div class="col-12 col-lg-3">
                                       ${inputTextForm('Nama Instansi Pekerjaan', 'nama_instansi', profession_intance_name, '', '')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputTextForm('Telephone  Pekerjaan', 'telephone_instansi', profession_intance_telephone, '', '')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${simpleSelectForm('Pendidikan Terakhir', 'pendidikan_terakhir', JSON.stringify(json.pendidikan), '', last_education, 'py-1')}
                                    </div>
                                    <div class="col-12 col-lg-2">
                                       ${inputTextForm('Penyakit', 'penyakit', desease, '', '')}
                                    </div>
                                    <div class="col-12 col-lg-3">
                                       ${simpleSelectForm('Agen', 'agen', JSON.stringify(json.list_agen), '', agen, 'py-1')}
                                    </div>
                                    <div class="col-6" >
                                       <div class="row" >
                                          <label for="staticEmail" class="col-sm-12 col-form-label pb-0">Ambil Photo Jamaah</label>
                                          <div class="col-6 text-center pt-3" id="my_camera" ></div>
                                          <div class="col-6 text-center pt-3" id="results" style="padding-top: 0rem!important;height: 206px;">`;
                                 if( json.member_info.photo != ''  ) {
                                    if ( json.member_info.photo != 'default.png' ){
                                       html +=    `<img src="${baseUrl + 'image/personal/' + json.member_info.photo}" class="img-fluid" alt="Responsive image" >`;
                                    }else{
                                       html += `<div class="py-5" style="width: 100%;height: 231px;background-color: #e6e6e6;" >
                                                   <div class="mx-auto my-5 py-2 text-center" style="font-weight: 700;color: #909090;">HASIL PHOTO</div>
                                                </div>`;   
                                    }
                                 } else {
                                    html +=    `<div class="py-5" style="width: 100%;height: 231px;background-color: #e6e6e6;" >
                                                   <div class="mx-auto my-5 py-2 text-center" style="font-weight: 700;color: #909090;">HASIL PHOTO</div>
                                                </div>`;
                                 }
                                 html += `</div>
                                          <div class="col-6" >
                                             <button type="button" class="btn btn-primary" onclick="take_snapshot()" style="width:100%">Ambil Gambar</button>
                                          </div>
                                          <div class="col-6" >
                                             <button type="button" class="btn btn-danger" onclick="delete_snapshot()" style="width:100%">Hapus Gambar</button>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-6" >
                                       <div class="row" >
                                          ${checkBoxKelengkapan('Kelengkapan','col-lg-6', checkBoxKelengkapanVal )}
                                          <div class="col-lg-6 px-3">
                                             ${textAreaFrom('Keterangan', 'keterangan', keterangan, 'Keterangan')}
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row mb-3">
                           <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                              <div class="row">
                                 <div class="col-12">
                                    <span class="float-left mt-2" style="color:red;font-style:italic;font-size: 11px;" id="alern_add_jamaah"></span>
                                    <button type="button" class="btn btn-default mx-2" onclick="menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul')">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                 </div>
                              </div>
                           </div>
                        </div>   
                     </form>
                  </div>
                  <script>
                     Webcam.set({ width: 308, height: 233, image_format: 'jpeg', jpeg_quality: 90});
                     Webcam.attach( '#my_camera' );
                     $('#my_camera').children( "div" ).addClass( "doting" );
                  </script>`;
   return html;               
}


function addupdate_jamaah_member(e){
   e.preventDefault();
   ajax_submit_base64(e, "#form_utama", function(e) {
      if( e['error'] != true ){
         menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
      }
      e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
   });
}

// var nomor_whatsapp = $('#nomor_whatsapp').val();
// var password = $('#password').val();
// if( ('#id').length == 0 ){
//    if( (nomor_whatsapp != '' && password == '') ||  (nomor_whatsapp == '' && password != '') ){
//       if( nomor_whatsapp == '' ){
//          var msg = 'Untuk menambahkan akun, nomor_whatsapp tidak boleh kosong';
//       }else{
//          var msg = 'Untuk menambahkan akun, password tidak boleh kosong';
//       }
//       frown_alert(msg);
//    }else{
//       ajax_submit_base64(e, "#form_utama", function(e) {
//          if( e['error'] != true ){
//             menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
//          }
//          e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
//       });
//    }
// }else{
//    if( nomor_whatsapp == '' && password != '' ){
//       frown_alert('Untuk menambahkan akun, nomor_whatsapp tidak boleh kosong');
//    }else{
      
//    }
// }

// ajax_x(
//    baseUrl + "Trans_paket/delete_jamaah", function(e) {
//       e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
//       if( e['error'] != true ){
//         navBtn(this, 'trans_paket_daftar_jamaah', 'Daftar Jamaah', '')
//       }
//    },[{id:id}]
// );

function formListMember(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl}Deposit_paket/proses_addupdate_deposit_paket"
                     id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Daftar Member</label>
                                 <select class="form-control form-control-sm" name="member_id" id="member_id">
                                    <option value="0"> Pilih Member </option>`;
                           for (x in json) {
                              html += `<option value="${x}" >
                                          ${json[x]}
                                       </option>`;
                           }
                        html += `</select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <script>
                  $("#member_id").select2({
                     dropdownParent: $(".jconfirm")
                  });
               </script>`;
  return html;

}


// ajax_x(
//    baseUrl + "Trans_paket/delete_jamaah", function(e) {
//       // e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
//       // if( e['error'] != true ){
//       //   navBtn(this, 'trans_paket_daftar_jamaah', 'Daftar Jamaah', '')
//       // }
//    },[]
// );

function editJamaah(id){
   ajax_x(
      baseUrl + "Trans_paket/edit_jamaah", function(e) {
         html = form_add_update_jamaah(JSON.stringify(e['data']),JSON.stringify(e['value']));
         $('#content_daftar_jamaah').html(html);
      },[{jamaah_id:id}]
   );
}

function deleteJamaah(id){
   $.confirm({
      columnClass: 'col-7',
      title: 'Peringatan penghapus data jamaah',
      theme: 'material',
      type: 'green',
      content: `Jika anda menghapus jamaah, berarti anda juga akan:
               <ul class="mb-0">
                  <li><b>Menghapus</b> jamaah dari daftar paket yang terdaftar.</li>
                  <li><b>Menghapus</b> jamaah dari daftar mahram.</li>
                  <li><b>Menghapus</b> riwayat handover barang jamaah dari database.</li>
                  <li><b>Menghapus</b> riwayat handover fasilitas jamaah dari database.</li>
               </ul>
               Apakah anda ingin melanjutkan proses penghapusan?`,
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         ya: {
            text: 'Iya',
            btnClass: 'btn-red',
            action: function () {
               ajax_x(
                  baseUrl + "Trans_paket/delete_jamaah", function(e) {
                     e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                     if( e['error'] != true ){
                       menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
                     }
                  },[{id:id}]
               );
            }
         }
      }
   });
}

// download_all_jamaah_to_excel
function download_all_jamaah_to_excel(){
   ajax_x_t2(
      baseUrl + "Trans_paket/download_all_jamaah_to_excel",
      function(e) {
         if ( e['error'] == false ) {
            window.open(baseUrl + "Download/", "_blank");
         } else {
            frown_alert(e['error_msg'])
         }
      },
      []
   );
}
//  ajax_x(
//    baseUrl + "Trans_paket/download_all_jamaah_to_excel", function(e) {
//       // e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
//       // if( e['error'] != true ){
//       //   menu( this, 'daftar_jamaah', 'Keagenan & Jamaah', 'fas fa-users', '', 'submodul');
//       // }
//    },[]
// );
