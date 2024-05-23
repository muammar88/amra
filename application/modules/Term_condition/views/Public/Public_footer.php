<!-- jQuery -->
<script src="<?php echo base_url('assets/material_template/plugins/jquery/jquery.min.js'); ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url('assets/material_template/plugins/jquery-ui/jquery-ui.min.js'); ?> "></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
   $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('assets/material_template/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?> "></script>
<!-- ChartJS -->
<script src="<?php echo base_url('assets/material_template/plugins/chart.js/Chart.min.js'); ?> "></script>
<!-- Sparkline -->
<script src="<?php echo base_url('assets/material_template/plugins/sparklines/sparkline.js'); ?> "></script>
<!-- JQVMap -->
<script src="<?php echo base_url('assets/material_template/plugins/jqvmap/jquery.vmap.min.js'); ?> "></script>
<script src="<?php echo base_url('assets/material_template/plugins/jqvmap/maps/jquery.vmap.usa.js'); ?> "></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo base_url('assets/material_template/plugins/jquery-knob/jquery.knob.min.js'); ?> "></script>
<!-- daterangepicker -->
<script src="<?php echo base_url('assets/material_template/plugins/moment/moment.min.js'); ?> "></script>
<script src="<?php echo base_url('assets/material_template/plugins/daterangepicker/daterangepicker.js'); ?> "></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo base_url('assets/material_template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js'); ?> "></script>
<!-- Summernote -->
<script src="<?php echo base_url('assets/material_template/plugins/summernote/summernote-bs4.min.js'); ?> "></script>
<!-- overlayScrollbars -->
<script src="<?php echo base_url('assets/material_template/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?> "></script>
<!-- Currency JS -->
<script src="<?php echo base_url('assets/material_template/plugins/jquery_currency/jquery_currency.js'); ?> "></script>
<!-- File Input JS -->
<script src="<?php echo base_url('assets/material_template/plugins/fileinput/js/fileinput.js'); ?> "></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/material_template/dist/js/adminlte.js'); ?> "></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo base_url('assets/material_template/dist/js/pages/dashboard.js'); ?> "></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url('assets/material_template/dist/js/demo.js'); ?> "></script>
<!-- Select2 -->
<script src="<?php echo base_url('assets/material_template/plugins/select2/select2.min.js'); ?> "></script>
<!-- OWL js -->
<script src="<?php echo base_url('assets/material_template/plugins/owl/owl.carousel.min.js'); ?>"></script>
<!--  SweetAlert -->
<script src="<?php echo base_url('assets/material_template/plugins/sweetalert/sweetalert.min.js'); ?>"></script>
<!-- LightBox JS-->
<script src="<?php echo base_url('assets/material_template/plugins/lightbox/js/uikit.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/material_template/plugins/lightbox/js/uikit-icons.min.js'); ?>"></script>
<!-- Pagination JS -->
<script src="<?php echo base_url('assets/material_template/plugins/pagination/pagination.min.js'); ?>"></script>
<!-- Jquery Confirm -->
<script src="<?php echo base_url('assets/material_template/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.js'); ?>"></script>
<!-- myFrames JS-->
<script src="<?php echo base_url('assets/material_template/plugins/myFrame/myFrames.js'); ?>"></script>
<!-- Particle JS -->
<script src="<?php echo base_url('assets/material_template/plugins/particle/particles.min.js'); ?>"></script>
<!-- Fullcalendar -->
<script src="<?php echo base_url(); ?>assets/material_template/plugins/fullcalendar/js/fullcalendar.min.js"></script>
<!-- Webcam JS -->
<script src="<?php echo base_url('assets/material_template/plugins/webcamjs/webcam.min.js'); ?>"></script>
<?php
if (isset($foto)) {
   echo '<script type="text/javascript"> var cFoto = parseInt("' . count($foto) . '"); </script>';
} else {
   echo '<script type="text/javascript"> var cFoto = parseInt(1); </script>';
}
if (isset($js)) :
   if (is_array($js)) :
      foreach ($js as $js_key => $js_value) :
         echo '<script src="' . base_url("assets/material_template/app/") . $js_value . '.js"></script>';
      endforeach;
   else :
      echo '<script src="' . base_url("assets/material_template/app/") . $js . '.js"></script>';
   endif;
endif;
echo '<script type="text/javascript">
            var csrfs = "' . $this->security->get_csrf_hash() . '";

         </script>';
?>
</body>
<script type="text/javascript">
   $(document).ready(function() {

      localStorage.setItem('csrfName', '<?php echo  $this->security->get_csrf_token_name(); ?>');

      var csrfName = localStorage.getItem("csrfName");
      var csrfHash = localStorage.getItem("csrfHash");
      localStorage.setItem('csrfHash', csrfs);
      csrfName = localStorage.getItem("csrfName");
      csrfHash = localStorage.getItem("csrfHash");
   });

   function filterUsename() {
      var level_akun = $('#level_akun').val();
      var html = '';
      if (level_akun == 'administrator') {
         html += `<div class="col-12">
                     <div class="row px-3">
                        <label class="mb-1">
                           <h6 class="mb-0 text-sm">Email</h6>
                        </label>
                        <input class="mb-4" type="text" name="email" placeholder="Email">
                     </div>
                  </div>`;
      } else {
        var code = $('#code').val();

        if( $('#code').length == 0 ) {
          html += `<div class="col-12">
                      <div class="row px-3">
                         <label class="mb-1">
                            <h6 class="mb-0 text-sm">Kode Perusahaan</h6>
                         </label>
                         <input class="mb-4" type="text" name="kode_perusahaan" id="kode_perusahaan" placeholder="Kode Perusahaan">
                      </div>
                   </div>`;
        }
         html += `<div class="col-12">
                     <div class="row px-3">
                        <label class="mb-1">
                           <h6 class="mb-0 text-sm">Token</h6>
                        </label>
                        <div class="input-group mb-3">
                           <input type="text" name="token" maxlength="6" class="form-control mt-0 rounded-0" placeholder="Token" aria-label="Token" aria-describedby="button-addon2">
                           <button class="btn btn-blue" type="button" id="button-addon2" onClick="get_token_staff()" >Get Token</button>
                           <small id="emailHelp" class="form-text text-muted"><i>Pesan Token OTP akan dikirimkan ke nomor Whatsapp anda.</i></small>
                        </div>

                     </div>
                  </div>
                  <div class="col-12">
                     <div class="row px-3">
                        <label class="mb-1">
                           <h6 class="mb-0 text-sm">Nomor Whatsapp</h6>
                        </label>
                        <input class="mb-4" type="text" name="nomor_whatsapp" id="nomor_whatsapp" placeholder="Nomor Whatsapp">
                     </div>
                  </div>`;
      }
      $('#filter').html(html);
   }

   let checkValue = (value, arr) => {
      var status = false;
      for (var i = 0; i < arr.length; i++) {
         var name = arr[i];
         if (name == value) {
            status = true;
            break;
         }
      }
      return status;
   }
   $(document).on('collapsed.lte.pushmenu', function() {
      $('#infoNameblock').attr("style", "display: none !important;");
      $('#fa-times').fadeIn(500);
      $('#fa-bars').attr("style", "display: none !important;");
   }).on('shown.lte.pushmenu', function() {
      $('#infoNameblock').delay(300).fadeIn(500);
      $('#fa-bars').fadeIn(500);
      $('#fa-times').attr("style", "display: none !important;");
   });
   $("#particles-js").mouseenter(function() {
      if ($(".sidebar-collapse")[0]) {
         $('#infoNameblock').delay(300).fadeIn(500);
      }
   }).mouseleave(function() {
      if ($(".sidebar-collapse")[0]) {
         $('#infoNameblock').delay(300).attr("style", "display: none !important;");
      }
   });
   let baseUrl = '<?php echo base_url(); ?>';
   let assets = 'assets/';
   let js = assets + 'js/';
   let custom = js + 'custom/';
   let plugins = assets + 'plugins/';
   let switchery = plugins + 'switchery/';
   let datatables = plugins + 'datatables/';
   // directory FN
   let jsFN = (fILE) => baseUrl + js + fILE;
   let customFN = (fILE) => baseUrl + custom + fILE;
   let pluginsFN = (fILE) => baseUrl + plugins + fILE;
   let switcheryFN = (fILE) => baseUrl + switchery + fILE;
   let datatablesFN = (fILE) => baseUrl + datatables + fILE;
   let loadJQuery = (uRLs) => {
      uRL = JSON.parse(uRLs);
      for (x in uRL) {
         var script = document.createElement('script');
         script.src = uRL[x];
         script.type = 'text/javascript';
         document.getElementsByTagName('script')[0].parentNode.appendChild(script);
      }
   }
   let trTable = (label, value, icon) => {
      return `<tr class="py-2">
                  <td style="width:10%"><i class="${icon}"></i></td>
                  <td style="width:55%">${label}</td>
                  <td style="width:35%"><b>${value}</b></td>
               </tr>`;
   }
   // menu function
   let menu = (e, fn, mdlName, mdlIcon, state) => {
      Webcam.reset();
      if (state == undefined) {
         $(".nav-link").removeClass("nav-active");
         $(e).addClass("nav-active");
      }
      $("#position").hide().html(positionForm(mdlIcon, mdlName)).fadeIn(500);
      $("#breadcrumb").hide().html(breadcumForm(fn == mdlName.toLowerCase() ? JSON.stringify([mdlName]) : JSON.stringify([mdlName, capitalizeFirstLetter(fn)]))).fadeIn(500);
      if (FnCk(fn + 'Controllers') == true) {
         FnCk(fn + 'Views');
      }
   }

   function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
   }
   $(document).ready(function() {
      menu(this, 'beranda', 'Beranda', 'fas fa-home', 'true');
   });
   // checking function funct is exist
   let FnCk = (fn) => {
      if (eval("typeof " + fn) === 'function') {
         window[fn]();
         return true;
      } else {
         console.log(fn + ' Function Notfound !!!');
         return false;
      }
   }
   // tab navigation
   let Navigation = (dataJSON) => {
      Webcam.reset();
      let nav = JSON.parse(dataJSON);
      let feedBack = '';
      let i = 0;
      for (x in nav) {
         feedBack += `<li class="nav-item mx-0 ml-lg-0 mr-lg-1 px-1 px-lg-0 " data-target="#sliderContent" data-slide-to="${i}">
                     <a class="nav-link nav-insert ${i == 0 ? 'active' : ''}" data-toggle="tab" href="#${i}" ${ nav[x][2] != '' ? ' onClick="'+nav[x][2]+'()"' : ''}>
                       <i class="${nav[x][0]}" style="font-size: 11px;"></i>
                       <span class="d-none d-sm-none d-md-none d-lg-inline-block d-md-none">${nav[x][1]}</span>
                     </a>
                   </li>`;
         i++;
      }
      return feedBack;
   }
   // slider views
   let Slider = (activities, JSONdata) => {
      let slider = JSON.parse(activities);
      let data = JSON.parse(JSONdata);
      let i = 0;
      let feedBack = `<div class="col-12">
                           <div id="sliderContent" class="carousel slide" data-ride="carousel" data-interval="false" style="min-height: 73vh;height: 100%;font-size: 12px;">
                              <div class="carousel-inner">`;
      for (x in data) {
         feedBack += `<div class="carousel-item ${i == 0 ? 'active' : ''}">${data[x]}</div>`;
         menu
         i++;
      }
      feedBack += `</div>
                           </div>
                        </div>`;
      return feedBack;
   }

   let development_alert = () => {
      $.alert({
         icon: 'far fa-smile',
         title: 'Peringatan',
         content: 'Fitur ini masih dalam pengembangan.',
         type: 'blue',
      });
   }

   let smile_alert = (text) => {
      $.alert({
         icon: 'far fa-smile',
         title: 'Peringatan',
         content: text,
         type: 'blue',
      });
   }

   let frown_alert = (text) => {
      $.alert({
         icon: 'far fa-frown',
         title: 'Peringatan',
         content: text,
         type: 'red',
      });
   }

   function loadPage(viewString) {
      $('#fluid-content').hide().html(viewString).fadeIn(500);
   }

   function logout() {
      ajax_x(
         baseUrl + "Auth/logout",
         function(e) {
            if (e['error'] == false) {
               window.location.href = baseUrl + "Auth";
            }
         },
         []
      );
   }
   Object.size = function(obj) {
      var size = 0,
         key;
      for (key in obj) {
         if (obj.hasOwnProperty(key)) size++;
      }
      return size;
   };
   jQuery(window).load(function() {
      $('.carousel').carousel('pause');
   });
   $(document).on("keyup", ".currency", function(e) {
      var e = window.event || e;
      var keyUnicode = e.charCode || e.keyCode;
      if (e !== undefined) {
         switch (keyUnicode) {
            case 16:
               break;
            case 27:
               this.value = '';
               break;
            case 35:
               break;
            case 36:
               break;
            case 37:
               break;
            case 38:
               break;
            case 39:
               break;
            case 40:
               break;
            case 78:
               break;
            case 110:
               break;
            case 190:
               break;
            default:
               $(this).formatCurrency({
                  colorize: true,
                  negativeFormat: '-%s%n',
                  roundToDecimalPlace: -1,
                  eventOnDecimalsEntered: true
               });
         }
      }
   });
   $(document).on("ready", ".js-example-basic-single", function(e) {
      $(e).select2();
   });
   $(document).ready(function() {
      $('.js-example-basic-single').select2();
   });
   var prevScrollpos = window.pageYOffset;
   window.onscroll = function() {
      if ($(window).width() >= 767) {
         var currentScrollPos = window.pageYOffset;
         if (prevScrollpos > currentScrollPos) {
            $(".navbar").css({
               height: '64px',
               top: '30px'
            });
         } else {
            $(".navbar").css({
               height: '54px',
               top: '30px'
            });
         }
         prevScrollpos = currentScrollPos;
      }
   }
   $(document).scroll(function() {
      var scrollDistance = $(this).scrollTop();
      if (scrollDistance > 100) {
         $('.scroll-to-top').fadeIn();
      } else {
         $('.scroll-to-top').fadeOut();
      }
   });
   $(document).on('click', 'a.scroll-to-top', function(event) {
      var $anchor = $(this);
      $("html, body").animate({
         scrollTop: 0
      }, "slow");
      return false;
   });

   function renew_subscribtion(){
      ajax_x(baseUrl + 'Users/Renew_subscribtion/renew', function(a) {
         window.location.href = baseUrl + 'Users/Payment?code=' + a['data'];
      }, [{ code: $('#code').val(), duration: $('#duration').val() }]);
   }

   function editUser() {
      ajax_x(baseUrl + 'Admin/getInfoProfil/', function(a) {
         $.confirm({
            title: 'Edit Profil',
            theme: 'material',
            content: `<form action="${baseUrl }Admin/updateUserProfil" id="form_utama" class="formName">
                        <div class="form-group mb-4">
                           <div class="justify-content-between mx-auto text-center">
                              <img src="${baseUrl}/image/personal/${a.data['photo']}" class="img-fluid rounded-circle" alt="Responsive image" style="width: 132px;height: 132px;">
                           </div>
                        </div>
                        <div class="form-group">
                           <label>Upload Photo Pengguna</label>
                           <input type="file" name="photo" placeholder="Photo Pengguna" class="photo_pengguna form-control form-control-sm" />
                           <small id="emailHelp" class="form-text text-muted"><i>Ukuran Maximum Photo 200KB (Tipe : .jpg, .jpeg, .png)</i></small>
                        </div>
                        <div class="form-group">
                           <label>Nama Pengguna</label>
                           <input type="text" name="fullname" placeholder="Nama Pengguna" class="fullname form-control form-control-sm" value="${a.data['fullname']}" required />
                        </div>${ a.data.level_akun != 'staff' ?
                                `<div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" placeholder="Username" class="username form-control form-control-sm" value="${a.data['username']}" required />
                                 </div>` : ``}
                        <div class="form-group">
                           <label>Password</label>
                           <input type="password" name="password" placeholder="Password" class="password form-control form-control-sm" value="" required />
                        </div>
                     </form>`,
            closeIcon: false,
            buttons: {
               cancel: function() {
                  return true;
               },
               formSubmit: {
                  text: 'Simpan',
                  btnClass: 'btn-green',
                  action: function() {
                     ajax_submit_t1("#form_utama", function(e) {
                        $.alert({
                           title: 'Peringatan',
                           content: e['error_msg'],
                           type: e['error'] == true ? 'red' : 'green',
                        });
                        if (e['error'] == true) {
                           return false;
                        } else {
                           window.location.href = baseUrl + "Admin";
                        }
                     });
                  }
               }
            }
         });
      }, []);
   }
   firstLoad();
</script>

</html>
