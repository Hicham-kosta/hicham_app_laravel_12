<!--begin::Third Party Plugin(OverlayScrollbars)-->
<script
            src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
            integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
            crossorigin="anonymous"
            ></script>
        <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
            ></script>
        <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"
            ></script>
        <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
        <script src="{{asset('admin/js/adminlte.js')}}"></script>
        <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
        <script>
            const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
            const Default = {
              scrollbarTheme: 'os-theme-light',
              scrollbarAutoHide: 'leave',
              scrollbarClickScroll: true,
            };
            document.addEventListener('DOMContentLoaded', function () {
              const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
              if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                  scrollbars: {
                    theme: Default.scrollbarTheme,
                    autoHide: Default.scrollbarAutoHide,
                    clickScroll: Default.scrollbarClickScroll,
                  },
                });
              }
            });
        </script>
        <!--end::OverlayScrollbars Configure-->
        <!-- OPTIONAL SCRIPTS -->
        <!-- sortablejs -->
        <script
            src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
            integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ="
            crossorigin="anonymous"
            ></script>
        <!-- sortablejs -->
        <script>
            const connectedSortables = document.querySelectorAll('.connectedSortable');
            connectedSortables.forEach((connectedSortable) => {
              let sortable = new Sortable(connectedSortable, {
                group: 'shared',
                handle: '.card-header',
              });
            });
            
            const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
            cardHeaders.forEach((cardHeader) => {
              cardHeader.style.cursor = 'move';
            });
        </script>
        <!-- apexcharts -->
        <script
            src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
            integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
            crossorigin="anonymous"
            ></script>
        <!-- ChartJS -->
        <script>
            // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
            // IT'S ALL JUST JUNK FOR DEMO
            // ++++++++++++++++++++++++++++++++++++++++++
            
            const sales_chart_options = {
              series: [
                {
                  name: 'Digital Goods',
                  data: [28, 48, 40, 19, 86, 27, 90],
                },
                {
                  name: 'Electronics',
                  data: [65, 59, 80, 81, 56, 55, 40],
                },
              ],
              chart: {
                height: 300,
                type: 'area',
                toolbar: {
                  show: false,
                },
              },
              legend: {
                show: false,
              },
              colors: ['#0d6efd', '#20c997'],
              dataLabels: {
                enabled: false,
              },
              stroke: {
                curve: 'smooth',
              },
              xaxis: {
                type: 'datetime',
                categories: [
                  '2023-01-01',
                  '2023-02-01',
                  '2023-03-01',
                  '2023-04-01',
                  '2023-05-01',
                  '2023-06-01',
                  '2023-07-01',
                ],
              },
              tooltip: {
                x: {
                  format: 'MMMM yyyy',
                },
              },
            };
            
            const sales_chart = new ApexCharts(
              document.querySelector('#revenue-chart'),
              sales_chart_options,
            );
            sales_chart.render();
        </script>
        <!-- jsvectormap -->
        <script
            src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
            integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y="
            crossorigin="anonymous"
            ></script>
        <script
            src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
            integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY="
            crossorigin="anonymous"
            ></script>
        <!-- jsvectormap -->
        <script>
            const visitorsData = {
              US: 398, // USA
              SA: 400, // Saudi Arabia
              CA: 1000, // Canada
              DE: 500, // Germany
              FR: 760, // France
              CN: 300, // China
              AU: 700, // Australia
              BR: 600, // Brazil
              IN: 800, // India
              GB: 320, // Great Britain
              RU: 3000, // Russia
            };
            
            // World map by jsVectorMap
            const map = new jsVectorMap({
              selector: '#world-map',
              map: 'world',
            });
            
            // Sparkline charts
            const option_sparkline1 = {
              series: [
                {
                  data: [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021],
                },
              ],
              chart: {
                type: 'area',
                height: 50,
                sparkline: {
                  enabled: true,
                },
              },
              stroke: {
                curve: 'straight',
              },
              fill: {
                opacity: 0.3,
              },
              yaxis: {
                min: 0,
              },
              colors: ['#DCE6EC'],
            };
            
            const sparkline1 = new ApexCharts(document.querySelector('#sparkline-1'), option_sparkline1);
            sparkline1.render();
            
            const option_sparkline2 = {
              series: [
                {
                  data: [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921],
                },
              ],
              chart: {
                type: 'area',
                height: 50,
                sparkline: {
                  enabled: true,
                },
              },
              stroke: {
                curve: 'straight',
              },
              fill: {
                opacity: 0.3,
              },
              yaxis: {
                min: 0,
              },
              colors: ['#DCE6EC'],
            };
            
            const sparkline2 = new ApexCharts(document.querySelector('#sparkline-2'), option_sparkline2);
            sparkline2.render();
            
            const option_sparkline3 = {
              series: [
                {
                  data: [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21],
                },
              ],
              chart: {
                type: 'area',
                height: 50,
                sparkline: {
                  enabled: true,
                },
              },
              stroke: {
                curve: 'straight',
              },
              fill: {
                opacity: 0.3,
              },
              yaxis: {
                min: 0,
              },
              colors: ['#DCE6EC'],
            };
            
            const sparkline3 = new ApexCharts(document.querySelector('#sparkline-3'), option_sparkline3);
            sparkline3.render();
        </script>

        <!-- jQuery -->
        
        <script src="{{url('admin/js/jquery-3.7.1.min.js')}}"></script>

         <!-- jQuery UI -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Select2 JS -->
         <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Bootstrap JS -->

        <!--Custom Script-->
        <script src="{{url('admin/js/custom.js')}}"></script>

        <!--Datatable-->
        <!-- Datatable CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <!-- ColRecrder -->
        <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.6.2/css/colReorder.dataTables.min.css">
        <!-- buttons CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
        <!-- Datatable JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <!-- ColReorder JS -->
        <script src="https://cdn.datatables.net/colreorder/1.6.2/js/dataTables.colReorder.min.js"></script>
        <!-- Buttons Extension -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <!-- Column Visibility -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
        <script>
          $(document).ready(function(){
            $("#subadmins").DataTable();
            $("#brands").DataTable();
            // Inject PHP data into JS safely
            const tablesConfig = [
              {
                id: "categories",
                savedOrder: @json($categoriesSavedOrder ?? null),
                hiddenCols: @json($categoriesHiddenCols ?? []),
                tableName: "Categories"
              },
              {
                id: "products",
                savedOrder: @json($productsSavedOrder ?? null),
                hiddenCols: @json($productsHiddenCols ?? []),
                tableName: "products"
              },
              {
                id: "filters",
                savedOrder: @json($filtersSavedOrder ?? null),
                hiddenCols: @json($filtersHiddenCols ?? []),
                tableName: "filters"
              },
              {
                id: "filters_values",
                savedOrder: @json($filterValuesSavedOrder ?? null),
                hiddenCols: @json($filterValuesHiddenCols ?? []),
                tableName: "filter_values"
              },
              {
                id: "coupons",
                savedOrder: @json($couponsSavedOrder ?? null),
                hiddenCols: @json($couponsHiddenCols ?? []),
                tableName: "coupons"
              },
              {
                id: "users",
                savedOrder: @json($couponsSavedOrder ?? null),
                hiddenCols: @json($couponsHiddenCols ?? []),
                tableName: "users"
              },
              {
                id: "currencies",
                savedOrder: @json($couponsSavedOrder ?? null),
                hiddenCols: @json($couponsHiddenCols ?? []),
                tableName: "currencies"
              },
              {
                id: "reviews",
                savedOrder: @json($couponsSavedOrder ?? null),
                hiddenCols: @json($couponsHiddenCols ?? []),
                tableName: "reviews"
              },
              {
                id: "wallet_credits",
                savedOrder: @json($couponsSavedOrder ?? null),
                hiddenCols: @json($couponsHiddenCols ?? []),
                tableName: "wallet_credits"
              },
              {
                id: "orders",
                savedOrder: @json($couponsSavedOrder ?? null),
                hiddenCols: @json($couponsHiddenCols ?? []),
                tableName: "orders"
              },
            ];
            tablesConfig.forEach(config => {
              const tableElement = $("#" + config.id);
              if(tableElement.length > 0){
                let dataTable = tableElement.DataTable({
                  order: [[0, 'desc']],
                  colReorder:{
                    order: config.savedOrder
                  },
                  stateSave: true,
                  dom: 'Bfrtip',
                  buttons: ['colvis'],
                  columnDefs: config.hiddenCols.map(index => ({
                    targets: parseInt(index),
                    visible: false
                  }))
                });
                dataTable.on('column-reorder', function(){
                  savePreferences(config.tableName, dataTable.colReorder.order(),
                getHiddenColumnIndexes(dataTable));
                });
                dataTable.on('column-visibility.dt', function(){
                  savePreferences(config.tableName, dataTable.colReorder.order(),
                getHiddenColumnIndexes(dataTable));
                });
              }
              });
              function getHiddenColumnIndexes(dataTable) {
                let hidden = [];
                dataTable.columns().every(function(index){
                  if(!this.visible()) hidden.push(index);
                });
                return hidden;
              }
              function savePreferences(tableName, columnOrder, hiddenCols) {
                $.ajax({
                  url: "{{url('admin/save-column-visibility')}}",
                  type: 'POST',
                  data: {
                    _token: "{{ csrf_token() }}",
                    table_key: tableName,
                    column_order: columnOrder,
                    hidden_column: hiddenCols
                  },
                  success: function(response) {
                    console.log("Preferences saved successfully for " + tableName + ":", response);
                  }
                  
                });
                $.ajax({
                  url: "{{url('admin/save-column-order')}}",
                  type: 'POST',
                  data: {
                    _token: "{{ csrf_token() }}",
                    table_key: tableName,
                    column_order: columnOrder
                  },
                  success: function(response) {
                    console.log("Column order saved successfully for", response);
                  }
                });
              }           
          });
        </script>
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

        <!-- Dropzon CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
        <!-- Dropzone JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

        <script>
        Dropzone.autoDiscover = false;
        // Main Image Dropzone
        let mainImageDropzone  = new Dropzone('#mainImageDropzone',{
          url:"{{route('product.upload.image')}}",
          maxFiles: 1,
          acceptedFiles: "image/*",
          maxFilesize: 0.5, // MB
          addRemoveLinks: true,
          dictDefaultMessage: "Drop main image here or click to upload",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          success: function(file, response) {
            // Store file name to reference it later in deletion
            file.uploadedFileName = response.fileName;
            document.getElementById('main_image_hidden').value = response.fileName;
          },
          removedFile: function(file){
            // Optionnal check if the file was successfully uploaded
            if(file.uploadedFileName){
              $.ajax({
                url: "{{route('admin.product.delete-image')}}",
                type: 'POST',
                data: {
                  _token: "{{ csrf_token() }}",
                  image: file.uploadedFileName,
                },
                success: function(response) {
                  console.log("Main image deleted successfully.");
                  //Clear hidden field if the image is removed
                  document.getElementById('main_image_hidden').value = '';
                },
                error: function() {
                  console.log("Error deleting main image.");
                }
              });
            }
            // Remove preview element from Dropzone UI
            var previewElement = file.previewElement;
            if(previewElement !== null){
              previewElement.parentNode.removeChild(previewElement);
            }
          },
          error: function(file, message){
            // Prevent multiple alerts for the same file
            if(!file.alreadyRejected){
              file.alreadyRejected = true; // Mark the file as already rejected
              // Show the error message in the container instead of an alert()
              let errorContainer = document.getElementById('mainImageDropzoneError');
              if(errorContainer) {
                errorContainer.innerText = typeof message === 'string' ? message : message.message;
                errorContainer.style.display = 'block'; // Show the error container
                // Hide after 5 seconds
                setTimeout(() => {
                  errorContainer.style.display = 'none';
                }, 4000);
            }
          }
            this.removeFile(file); // Remove the file from Dropzone
          },
          init: function(){
            this.on("maxfilesexceeded", function(file){
              this.removeAllFiles();
              this.addFile(file);
            });
          }
        });

        // Product images Dropzone
        let productImagesDropzone = new Dropzone("#productImagesDropzone", {
          url: "{{route('product.upload.images')}}",
          maxFiles: 10,
          acceptedFiles: "image/*",
          parallelUploads: 10, // Allow multiple files to be uploaded at once
          uploadMultiple: false, // Disable multiple file uploads
          maxFilesize: 0.5, // MB
          addRemoveLinks: true,
          dictDefaultMessage: "Drop images here or click to upload",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          init: function() {
            this.on("success", function(file, response) {
              //append filename to hidden input
              let hiddenInput = document.getElementById('product_images_hidden');
              let currentVal = hiddenInput.value;
              hiddenInput.value = currentVal ? currentVal + ',' + response.fileName : 
              response.fileName; // Update the hidden input value
              file.uploadedFileName = response.fileName; // Store the filename in the file object
              });
            this.on("removedfile", function(file) {
              if(file.uploadedFileName){
                let hiddenInput = document.getElementById('product_images_hidden');
                let currentVal = hiddenInput.value.split(',').filter(name => name!==
                file.uploadedFileName).join(','); // Get current value without empty strings
                // Optional delete the file from server
                $.ajax({
                  url: "{{route('product.delete-temp-altimage')}}",
                  type: 'POST',
                  data: {
                    filename: file.uploadedFileName,
                  },
                  headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                  }
                });
              }
            });
            }    
        });
        
        // Product Video Dropzone
        let productVideoDropzone = new Dropzone('#productVideoDropzone',{
          url:"{{route('product.upload.video')}}",
          maxFiles: 1,
          acceptedFiles: "video/*",
          maxFilesize: 2, // MB
          addRemoveLinks: true,
          dictDefaultMessage: "Drop video here or click to upload",
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          success: function(file, response) {
            document.getElementById('product_video_hidden').value = response.fileName;
            file.uploadedFileName = response.fileName; // Store the filename in the file object
          },
          removedFile: function(file){
            if(file.uploadedFileName){
              document.getElementById('product_video_hidden').value = '';
              $.ajax({
                url: "{{route('product.delete.temp.video')}}",
                type: 'POST',
                data: {
                  filename: file.uploadedFileName,
                },
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
              });
            }
            let previewElement = file.previewElement;
            if(previewElement !== null){
              previewElement.parentNode.removeChild(previewElement);
            }
          },
          init: function(){
            this.on("maxfilesexceeded", function(file){
              this.removeAllFiles();
              this.addFile(file);
            });
          }
        });

        // Product image sort script
        $("#sortable-images").sortable({
          helper: 'clone',
          placeholder: "sortable-placeholder",
          forcePlaceholderSize: true,
          scroll: true,
          axis: 'x', // Restrict to horizontal only
          update: function(event, ui){
            let sortedIds = [];
            $('#sortable-images .sortable-item').each(function(index){
              sortedIds.push({
                id: $(this).data('id'),
                sort: index
             });
            });
            $.ajax({
              url: "{{route('admin.product.update-image-sorting')}}",
              method: "POST",
              data: {
                _token: "{{ csrf_token() }}",
                sorted_images: sortedIds
              }
            });
          }
        });
        </script>

        <script>
      // Initialize submenu functionality
      $(document).ready(function() {
        $('.dropdown.submenu a.test').on('click', function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
      });
    </script>

<script>
  window.appConfig = window.appConfig || {};
  window.appConfig.currencySwitchUrl = "{{ route('currency.switch') }}";
</script>