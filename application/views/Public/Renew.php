<style>
/* body{
   font-family: 'Rubik', sans-serif !important;
}
h3, h4, h5, h6 {
    font-family: 'Rubik', sans-serif !important;
} */


#mainNav {
  padding-top: 1rem;
  padding-bottom: 1rem;
  font-family: "Rubik", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji",
    "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  font-weight: bold;
  box-shadow: 7px -11px 16px 9px #8b8b8b59;
}
#mainNav .navbar-brand {
  color: #fff;
}
#mainNav .navbar-nav {
  margin-top: 1rem;
}
#mainNav .navbar-nav li.nav-item a.nav-link {
  color: #5e5e5e;
}
#mainNav .navbar-nav li.nav-item a.nav-link:hover {
  color: #dde2e1;
}
#mainNav .navbar-nav li.nav-item a.nav-link:active,
#mainNav .navbar-nav li.nav-item a.nav-link:focus {
  color: #fff;
}
#mainNav .navbar-nav li.nav-item a.nav-link.active {
  color: #1a4e7b;
}
#mainNav .navbar-toggler {
  font-size: 80%;
  padding: 0.8rem;
}

@media (min-width: 992px) {
  #mainNav {
    padding-top: 0.6rem;
    padding-bottom: 0.6rem;
    transition: padding-top 0.3s, padding-bottom 0.3s;
    font-size: 14px;
  }
  #mainNav .navbar-brand {
    font-size: 1.75em;
    transition: font-size 0.3s;
  }
  #mainNav .navbar-nav {
    margin-top: 0;
  }
  #mainNav .navbar-nav > li.nav-item > a.nav-link.active {
    color: #fff;
    background: #22659e;
  }
  #mainNav .navbar-nav > li.nav-item > a.nav-link.active:active,
  #mainNav .navbar-nav > li.nav-item > a.nav-link.active:focus,
  #mainNav .navbar-nav > li.nav-item > a.nav-link.active:hover {
    color: #fff;
    background: #22659e;
  }

  #mainNav.navbar-shrink {
    padding-top: 0.2rem;
    padding-bottom: 0.2rem;
  }
  #mainNav.navbar-shrink .navbar-brand {
    font-size: 1.5em;
  }
}

.bg-secondary {
    background-color: white !important;
}

.btn-blue{
   background-color: #033047 !important;
   width: 150px;
   color: #fff;
   border-radius: 2px;
}


.harga-formatted {
  display: inline-block;
  color: #007bd2;
  font-weight: 700;
  margin: 0 auto;
  text-align: left;
}

.harga-formatted .kurs {
  font-size: 30px;
  position: relative;
  display: block;
  float: left;
  line-height: 30px;
  margin-right: 2px;
}

.harga-formatted .harga {
  font-size: 70px;
  position: relative;
  line-height: 70px;
  display: block;
  float: left;
  width: auto;
  top: -7px;
}

.harga-formatted .ribuan {
  display: block;
  float: left;
  width: 50px;
}

.harga-formatted .ribuan i:first-child {
  display: block;
  font-style: normal;
  font-size: 27px;
  line-height: 30px;
}

.harga-formatted .ribuan i:last-child {
  display: block;
  font-style: normal;
}

.display-harga {
  display: inline-block;
  width: 100%;
  text-align: center;
  margin: 15px 0 0 0;
}

.card-header {
  padding: 0.75rem 1.25rem;
  margin-bottom: 0;
  background-color: rgb(55 118 172);
  border-bottom: 0.125rem solid rgba(0, 0, 0, 0.125);
  color: white;
}
.card-header:first-child {
  border-radius: 0.375rem 0.375rem 0 0;
}

.card-title-2{
   min-height: 24px;
    font-weight: bold;
    text-align: center !important;
    color: white;
    font-size: 20px;
}

.card-mod{
   width:100%;
   /* max-width: 7rem; */
    height: 120px;
    box-shadow: 0 4px 8px rgb(119 119 119 / 10%), 0 12px 20px rgb(119 119 119 / 20%) !important;
    /* background-color: rgb(55 118 172); */
    background: rgb(55,118,172);
    background: linear-gradient(292deg, rgba(55,118,172,1) 35%, rgba(0,212,255,1) 100%);
    color: white;
}

.actived{
   border: 4px solid #3776ac !important;
   background: linear-gradient(292deg, rgba(55,118,172,1) 35%, rgb(104 161 211) 100%);
   box-shadow: 0 4px 8px rgb(0 0 0 / 10%), 0 12px 20px rgb(55 118 172) !important;
}

.card-subtitle-to{
   color: white;
   font-weight: bold;
   font-size: 15px;
   text-align: center;
}

.card-subsubtitle{
   color: white;
   font-weight: normal;
   font-size: 15px;
   text-align: center;
}
#imagebrandheader {
    position: fixed;
    top: 17px;
    transition: top 0.3s;
}

#titleMalemdiwa {
    display: none;
    transition: display 1s;
    text-shadow: 2px 2px 8px #22659e;
    position: relative;
    left: 61px;
}

.navbar-brand > img {
    width: 6.1rem;
    transition: width 0.3s;
}
</style>

<body class="backgrounds">

   <div class="wrap" style="background-color: #1aa4b800;background-image: linear-gradient(141deg, #cacaca 0%, #fffffff2 75%) !important;min-height:100vh;">
      <nav class="navbar navbar-expand-lg bg-secondary fixed-top" id="mainNav" style="height: 70px;">
         <div class="container">
              <a class="navbar-brand js-scroll-trigger" href="<?= base_url() ?>">
                  <img id="imagebrandheader" src="<?= base_url(); ?>image/logo_amra.png" alt="">
                  <span id="titleMalemdiwa" class="hvr-grow">Malemdiwa</span>
              </a>
              <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" style="border: 1px solid white;box-shadow: 1px 6px 5px #23639a;">
                  Menu
                  <i class="fas fa-bars"></i>
              </button>
         </div>
      </nav>
      <div class="container py-5 mt-5">
         <div class="row justify-content-center">
            <div class="col-4 mx-auto" >
               <div class="card border-0 rounded-0" style="width: 100%;">
                 <div class="card-body">
                   <h5 class="card-title mb-4" style="font-size: 20px;"><b>PAKET BERLANGGANAN</b></h5>
                   <div class="row">
                      <div class="col-12">
                         <h6 class="card-subtitle mb-3 text-muted">Silahkan pilih paket berlangganan anda.</h6>
                      </div>
                      <div class="col-6">
                        <div onClick="durasi_berlangganan(this,'1')" class="card card-mod border-0 align-top  border-secondary mb-3 d-inline-block mx-0 actived" style="box-shadow: 0 4px 8px rgb(119 119 119 / 10%), 0 12px 20px rgb(119 119 119 / 20%) !important;" >
                           <div class="card-body py-3 px-2">
                              <h5 class="card-title-2 mb-2" >1 Bulan</h5>
                              <h6 class="my-0 card-subtitle card-subtitle-to mb-2">Rp 100.000,00/bln</h6>
                              <h6 class="my-0 card-subsubtitle">Rp 100.000,00</h6>
                           </div>
                       </div>
                      </div>
                      <div class="col-6">
                        <div onClick="durasi_berlangganan(this,'3')" class="card card-mod border-0 align-top  border-secondary mb-3 d-inline-block mx-0" style="box-shadow: 0 4px 8px rgb(119 119 119 / 10%), 0 12px 20px rgb(119 119 119 / 20%) !important;" >
                           <div class="card-body py-3 px-2">
                              <h5 class="card-title-2 mb-2" >3 Bulan</h5>
                              <h6 class="my-0 card-subtitle card-subtitle-to mb-2">Rp 100.000,00/bln</h6>
                              <h6 class="my-0 card-subsubtitle">Rp 300.000,00</h6>
                           </div>
                       </div>
                      </div>
                      <div class="col-6">
                        <div onClick="durasi_berlangganan(this,'6')" class="card card-mod border-0 align-top  border-secondary mb-3 d-inline-block mx-0" style="box-shadow: 0 4px 8px rgb(119 119 119 / 10%), 0 12px 20px rgb(119 119 119 / 20%) !important;" >
                           <div class="card-body py-3 px-2">
                              <h5 class="card-title-2 mb-2" >6 Bulan</h5>
                              <h6 class="my-0 card-subtitle card-subtitle-to mb-2">Rp 100.000,00/bln</h6>
                              <h6 class="my-0 card-subsubtitle">Rp 600.000,00</h6>
                           </div>
                       </div>
                      </div>
                      <div class="col-6">
                        <div onClick="durasi_berlangganan(this,'12')" class="card card-mod border-0 align-top  border-secondary mb-3 d-inline-block mx-0" style="box-shadow: 0 4px 8px rgb(119 119 119 / 10%), 0 12px 20px rgb(119 119 119 / 20%) !important;" >
                           <div class="card-body py-3 px-2">
                              <h5 class="card-title-2 mb-2" >12 Bulan</h5>
                              <h6 class="my-0 card-subtitle card-subtitle-to mb-2">Rp 100.000,00/bln</h6>
                              <h6 class="my-0 card-subsubtitle">Rp 1.200.000,00</h6>
                           </div>
                       </div>
                      </div>
                      <input type="hidden" name="duration" id="duration" value="1">
                      <input type="hidden" name="code" id="code" value="<?= $code ?>">
                      <div class="col-12 pb-3">
                        <table class="table table-borderless mb-0">
                           <tbody><tr>
                              <td class="px-0 text-left border-0" id="title-durasi">Durasi berlangganan AMRA selama 1 Bulan</td>
                              <td class="px-0 text-left border-0" id="price-durasi">Rp 100.000,00</td>
                           </tr>
                        </tbody></table>
                     </div>
                     <div class="col-lg-12" >
                        <a onClick="renew_subscribtion()" class="btn btn-blue w-100"><b>Bayar Paket</b></a>
                     </div>
                   </div>
                 </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>
<script>
   function durasi_berlangganan(e, param){
      if(param == 1){
         total = 'Rp 100.000,00';
      }else if ( param == 3 ) {
         total = 'Rp 300.000,00';
      }else if ( param == 6 ) {
         total = 'Rp 600.000,00';
      }else if ( param == 12 ) {
         total = 'Rp 1.200.000,00';
      }
      $('.card').removeClass('actived');
      $(e).addClass('actived');
      $('#duration').val(param);
      $('#title-durasi').html(`Durasi berlangganan AMRA selama ${param} Bulan`);
      $('#price-durasi').html(total);
   }
</script>
