		<!-- jQuery -->
		<script src="<?php echo base_url('assets/material_template/plugins/jquery/jquery.min.js'); ?>"></script>
		<!-- jQuery UI 1.11.4 -->
		<script src="<?php echo base_url('assets/material_template/plugins/jquery-ui/jquery-ui.min.js'); ?> "></script>

		<script> $.widget.bridge('uibutton', $.ui.button) </script>
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
		<!-- OWL js -->
		<script src="<?php echo base_url('assets/material_template/plugins/owl/owl.carousel.min.js'); ?>"></script>
		<!--  SweetAlert -->
		<script src="<?php echo base_url('assets/material_template/plugins/sweetalert/sweetalert.min.js'); ?>"></script>
		<!-- LightBox JS-->
		<script src="<?php echo base_url('assets/material_template/plugins/lightbox/js/uikit.min.js'); ?>"></script>
		<script src="<?php echo base_url('assets/material_template/plugins/lightbox/js/uikit-icons.min.js'); ?>"></script>
		<!-- Pagination JS -->
		<script src="<?php echo base_url('assets/material_template/plugins/pagination/pagination.min.js'); ?>"></script>
		<!-- myFrames JS-->
		<script src="<?php echo base_url('assets/material_template/plugins/myFrame/myFrames.js'); ?>"></script>

		<?php
		if (isset($foto)) {
			echo '<script type="text/javascript"> var cFoto = parseInt("' . count($foto) . '"); </script>';
		} else {
			echo '<script type="text/javascript"> var cFoto = parseInt(1); </script>';
		}
		echo '<script type="text/javascript"> var baseUrl = "' . base_url() . '"; </script>';

		


		?>

		<!-- public JS-->
		<script src="<?php echo base_url('assets/material_template/app/' . $js . '.js'); ?>"></script>
		</body>
		<script type="text/javascript">
			$(document).ready(function() {
				localStorage.setItem('csrfName', '<?php echo  $this->security->get_csrf_token_name(); ?>');
				$(".nav-item").click(function() {
					$(".nav-item").removeClass("nav-active");
					$(this).addClass("nav-active");
				});
			});


			jQuery(window).load(function() {
				/*
		        Stop carousel
		    */
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



			var prevScrollpos = window.pageYOffset;
			window.onscroll = function() {
				if ($(window).width() >= 767) {
					var currentScrollPos = window.pageYOffset;
					if (prevScrollpos > currentScrollPos) {
						$(".navbar").css({
							height: '64px',
							top: '30px'
						});
						// $(".dropdown-menu").css({
						//                     top: '96% !important'
						//                 });
						// $(".nav-link").css({
						//                     "padding-left" : '.5rem',
						//                     "padding-right" : '.5rem',
						//                     "padding-top" : '29px',
						//                     "padding-bottom" : '29px',
						//                     height: '77px'
						//                 });
					} else {
						$(".navbar").css({
							height: '54px',
							top: '30px'
						});
						// $(".dropdown-menu").css({
						//                     top: '80% !important'
						//                 });
						// $(".nav-link").css({
						//                     "padding-left" : '.5rem',
						//                     "padding-right" : '.5rem',
						//                     "padding-top" : '15px',
						//                     "padding-bottom" : '15px',
						//                     height: '50px'
						//                 });
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
		</script>

		</html>
