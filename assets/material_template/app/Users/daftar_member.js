function daftar_member_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarMember">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_member()">
                        <i class="fas fa-users"></i> Tambah Member
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_member(20)" id="searchDaftarMember" name="searchDaftarMember" placeholder="Nama/Nomor Identitas Member" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_member(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Photo</th>
                              <th style="width:35%;">Info Member</th>
                              <th style="width:35%;">Info Akun</th>
                              <th style="width:20%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_member">
                           <tr>
                              <td colspan="5">Daftar member tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_member"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_member_getData(){
   get_daftar_member(20);
}

function get_daftar_member(perpage){
   get_data( perpage,
             { url : 'Daftar_member/daftar_members',
               pagination_id: 'pagination_daftar_member',
               bodyTable_id: 'bodyTable_daftar_member',
               fn: 'ListDaftarMember',
               warning_text: '<td colspan="3">Daftar member tidak ditemukan</td>',
               param : { search : $('#searchDaftarMember').val() } } );
}

function ListDaftarMember(JSONData){
   var json = JSON.parse(JSONData);
   var muthawif = 0;
   var agen = 0;
   var info_member = `<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:40%;border:none;">NAMA</td>
                              <td class="text-left py-0 pt-1" style="width:60%;border:none;font-weight:bold;">${json.fullname}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="border:none;">NOMOR IDENTITAS</td>
                              <td class="text-left py-0 pt-1" style="border:none;font-weight:bold;">${json.identity_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="border:none;">JENIS KELAMIN</td>
                              <td class="text-left py-0 pt-1" style="border:none;font-weight:bold;">${json.gender}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="border:none;">TTL</td>
                              <td class="text-left py-0 pt-1" style="border:none;font-weight:bold;">${json.birth_place}, ${json.birth_date}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="border:none;">ALAMAT</td>
                              <td class="text-left py-0 pt-1" style="border:none;font-weight:bold;">${json.address}</td>
                           </tr>
                        <tbody>
                     </table>`;


      var register_as = '<ul class="pl-3 list">';
      if( Object.keys(json.register_as).length > 0 ){
         for( x in json.register_as ){
            if( json.register_as[x] == 'Muthawif'){
               muthawif = 1;
            }
            if( json.register_as[x] == 'Agen'){
               agen = 1;
            }
            register_as += `<li>${json.register_as[x].toUpperCase()}</li>`;
         }
      }else{
         register_as += `<li>-</li>`;
      }
      register_as += '</ul>';
   var info_akun =  `<table class="table table-hover">
                        <tbody>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:30%;border:none;">EMAIL</td>
                              <td class="text-left py-0 pt-1" style="width:70%;border:none;font-weight:bold;">${json.email}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:30%;border:none;">NOMOR WHATSAPP</td>
                              <td class="text-left py-0 pt-1" style="width:70%;border:none;font-weight:bold;">${json.nomor_whatsapp}</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0 pt-1" style="width:30%;border:none;">TERDAFTAR SEBAGAI</td>
                              <td class="text-left py-0 pt-1" style="width:70%;border:none;font-weight:bold;">${register_as}</td>
                           </tr>
                        <tbody>
                     </table>`;

   var html = `<tr>
                  <td>
                     <img src="${baseUrl}/image/personal/${json.photo}" class="img-fluid" alt="Photo Santri" style="border: 2px solid #c9ccd7;border-radius: 4px;width: 68px;height: 94px;">
                  </td>
                  <td>${info_member}</td>
                  <td>${info_akun}</td>
                  <td>`;
         console.log(agen);

         if( agen == 0 ) {
            html +=    `<button type="button" class="btn btn-default btn-action" title="Jadikan Agen"
                           onclick="as_agen('${json.id}')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-user-tie" style="font-size: 11px;"></i>
                        </button>`;
         }else{
            html +=    `<button type="button" class="btn btn-default btn-action disabled" title="Jadikan Agen"
                           onclick="frown_alert('Member sudah menjadi agen !!!.')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-user-tie" style="font-size: 11px;"></i>
                        </button>`;
         }

         if( muthawif == 0 ){
            html +=    `<button type="button" class="btn btn-default btn-action" title="Jadikan Muthawif"
                           onclick="as_muthawif('${json.id}')" style="margin:.15rem .1rem  !important">
                            <i class="fas fa-user-cog" style="font-size: 11px;"></i>
                        </button>`;
         }else{
            html +=    `<button type="button" class="btn btn-default btn-action disabled" title="Jadikan Muthawif"
                           onclick="frown_alert('Member sudah menjadi muthawif !!!.')" style="margin:.15rem .1rem  !important" >
                            <i class="fas fa-user-cog" style="font-size: 11px;"></i>
                        </button>`;
         }

         html +=    `<button type="button" class="btn btn-default btn-action" title="Edit Member"
                        onclick="edit_member('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Member"
                        onclick="delete_member('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function form_asAgen(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<form action="${baseUrl }Daftar_member/proses_addupdate_as_agen" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Member</label>
                                 <input type="text" value="${json.nama}" class="form-control form-control-sm" disabled />
                                 <input type="hidden" name="id" value="${json.id}">
                              </div>
                           </div>
                           <div class="col-8">
                              <div class="form-group">
                                 <label>Nomor Identitas Member</label>
                                 <input type="text" value="${json.no_identitas}" class="form-control form-control-sm" disabled />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Upline</label>
                                 <select class="form-control form-control-sm" name="upline">
                                    <option value="0">Pilih upline member</option>`;
                        for( x in json.agen ){
                           html += `<option value="${json.agen[x]['id']}">${json.agen[x]['fullname']}  (Level : ${json.agen[x]['level_agen']})</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group mb-2">
                                 <label>Level Agen</label>
                                 <select class="form-control form-control-sm" name="level_agen">`;
                        for ( y in json.level_keagenan ){
                           html += `<option value="${y}">${json.level_keagenan[y]}</option>`;
                        }
                        html += `</select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function as_agen(id){
   ajax_x(
      baseUrl + "Daftar_member/info_as_agen", function(e) {
         $.confirm({
            columnClass: 'col-4',
            title: 'Jadikan Agen',
            theme: 'material',
            content: form_asAgen(JSON.stringify(e['data'])),
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
                           get_daftar_member(20);
                        }
                     });
                  }
               }
            }
         });
      },[{id:id}]
   );
}

function as_muthawif(id){
   ajax_x(
      baseUrl + "Daftar_member/as_muthawif", function(e) {
         if( e['error'] == false ){
             get_daftar_member(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}

function delete_member(id){
   ajax_x(
      baseUrl + "Daftar_member/delete_member", function(e) {
         if( e['error'] == false ){
             get_daftar_member(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}

function edit_member(id){
   ajax_x(
      baseUrl + "Daftar_member/get_info_edit_member", function(e) {
         $.confirm({
            columnClass: 'col-8',
            title: 'Edit Member',
            theme: 'material',
            content: formaddupdate_member( JSON.stringify( e.data ), JSON.stringify( e.value ) ),
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
                        // alert
                        e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                        // filter
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_member(20);
                        }
                     });
                  }
               }

            }
         });
      },[{id:id}]
   );
}

function add_member(){
   ajax_x(
      baseUrl + "Daftar_member/get_info_member", function(e) {
         $.confirm({
            columnClass: 'col-8',
            title: 'Tambah Member',
            theme: 'material',
            content: formaddupdate_member( JSON.stringify( e.data )),
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
                        // alert
                        e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                        // filter
                        if ( e['error'] == true ) {
                           return false;
                        } else {
                           get_daftar_member(20);
                        }
                     });
                  }
               }

            }
         });
         // $.confirm({
         //    columnClass: 'col-8',
         //    title: 'Edit Member',
         //    theme: 'material',
         //    content: formaddupdate_member(JSON.stringify(e['data'])),
         //    closeIcon: false,
         //    buttons: {
         //       cancel:function () {
         //           return true;
         //       },
         //       simpan: {
         //          text: 'Simpan',
         //          btnClass: 'btn-blue',
         //          action: function () {
         //             ajax_submit_t1("#form_utama", function(e) {
         //                // alert
         //                e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
         //                // filter
         //                if ( e['error'] == true ) {
         //                   return false;
         //                } else {
         //                   get_daftar_member(20);
         //                }
         //             });
         //          }
         //       }

         //    }
         // });
      },[]
   );

   




}

function formaddupdate_member(JSONData, JSONValue){
   var json = JSON.parse( JSONData );
   var list_bank = json.list_bank;
   var id_member = '';
   var nama = '';
   var no_identitas = '';
   var gender = '';
   var tempat_lahir = '';
   var tanggal_lahir = '';
   var email = '';
   var alamat = '';
   var nomor_whatsapp = '';
   var photo = '';
   var bank_selected = '';
   var nomor_akun_bank = '';
   var nama_akun_bank = '';
   if (JSONValue != undefined) {
      var value = JSON.parse(JSONValue);
      id_member = `<input type="hidden" name="id" value="${value.id}">`;
      nama = value.nama;
      no_identitas = value.no_identitas;
      gender = value.gender;
      if( value.photo != '' ) {
         photo = `<a class="ml-3" style="color: #c4c7d2 !important;font-style: italic;float: right;" onclick="previewImage('${value.photo}')">
                     <i class="fas fa-search"></i> Preview Image
                  </a>`;
      }
      bank_selected = value.bank_id;
      nomor_akun_bank = value.number_account;
      nama_akun_bank = value.account_name;
      tempat_lahir = value.birth_place;
      tanggal_lahir = value.birth_date;
      email = value.email;
      alamat = value.alamat;
      nomor_whatsapp = value.nomor_whatsapp;
   }

   var html = `<form action="${baseUrl }Daftar_member/proses_addupdate_member" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-5">
                              <div class="form-group">
                                 <label >Upload Photo ${photo}</label>
                                 <input class="form-control form-control-sm" type="file" id="formFile" name="photo">
                                 <small class="form-text text-muted"><i>Ukuran Maximum Photo 600KB (Tipe : .jpg, .jpeg, .png)</i></small>
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 ${id_member}
                                 <label>Nama</label>
                                 <input type="text" name="nama" value="${nama}" class="form-control form-control-sm" placeholder="Nama Member" />
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Nomor Identitas</label>
                                 <input type="text" name="nomor_identitas" value="${no_identitas}" class="form-control form-control-sm" placeholder="Nomor Identitas Member" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Jenis Kelamin</label>
                                 <select class="form-control form-control-sm" name="jenis_kelamin">
                                    <option value="0" ${ gender == '0' ? 'selected' : '' }>Laki-laki</option>
                                    <option value="1" ${ gender == '1' ? 'selected' : '' }>Perempuan</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Tempat Lahir</label>
                                 <input type="text" name="tempat_lahir" value="${tempat_lahir}" class="form-control form-control-sm" placeholder="Tempat Lahir" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Tanggal Lahir</label>
                                 <input type="date" name="tanggal_lahir" value="${tanggal_lahir}" class="form-control form-control-sm" placeholder="Tanggal Lahir" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nama Akun Bank</label>
                                 <input type="text" name="nama_akun_bank" value="${nama_akun_bank}" class="form-control form-control-sm" placeholder="Nama Akun Bank" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Nomor Akun Bank</label>
                                 <input type="text" name="nomor_akun_bank" value="${nomor_akun_bank}" class="form-control form-control-sm" placeholder="Nomor Akun Bank" />
                              </div>
                           </div>
                           <div class="col-4">
                              <div class="form-group">
                                 <label>Daftar Bank</label>
                                 <select class="form-control form-control-sm" name="list_bank">`;
                  for( x in list_bank ) {
                     html += `<option value="${x}" ${ x == bank_selected ? 'selected' : '' }>${list_bank[x]}</option>`;
                  }
                  html +=       `</select>
                              </div>
                           </div>
                           
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Email</label>
                                 <input type="email" name="email" value="${email}" class="form-control form-control-sm" placeholder="Email" />
                              </div>
                              <div class="form-group">
                                 <label>Alamat</label>
                                 <textarea class="form-control form-control-sm pb-2" name="alamat" rows="6"
                                    style="resize: none;" placeholder="Alamat member" required>${alamat}</textarea>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label>Nomor Whatsapp</label>
                                 <input type="text" name="nomor_whatsapp" value="${nomor_whatsapp}" class="form-control form-control-sm" placeholder="Nomor Whatsapp" />
                                 <small class="form-text text-muted">Pastikan nomor yang terdaftar adalah nomor Whatsapp yang aktif. Nomor ini akan digunakan untuk menerima OTP.</small>
                              </div>
                              <div class="form-group">
                                 <label>Password</label>
                                 <input type="password" name="password" class="form-control form-control-sm" placeholder="Password" />
                                 <small class="form-text text-muted">Password hanya terdiri dari alpha numeric.</small>
                              </div>
                              <div class="form-group">
                                 <label>Password Konfirmasi</label>
                                 <input type="password" name="conf_password" class="form-control form-control-sm" placeholder="Password Konfirmasi" />
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>`;
   return html;
}

function previewImage(imgSrc, path ){
   if( path == undefined ){
      path = 'personal';
   }
	$.confirm({
      columnClass: 'col-3',
		title: 'Preview Image',
		theme: 'material',
		content:`<div class="content">
                  <div class="row mx-0">
                     <div class="col-lg-12">
                        <img src="${baseUrl}/image/${path}/${imgSrc}" class="img-fluid" alt="Responsive image" style="width: 100%;">
                     </div>
                  </div>
               </div>`,
		closeIcon: false,
		buttons: {
         ok:{
            text: 'Tutup',
            btnClass: 'btn-blue',
            action:  function () {
              return true;
           }
         },
  		}
	});
}
