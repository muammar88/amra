<body class="hold-transition sidebar-mini layout-fixed">
	<div class="wrapper backgrounds">
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="box-shadow: rgba(0, 0, 0, 0.75) 0px 7px 13px -9px;">
			<a class="nav-link" data-widget="pushmenu" href="#" style="color: #415192 !important;">
				<i class="fas fa-bars" id="fa-bars"></i>
				<i class="fas fa-times" id="fa-times" style="display:none;"></i>
			</a>
			<ul class="navbar-nav m-0 ml-auto">
				<li class="nav-item">
					<a class="nav-link py-0" title="Personal Info" onClick="editUser()">
						<div class="ml-xs-0" style="display:inline-block">
							<span class="pr-1 d-block text-right" style="color:#86a9c3;text-transform:uppercase;font-weight:bold;" id="profilName">
								<?php echo $this->session->userdata('superman')['fullname']; ?>
							</span>
							<span class="pr-1" style="color:#86a9c3;font-weight:bold;" id="profilName">
								<?php echo $this->session->userdata('superman')['username']; ?>
							</span>
						</div>
						<div class="pt-2 ml-0 ml-md-2" style="display:inline-block;height:auto;vertical-align:top;">
							<i class="fas fa-user-circle" style="color: #415192;"></i>
						</div>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="dropdown" href="#listNotif" title="Info Notifikasi" id="notif-bell">
						<i class="fas fa-bell" style="color: #415192;"></i>
					</a>
					<div id="listNotif" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<span class="dropdown-item dropdown-header">15 Notifications</span>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-exchange-alt mr-2"></i> Trans. Tiket Jatuh Tempo
							<span class="float-right text-muted text-sm" style="font-size: 12px !important;">3 Transaksi</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-file mr-2"></i> Data Belum Komplit
							<span class="float-right text-muted text-sm" style="font-size: 12px !important;">12 Jamaah</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-exchange-alt mr-2"></i> Trans. Paket Jatuh Tempo
							<span class="float-right text-muted text-sm" style="font-size: 12px !important;">2 Transaksi</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-exchange-alt mr-2"></i> Pengisian Saldo
							<span class="float-right text-muted text-sm" style="font-size: 12px !important;">2 Transaksi</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-exchange-alt mr-2"></i> Trans. Agen
							<span class="float-right text-muted text-sm" style="font-size: 12px !important;">3 Transaksi</span>
						</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link" title="Update Akun" data-toggle="dropdown" href="#">
						<i class="fas fa-cog" style="color: #415192;"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<a href="#" class="dropdown-item" onClick="logout_superman()">
							<i class="fas fa-sign-out-alt mr-2"></i> Logout
						</a>
					</div>
				</li>
			</ul>
		</nav>
		<?php
		echo myModal('myModal', '', '<form id="form_data_utama"></form>', 'modalBody', '30%', 'modalAddBrandLabel');
		echo myModal('myModal2', '', '<form id="form_data_utama2"></form>', 'modalBody2', '45%', 'modalAddBrandLabel2');
		?>
		<!-- Main Sidebar Container -->
		<aside id="particles-js" class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #1aa4b800;background-image: linear-gradient(141deg, #202e64 0%, #415192fa 75%) !important;">
			<!-- Brand Logo -->
			<a href="<?php echo base_url() ?>" class="brand-link" style="border: none;height: 73px;background-color: white;">
				<center>
					<img src="<?php echo base_url() ?>image/logo_superman.svg" alt="AdminLTE Logo" class="brand-image" style="opacity: .8;float: none;">
				</center>
			</a>
			<div class="sidebar pt-2">
				<input type="hidden" id="modul_tab" value='<?php echo json_encode($modul_tab); ?>'>
				<input type="hidden" id="submodul_tab" value='<?php echo json_encode($submodul_tab); ?>'>
				<input type="hidden" id="json_temp" >
				<div class="row" style="background-color: #3a4986;">
					<div class="col-10 justify-content-between mx-auto text-center">
						<div class="row" onClick="editUser()" style="cursor: pointer;">
							<div class="my-2 ">
							</div>
							<div class="col-8 my-2 px-1 py-1 justify-content-between mx-auto text-left" id="infoNameblock">
								<span class="pr-1 d-block text-left" style="color:#86a9c3;text-transform:uppercase;font-weight:bold;font-size: 12px;">
									<?php echo $this->session->userdata('superman')['fullname']; ?>
								</span>
								<span class="pr-1 d-block text-left" style="color:#86a9c3;text-transform:uppercase;font-size: 10px;">
									<?php echo $this->session->userdata('superman')['username']; ?>
								</span>
							</div>
						</div>
					</div>
				</div>
				<!-- Sidebar Menu -->
				<nav>
					<ul class="nav nav-pills nav-sidebar flex-column pt-3" data-widget="treeview" role="menu" data-accordion="false">
						<?php
						if (isset($modul_access)) {
							$group_echo = '';
							foreach ($modul_access as $key => $value) {
								if ($value['module_path'] == '#') {
									$group_echo .= '<li class="nav-item has-treeview">
														<a href="#" class="nav-link ">
															<i class="nav-icon ' . $value['module_icon'] . '"></i>
															<p>' . $value['module_name'] . '<i class="fas fa-angle-left right"></i></p>
														</a>
														<ul class="nav nav-treeview" sasa>';
									if (isset($value['submodule'])) {
										foreach ($value['submodule'] as $subkey => $subvalue) {
											$group_echo .= '<li class="nav-item">
																<a onClick="menu_superman( this, \'' . $subvalue['path'] . '\', \'' . $value['module_name'] . '\', \'' . $value['module_icon'] . '\', \'\', \'submodul\')" class="nav-link">
																	<i class="far fa-circle nav-icon"></i>
																	<p>' . $subvalue['name'] . '</p>
																</a>
															</li>';
										}
									}
									$group_echo .=    '</ul>
													</li>';
								} else {
									$nav_active = '';
									if ($value['module_name'] == 'Beranda') {
										$nav_active = 'nav-active';
									}
									$group_echo .= '<li class="nav-item ">
														<a onClick="menu_superman(this, \'' . $value['module_path'] . '\', \'' . $value['module_name'] . '\', \'' . $value['module_icon'] . '\',\'\', \'modul\' )" class="nav-link ' . $nav_active . ' ">
															<i class="nav-icon ' . $value['module_icon'] . '"></i>
															<p>' . $value['module_name'] . '</p>
														</a>
													</li>';
								}
							}
							echo $group_echo;
						}
						?>
					</ul>
				</nav>
				<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>
		<div class="content-wrapper">
			<div style="display: none;" id="loader"></div>
			<div class="content-header">
				<div class="container-fluid">
					<div class="row">
						<div class="col-2 col-lg-6 pl-3" id="position">
						</div>
						<div class="col-10 col-lg-6">
							<ol class="breadcrumb float-sm-right" id="breadcrumb">
							</ol>
						</div>
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<section class="content px-3 pb-0" style="min-height: 60vh;">
				<div class="row" id="fluid-content" style="height: 100%;"></div>
			</section>
		</div>
	</div>
</body>
