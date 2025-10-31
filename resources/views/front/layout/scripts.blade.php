<!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('front/lib/easing/easing.min.js')}}"></script>
    <script src="{{asset('front/lib/owlcarousel/owl.carousel.min.js')}}"></script>

    <!-- Contact Javascript File -->
    <script src="{{asset('front/mail/jqBootstrapValidation.min.js')}}"></script>
    <script src="{{asset('front/mail/contact.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{asset('front/js/main.js')}}"></script>
    
    <!-- Custom Scripts -->
    <script src="{{asset('front/js/custom.js')}}"></script>

    <!-- filters Scripts -->
    <script src="{{asset('front/js/filters.js')}}"></script>

    <!--===========Image Zoom===========-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/elevatezoom/3.0.8//jquery.elevatezoom.min.js"></script>
    <script>
        $(document).ready(function() {
            // Destroy old zoom instance when caroussel chages
            $('#product-caroussel').on('slid.bs.carousel', function() {
                $('.zoomContainer').remove(); // remove old zoom container
                $('.zoom-image').elevateZoom({
                    zoomType: "lens",
                    lensShape: "round",
                    lensSize: 200
                });
            });
            // initialize for the first image
            $('.zoom-image').elevateZoom({
                zoomType: "lens",
                lensShape: "round",
                lensSize: 200
            });
        });
        </script>
<script>
  // quick CSS toggle if you want simple show/hide without CSS file:
  document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('currency-list');
    var btn = document.getElementById('current-currency-btn');
    if (list && btn) {
      // set style block/none via class .show used in JS
      var style = document.createElement('style');
      style.innerHTML = '#currency-list { display:none; position:absolute; background:#fff; border:1px solid #ddd; padding:0; } #currency-list.show { display:block; }';
      document.head.appendChild(style);
    }
  });
  // Optional: pass route to JS if using separate file:
  window.appConfig = { switchCurrencyUrl: "{{ route('currency.switch') }}" };
</script>
