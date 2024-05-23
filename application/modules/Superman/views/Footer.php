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
		<!-- Webcam JS -->
		<script src="<?php echo base_url('assets/material_template/plugins/webcamjs/webcam.min.js'); ?>"></script>
		<!-- CK Editor -->
		<script src="<?php echo base_url('assets/material_template/plugins/ckeditor/ckeditor.js'); ?>"></script>
		<!-- Main JS -->
		<?php
		if (isset($foto)) {
			echo '<script type="text/javascript"> var cFoto = parseInt("' . count($foto) . '"); </script>';
		} else {
			echo '<script type="text/javascript"> var cFoto = parseInt(1); </script>';
		}
		echo '<script type="text/javascript"> let baseUrl = "' . base_url() . '"; </script>';
		echo '<script type="text/javascript"> var level_akun = "' . $this->session->userdata($this->config->item('apps_name'))['level_akun'] . '"; </script>';
		if (isset($js)) :
			if (is_array($js)) :
				foreach ($js as $js_key => $js_value) :
					echo '<script src="' . base_url("assets/material_template/app/") . $js_value . '.js"></script>';
				endforeach;
			else :
				echo '<script src="' . base_url("assets/material_template/app/") . $js . '.js"></script>';
			endif;
		endif;
		echo '<script type="text/javascript"> var csrfs = "' . $this->security->get_csrf_hash() . '"; </script>';
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
				// load first
				// menu_superman(this, 'beranda', 'Beranda', 'fas fa-home', 'true', 'modul');
				menu_superman(this, 'dashboard', 'Dashboard', 'fas fa-home','', 'modul' );
			});

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

			let development_alert = () => {
		 	  $.alert({
		 		  icon: 'fas fa-wrench',
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

			let error_alert = (text) => {
				$.alert({
					icon: 'far fa-frown',
					title: '<span style="color:red;font-weight:bold;">ERROR</span>',
					content: text,
					type: 'red',
					typeAnimated: true
				});
			}

			// logout
			function logout() {
				ajax_x(
					baseUrl + "Users/Sign_in/logout",
					function(e) {
						window.location.href = baseUrl + "Users/Sign_in";
					}, []
				);
			}

			function get_data_non_pagination_laporan(JSONData, callback) {
				// create first param
				var paramFirts = {};
				for (x in JSONData['param']) {
					paramFirts[x] = JSONData['param'][x];
				}
				ajax_x(
					baseUrl + JSONData['url'],
					function(e) {
						var html = "";
						if (e["data"] != undefined) {
							if (e["data"].length > 0) {
								for (x in e["data"]) {
									html += window[JSONData['fn']](JSON.stringify(e['data'][x]));
								}
								if (typeof callback === "function") {
									callback(JSON.stringify(e));
								}
							} else {
								html += `<tr>${JSONData['warning_text']}</tr>`;
								$('#footers').remove();
							}
						} else {
							html += `<tr>${JSONData['warning_text']}</tr>`;
							$('#footers').remove();
						}
						$("#" + JSONData['bodyTable_id']).html(html);
					},
					[paramFirts]
				);
			}

			function get_data(perpage, JSONData) {
				// create first param
				var paramFirts = {};
				paramFirts['perpage'] = perpage;
				for (x in JSONData['param']) {
					paramFirts[x] = JSONData['param'][x];
				}
				ajax_x(
					baseUrl + JSONData['url'],
					function(e) {
						var paginationNumber = e["total"];
						var data = new Array();
						for (var i = 1; i <= paginationNumber; i++) {
							data.push(i);
						}
						var container = $("#" + JSONData['pagination_id']);
						container.pagination({
							dataSource: data,
							pageSize: perpage,
							showPrevious: true,
							showNext: true,
							callback: function(data, pagination) {
								// create first param
								var paramSecond = {};
								paramSecond['pageNumber'] = pagination["pageNumber"];
								paramSecond['perpage'] = perpage;
								for (y in JSONData['param']) {
									paramSecond[y] = JSONData['param'][y];
								}
								var html = "";
								if (pagination["pageNumber"] == 1) {
									if (e["data"] != undefined) {
										if (e["data"].length > 0) {
											for (x in e["data"]) {
												console.log("Data Pertama");
												html += window[JSONData['fn']](JSON.stringify(e['data'][x]));
											}
										} else {
											html += `<tr>${JSONData['warning_text']}</tr>`;
										}
									} else {
										html += `<tr>${JSONData['warning_text']}</tr>`;
									}
									$("#" + JSONData['bodyTable_id']).html(html);
								} else {
									ajax_x(
										baseUrl + JSONData['url'],
										function(e) {
											if (e["data"] != undefined) {
												if (e["data"].length > 0) {
													for (x in e["data"]) {
														html += window[JSONData['fn']](JSON.stringify(e['data'][x]));
													}
												} else {
													html += `<tr>${JSONData['warning_text']}</tr>`;
												}
											} else {
												html += `<tr>${JSONData['warning_text']}</tr>`;
											}
											$("#" + JSONData['bodyTable_id']).html(html);
										},
										[paramSecond]
									);
								}
							}
						});
					},
					[paramFirts]
				);
			}

			// function midtrans_payment(){
			//
			//
			// }

			// menu function
			let menu_superman = (e, fn, mdlName, mdlIcon, state, modul_submodul_state) => {
				Webcam.reset();
				if (state == undefined) {
					$(".nav-link").removeClass("nav-active");
					$(e).addClass("nav-active");
				}
				$("#position").hide().html(positionForm(mdlIcon, mdlName)).fadeIn(500);
				$("#breadcrumb").hide().html(breadcumForm(fn == mdlName.toLowerCase() ? JSON.stringify([mdlName]) : JSON.stringify([mdlName, capitalizeFirstLetter(fn)]))).fadeIn(500);
				pages_content_controllers(fn, modul_submodul_state);
			}

			let pages_content_controllers = (param_path, state) => {
				let activities = [];
				let data = [];
				let path = [];
				if (state == 'modul') {
					var modul_tab_JSON = $('#modul_tab').val();
					var modul_tab = JSON.parse(modul_tab_JSON);
					var i = 0;
					if (modul_tab_JSON.indexOf(param_path) !== -1) {
						for (x in modul_tab[param_path]) {
							activities[i] = [modul_tab[param_path][x]['icon'], modul_tab[param_path][x]['name'], ''];
							data[i] = Tab(modul_tab[param_path][x]['name'], modul_tab[param_path][x]['path'], modul_tab[param_path][x]['description'], modul_tab[param_path][x]['path'] + '_Pages');
							path[i] = modul_tab[param_path][x]['path'];
							i++;
						}
					}
				} else if (state == 'submodul') {
					var submodul_tab_JSON = $('#submodul_tab').val();
					var submodul_tab = JSON.parse(submodul_tab_JSON);

					var i = 0;
					if (submodul_tab_JSON.indexOf(param_path) != -1) {
						for (x in submodul_tab[param_path]) {
							activities[i] = [submodul_tab[param_path][x]['icon'], submodul_tab[param_path][x]['name'], ''];
							data[i] = Tab(submodul_tab[param_path][x]['name'], submodul_tab[param_path][x]['path'], submodul_tab[param_path][x]['description'], submodul_tab[param_path][x]['path'] + '_Pages');
							path[i] = submodul_tab[param_path][x]['path'];
							i++;
						}
					}
				}
				// load pages
				loadPage(FrameContentViews(JSON.stringify(activities), JSON.stringify(data)));
				// run landing page
				for (y in path) {
					var fnName = path[y] + '_getData';
					if (FnCk(fnName) === false) {
						console.log('Fungsi ' + fnName + ' tidak ditemukan');
					}
				}
			}

			let Tab = (label, idClassName, descs, fn) => {
				var description = '';
				var content = '';
				if (eval("typeof " + fn) === 'function') {
					content += window[fn]();
				} else {
					content += '<div class="col-12 text-center">Fitur sedang dibangun.</div>';
				}
				if (descs != undefined) {
					description = descs;
				}
				return `<div class="row">
			               <div class="col-12 col-sm-10 col-md-8 col-lg-8 p-3">
			                  <p class="m-0 description" >${description}</p>
			               </div>
			               <div class="d-none d-sm-block col-sm-2 col-md-4 col-lg-4 p-3 text-right">
			                  <span class="showPosition" id="showPos${idClassName}">${label}</span>
			               </div>
			               <div class="col-12 col-lg-12 px-3 pb-0 pt-0">
			                  <div class="row" id="content_${idClassName}">${content}</div>
			               </div>
							</div>`;
			}

			let FrameContentViews = (JSONActivities, JSONData) => {
				return `<div class="col-md-12 col-lg-12 col-xl-12 px-0">
				            <ul class="nav nav-tabs">
				                ${Navigation(JSONActivities)}
				            </ul>
				         </div>
				        	<div class="col-md-12 col-lg-12 col-xl-12 h-100 mb-3 innerContent" >
					        	<div class="row">
					        		${Slider(JSONActivities, JSONData)}
					        	</div>
				         </div>`;
			}

			let capitalizeFirstLetter = (string) => {
				return string.charAt(0).toUpperCase() + string.slice(1);
			}

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

			// slider content views
			let Slider = (activities, JSONdata) => {
				let slider = JSON.parse(activities);
				let data = JSON.parse(JSONdata);
				let i = 0;
				let feedBack = `<div class="col-12">
									  	<div id="sliderContent" class="carousel slide" data-ride="carousel" data-interval="false" style="min-height: 73vh;height: 100%;font-size: 12px;">
										 	<div class="carousel-inner">`;
				for (x in data) {
					feedBack += `<div class="carousel-item ${i == 0 ? 'active' : ''}">${data[x]}</div>`;
					// menu_superman
					i++;
				}
				feedBack += `</div>
									  	</div>
									</div>`;
				return feedBack;
			}

			function loadPage(viewString) {
				$('#fluid-content').hide().html(viewString).fadeIn(500);
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

			function perpanjang(){
				ajax_x(
					baseUrl + "Users/perpanjang_berlangganan",
					function(e) {
						window.open( baseUrl + "Users/Renew_subscribtion?code=" + e['data'],'_blank');
					}, []
				);
			}
		</script>

		</html>
