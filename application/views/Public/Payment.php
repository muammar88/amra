<style>
.btn-blue{
   background-color: #033047 !important;
   width: 150px;
   color: #fff;
   border-radius: 2px;
}
</style>

<body class="backgrounds">

   <div class="wrap" style="background-color: #1aa4b800;background-image: linear-gradient(141deg, #cacaca 0%, #fffffff2 75%) !important;min-height:100vh;">
      <nav class="navbar navbar-expand-lg bg-secondary fixed-top" id="mainNav" style="min-height: 70px;">
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
            <div class="col-8 mx-auto" >
               <div class="card border-0 rounded-0" style="width: 100%;">
                 <div class="card-body">
                   <h5 class="card-title mb-4"><b>PEMBAYARAN PAKET</b></h5>
                   <div class="row">
                      <div class="col-lg-8" >
                         <h6 class="card-subtitle mb-2 text-muted">Informasi Akun</h6>
                         <table class="table table-borderless">
                           <tr>
                              <td class="px-0 py-1" style="width:40%;">Kode Perusahaan</td>
                              <td class="py-1"><?= $code ?></td>
                           </tr>
                           <tr>
                              <td class="px-0 py-1">Nama Perusahaan</td>
                              <td class="py-1"><?= $name ?></td>
                           </tr>
                           <tr>
                              <td class="px-0 py-1">Whatsapp Number</td>
                              <td class="py-1"><?= $whatsapp_number ?></td>
                           </tr>
                           <tr>
                              <td class="px-0 py-1">Telp</td>
                              <td class="py-1"><?= $telp ?></td>
                           </tr>
                           <tr>
                              <td class="px-0 py-1">Email</td>
                              <td class="py-1"><?= $email ?></td>
                              <td class="py-1"></td>
                              <td class="py-1"></td>
                           </tr>
                         </table>
                      </div>
                      <div class="col-lg-4" >
                         <h6 class="card-subtitle mb-2 text-muted">Informasi Pembayaran</h6>
                           <table class="table table-borderless">
                              <tr>
                                 <td class="px-0 py-1" >Durasi <?= $duration ?> Bulan</td>
                              </tr>
                              <tr>
                                 <td class="px-0 py-1" >Rp <?= number_format($pay_per_month) ?>,00/bln</td>
                              </tr>
                              <tr>
                                 <td class="px-0 py-1" >Rp <?= number_format($total) ?>,00</td>
                              </tr>
                              <tr>
                                 <td class="px-0 py-1" >Berlangganan sampai :<br><?= $end_date_subscribtion?></td>
                              </tr>
                           </table>
                           <input type="hidden" name="result_type" id="result-type" value="">
                           <input type="hidden" name="result_data" id="result-data" value="">
                      </div>
                     <div class="col-lg-8" >
                     </div>
                     <div class="col-lg-4" >
                        <a onClick="get_token_subscribtion('<?= $code ?>')" class="btn btn-blue w-100"><b>Bayar Paket</b></a>
                     </div>
                   </div>
                 </div>
               </div>
            </div>
         </div>
      </div>

   </div>
   <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $midtrans_client_key ?>"></script>
</body>

<script>
   
</script>
