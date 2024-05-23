let editUser = () => {
   // http://localhost/atra/Users/get_info_profil
   ajax_x(
      baseUrl + "Users/get_info_profil", function(e) {
         $.confirm({
            title: 'Edit Profil ' + (e.data['level_akun'] == 'administrator' ? 'Administrator' : ''),
            theme: 'material',
            content:`<form action="${baseUrl }Users/updateUserProfil" id="form_utama" class="formName">
                        <div class="form-group mb-4">
                           <div class="justify-content-between mx-auto text-center">
                              <img src="${baseUrl}/image/${e.data['photo']}" class="img-fluid rounded-circle" alt="Responsive image" style="width: 132px;height: 132px;">
                           </div>
                        </div>
                        <div class="form-group">
                           <label>${ e.data['level_akun'] == 'administrator' ? 'Upload Photo Administrator' : 'Upload Photo Pengguna' }</label>
                           <input type="file" name="photo" placeholder="Photo Pengguna" class="photo_pengguna form-control form-control-sm" />
                           <small id="emailHelp" class="form-text text-muted"><i>Ukuran Maximum Photo 200KB (Tipe : .jpg, .jpeg, .png)</i></small>
                        </div>
                        <div class="form-group">
                           <label>${ e.data['level_akun'] == 'administrator' ? 'Nama Perusahaan' : 'Nama Pengguna' }</label>
                           <input type="text" name="name" placeholder="${ e.data['level_akun'] == 'administrator' ? 'Nama Perusahaan' : 'Nama Pengguna' }" class="fullname form-control form-control-sm" value="${e.data['name']}" required />
                        </div>
                        ${ e.data.level_akun != 'staff' ?
                        `<div class="form-group">
                           <label>Email</label>
                           <input type="text" name="email" placeholder="Email" class="username form-control form-control-sm" value="${e.data.email}" required />
                        </div>` : `` }
                        <div class="form-group">
                           <label>Password</label>
                           <input type="password" name="password" placeholder="Password" class="password form-control form-control-sm"/>
                        </div>
                        <div class="form-group">
                           <label>Konfirmasi Password</label>
                           <input type="password" name="conf_password" placeholder="Konfirmasi Password" class="password form-control form-control-sm" />
                        </div>
                     </form>`,
            closeIcon: false,
            buttons: {
               cancel: function () {
                    return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-blue',
                  action: function () {
                     ajax_submit_t1("#form_utama", function(e) {
                        $.alert({
                           title: 'Peringatan',
                           content: e['error_msg'],
                           type: e['error'] == true ? 'red' :'green',
                        });
                        if ( e['error'] == true ) {
                           window.location.href = baseUrl + "Users/Sign_in";
                           return false;
                        } else {
                           window.location.href = baseUrl + "Users?company_code=" + e['company_code'];
                        }
                     });
                  }
               }
            }
         });
      },[]
   );
}
