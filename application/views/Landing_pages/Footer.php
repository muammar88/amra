<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Third party plugin JS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="assets/landing_pages/js/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>
<!-- Contact form JS-->
<script src="assets/mail/jqBootstrapValidation.js"></script>
<script src="assets/mail/contact_me.js"></script>
<script src="assets/landing_pages/js/all.js"></script>
<script src="assets/landing_pages/aos/aos.js"></script>
<script src="assets/landing_pages/library/fontawesome-free-5.15.2-web/js/all.min.js"></script>
<script src="<?= base_url() ?>assets/material_template/plugins/myFrame/myFrames.js"></script>
<!-- Core theme JS-->
<script src="js/scripts.js"></script>
</body>
<script>
    AOS.init();

    function slide(param) {
        $("#scarouselExampleControls").carousel(param);
    }

    function get_token_subscribtion(code){
      var csrfName = localStorage.getItem("csrfName");
      var csrfHash = localStorage.getItem("csrfHash");
      var data = {};
      data[csrfName] = csrfHash;
      data['code'] = code;

      $.ajax({
         url: '<?= base_url() ?>Users/Payment/get_token',
         type: "post",
         dataType: "json",
         data: data,
         success: function(e) {
            snap.pay(e.token, {
               onSuccess: function(result){
                  result['code'] = code;
                  ajax_x(
                     "<?= base_url() ?>Users/Payment/save_process_log_saldo", function(a) {
                        window.location.href = "<?= base_url() ?>Users/Sign_in";
                     },[result]
                  );
               },
               onPending: function(result){
                  result['code'] = code;
                  ajax_x(
                     "<?= base_url() ?>Users/Payment/save_process_log_saldo", function(a) {
                        window.location.href = "<?= base_url() ?>Users/Sign_in";
                     },[result]
                  );
               },
               onError: function(result){
                  result['code'] = code;
                  ajax_x(
                     "<?= base_url() ?>Users/Payment/save_process_log_saldo", function(a) {
                        window.location.href = "<?= base_url() ?>Users/Sign_in";
                     },[result]
                  );
               }
            });
         }
      });
   }

    //
</script>

</html>
