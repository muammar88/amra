  <body class="main-body hold-transition sidebar-mini layout-fixed">
      <div class="wrapper">
          <div class="col-lg-12 headline " style="background-color: #94c741;background-image: linear-gradient(141deg, #19ad90 0%, #94c741 75%);">
              <div class="row">
                  <div class="col-lg-10  justify-content-center list_upper container-fluid">
                      <div class="row">
                          <div class="area-baris col-lg-12">
                              <div class="row">
                                  <div class="col-lg-12 ">
                                      <div class="row">
                                          <input type="hidden" id="security" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                          <div class="col-lg-2 col-md-2 col-sm-2 col-3 posisi-baris">
                                              <span class="baris">HEADLINE :</span>
                                          </div>
                                          <div class="col-lg-10 col-md-10 col-sm-10 col-9 runtext-container">
                                              <div class="main-runtext">
                                                  <marquee direction="" onmouseover="this.stop();" onmouseout="this.start();">
                                                      <div class="holder">
                                                          <?php
                                                            if (isset($headline)) {

                                                                $i = 1;
                                                                $headPieace = '';
                                                                foreach ($headline as $key => $value) {
                                                                    if ($i == 1) {
                                                                        $headPieace .= '<div class="text-container px-3">
                                                                                        <a data-fancybox-group="gallery" class="fancybox" title="' . $value['title'] . '" href="' . base_url() . 'P/Berita/' . $value['berita_url'] . '" target="_blank">
                                                                                            ' . $value['title'] . '
                                                                                        </a>
                                                                                    </div>';
                                                                    } else {
                                                                        $headPieace .= '<i class="fas fa-ellipsis-h" style="color:#d06464;"></i>
                                                                                    <div class="text-container px-3">
                                                                                        <a data-fancybox-group="gallery" class="fancybox" title="' . $value['title'] . '" href="' . base_url() . 'P/Berita/' . $value['berita_url'] . '" target="_blank">
                                                                                            ' . $value['title'] . '
                                                                                        </a>
                                                                                    </div>';
                                                                    }
                                                                    $i++;
                                                                }

                                                                echo $headPieace;
                                                            }
                                                            ?>
                                                      </div>
                                                  </marquee>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <nav class="navbar navbar-light navbar-expand-md main-nav" style="-webkit-box-shadow: 0px 7px 13px -9px rgba(0,0,0,0.75);-moz-box-shadow: 0px 7px 13px -9px rgba(0,0,0,0.75);box-shadow: rgba(0, 0, 0, 0.75) 0px 7px 13px -9px;z-index: 10000;">
              <div class="col-lg-10 container-fluid">
                  <button class="navbar-toggler" data-toggle="collapse" data-target="#navcol-1">
                      <span class="sr-only">Toggle navigation</span>
                      <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="small-navbar-brand">
                      <img src="<?php echo base_url() ?>assets/material_template/img/logo-frontend-small.png" class="header-2 float-left rounded">
                  </div>
                  <div class="collapse navbar-collapse" id="navcol-1">
                      <a class="navbar-brand" href="<?php echo base_url();  ?>">
                          <img src="<?php echo base_url() ?>assets/material_template/img/logo-frontend.svg" class="header-2 float-left rounded">
                      </a>
                      <div class="topnav-right nv">
                          <ul class="navbar-nav my-nav ">
                              <li class="nav-item <?php echo ($active == 'Beranda' ? ' nav-active ' : ''); ?>" role="presentation">
                                  <a class="nav-link hvr-underline-from-center" href="<?php echo base_url(); ?>">Beranda</a>
                              </li>
                              <li class="nav-item <?php echo ($active == 'Profil' ? ' nav-active ' : ''); ?>" role="presentation">
                                  <a class="nav-link hvr-underline-from-center" href="<?php echo base_url(); ?>P/Profil">Profil</a>
                              </li>
                              <li class="nav-item <?php echo ($active == 'Pendaftaran' ? ' nav-active ' : ''); ?>" role="presentation">
                                  <a class="nav-link hvr-underline-from-center" href="<?php echo base_url(); ?>P/Pendaftaran">Pendaftaran</a>
                              </li>
                              <li class="nav-item <?php echo ($active == 'Keanggotaan' ? ' nav-active ' : ''); ?>" role="presentation">
                                  <a class="nav-link hvr-underline-from-center" href="<?php echo base_url(); ?>P/Keanggotaan">Keanggotaan</a>
                              </li>
                              <li class="nav-item <?php echo ($active == 'Gallery' ? ' nav-active ' : ''); ?>" role="presentation">
                                  <a class="nav-link hvr-underline-from-center" href="<?php echo base_url(); ?>P/Gallery">Gallery</a>
                              </li>
                              <li class="nav-item <?php echo ($active == 'Kontak' ? ' nav-active ' : ''); ?>" role="presentation">
                                  <a class="nav-link hvr-underline-from-center" href="<?php echo base_url(); ?>P/Kontak">Kontak</a>
                              </li>
                          </ul>
                      </div>
                  </div>
              </div>
          </nav>