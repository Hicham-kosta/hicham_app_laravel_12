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
    // REMOVE THESE LINES:
    // $("#subadmins").DataTable();
    // $("#brands").DataTable();
    // $("#pages").DataTable();
    
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
        savedOrder: @json($usersSavedOrder ?? null),
        hiddenCols: @json($usersHiddenCols ?? []),
        tableName: "users"
      },
      {
        id: "currencies",
        savedOrder: @json($currenciesSavedOrder ?? null),
        hiddenCols: @json($currenciesHiddenCols ?? []),
        tableName: "currencies"
      },
      {
        id: "reviews",
        savedOrder: @json($reviewsSavedOrder ?? null),
        hiddenCols: @json($reviewsHiddenCols ?? []),
        tableName: "reviews"
      },
      {
        id: "wallet_credits",
        savedOrder: @json($walletCreditsSavedOrder ?? null),
        hiddenCols: @json($walletCreditsHiddenCols ?? []),
        tableName: "wallet_credits"
      },
      {
        id: "orders",
        savedOrder: @json($ordersSavedOrder ?? null),
        hiddenCols: @json($ordersHiddenCols ?? []),
        tableName: "orders"
      },
      {
        id: "pages",
        savedOrder: @json($pagesSavedOrder ?? null),
        hiddenCols: @json($pagesHiddenCols ?? []),
        tableName: "pages"
      },
      {
        id: "subscribers",
        savedOrder: @json($subscribersSavedOrder ?? null),
        hiddenCols: @json($subscribersHiddenCols ?? []),
        tableName: "subscribers"
      },
    ];
    
    tablesConfig.forEach(config => {
      const tableElement = $("#" + config.id);
      if(tableElement.length > 0){
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable("#" + config.id)) {
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">

<script>
// Disable auto discover for all elements
Dropzone.autoDiscover = false;

$(document).ready(function() {
    // ============ MAIN IMAGE DROPZONE ============
    var mainImageDropzone = new Dropzone("#mainImageDropzone", {
        url: "{{ route('vendor.products.upload-image') }}",
        maxFiles: 1,
        maxFilesize: 0.5, // 500KB
        acceptedFiles: 'image/jpeg,image/png,image/jpg,image/gif',
        addRemoveLinks: true,
        dictDefaultMessage: "<i class='fas fa-cloud-upload-alt'></i><br>Drop main image here or click to upload",
        dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
        dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
        dictFileTooBig: "File is too big ({{"{{"}}filesize{{"}}"}}MB). Max filesize: {{"{{"}}maxFilesize{{"}}"}}MB.",
        dictInvalidFileType: "You can't upload files of this type.",
        dictResponseError: "Server responded with {{"{{"}}statusCode{{"}}"}} code.",
        dictCancelUpload: "Cancel upload",
        dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
        dictRemoveFile: "Remove",
        dictMaxFilesExceeded: "You can only upload 1 main image.",
        
        init: function() {
            // If editing and main image exists, show it in dropzone
            @if(!empty($product['main_image']))
                var mockFile = { 
                    name: "{{ $product['main_image'] }}", 
                    size: 12345,
                    accepted: true 
                };
                this.emit("addedfile", mockFile);
                this.emit("thumbnail", mockFile, "{{ url('front/images/products/'.$product['main_image']) }}");
                this.emit("complete", mockFile);
                this.files.push(mockFile);
            @endif
            
            this.on("addedfile", function(file) {
                // Remove previous file if exists
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
            
            this.on("success", function(file, response) {
                // Store the file name
                file.uploadedName = response.fileName;
                
                // Update hidden input
                $('#main_image_hidden').val(response.fileName);
                
                // Show thumbnail
                if (response.fileName) {
                    this.emit("thumbnail", file, "/temp/" + response.fileName);
                }
            });
            
            this.on("removedfile", function(file) {
                // Clear hidden input
                $('#main_image_hidden').val('');
                
                // Delete temp file from server if it was uploaded
                if (file.uploadedName) {
                    $.ajax({
                        url: "{{ route('vendor.products.delete-temp-image') }}",
                        type: "POST",
                        data: {
                            filename: file.uploadedName,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                }
            });
            
            this.on("error", function(file, message) {
                $('#mainImageDropzoneError').text(message).show();
                setTimeout(function() {
                    $('#mainImageDropzoneError').hide();
                }, 5000);
            });
        },
        
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    // ============ ALTERNATIVE IMAGES DROPZONE ============
    var productImagesDropzone = new Dropzone("#productImagesDropzone", {
        url: "{{ route('vendor.products.upload-images') }}",
        maxFiles: 10,
        maxFilesize: 0.5, // 500KB
        acceptedFiles: 'image/jpeg,image/png,image/jpg,image/gif',
        addRemoveLinks: true,
        dictDefaultMessage: "<i class='fas fa-cloud-upload-alt'></i><br>Drop multiple images here or click to upload",
        dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
        dictFileTooBig: "File is too big ({{"{{"}}filesize{{"}}"}}MB). Max filesize: {{"{{"}}maxFilesize{{"}}"}}MB.",
        dictInvalidFileType: "You can't upload files of this type.",
        dictCancelUpload: "Cancel upload",
        dictRemoveFile: "Remove",
        dictMaxFilesExceeded: "You can only upload up to 10 images.",
        
        init: function() {
            // If editing and images exist, show them in dropzone
            @if(isset($product->product_images) && $product->product_images->count() > 0)
                @foreach($product->product_images as $img)
                    var mockFile = { 
                        name: "{{ $img->image }}", 
                        size: 12345,
                        accepted: true,
                        uploadedName: "{{ $img->image }}"
                    };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, "{{ url('front/images/products/'.$img->image) }}");
                    this.emit("complete", mockFile);
                    this.files.push(mockFile);
                @endforeach
            @endif
            
            this.on("success", function(file, response) {
                // Store the file name
                file.uploadedName = response.fileName;
                
                // Update hidden input - append to existing
                var currentVal = $('#product_images_hidden').val();
                if (currentVal) {
                    $('#product_images_hidden').val(currentVal + ',' + response.fileName);
                } else {
                    $('#product_images_hidden').val(response.fileName);
                }
                
                // Show thumbnail
                if (response.fileName) {
                    this.emit("thumbnail", file, "/temp/" + response.fileName);
                }
            });
            
            this.on("removedfile", function(file) {
                // Remove from hidden input
                var currentVal = $('#product_images_hidden').val();
                if (currentVal) {
                    var files = currentVal.split(',');
                    var index = files.indexOf(file.uploadedName);
                    if (index > -1) {
                        files.splice(index, 1);
                        $('#product_images_hidden').val(files.join(','));
                    }
                }
                
                // Delete temp file from server if it was uploaded
                if (file.uploadedName) {
                    $.ajax({
                        url: "{{ route('vendor.products.delete-temp-image') }}",
                        type: "POST",
                        data: {
                            filename: file.uploadedName,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                }
            });
        },
        
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    // ============ PRODUCT VIDEO DROPZONE ============
    var productVideoDropzone = new Dropzone("#productVideoDropzone", {
        url: "{{ route('vendor.products.upload-video') }}",
        maxFiles: 1,
        maxFilesize: 2, // 2MB
        acceptedFiles: 'video/mp4,video/avi,video/mov,video/wmv',
        addRemoveLinks: true,
        dictDefaultMessage: "<i class='fas fa-cloud-upload-alt'></i><br>Drop video here or click to upload",
        dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
        dictFileTooBig: "File is too big ({{"{{"}}filesize{{"}}"}}MB). Max filesize: {{"{{"}}maxFilesize{{"}}"}}MB.",
        dictInvalidFileType: "You can't upload files of this type.",
        dictCancelUpload: "Cancel upload",
        dictRemoveFile: "Remove",
        dictMaxFilesExceeded: "You can only upload 1 video.",
        
        init: function() {
            // If editing and video exists, show it in dropzone
            @if(!empty($product['product_video']))
                var mockFile = { 
                    name: "{{ $product['product_video'] }}", 
                    size: 12345,
                    accepted: true 
                };
                this.emit("addedfile", mockFile);
                this.emit("thumbnail", mockFile, "/images/video-icon.png");
                this.emit("complete", mockFile);
                this.files.push(mockFile);
            @endif
            
            this.on("addedfile", function(file) {
                // Remove previous file if exists
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
            
            this.on("success", function(file, response) {
                // Store the file name
                file.uploadedName = response.fileName;
                
                // Update hidden input
                $('#product_video_hidden').val(response.fileName);
                
                // Set video icon as thumbnail
                this.emit("thumbnail", file, "/images/video-icon.png");
            });
            
            this.on("removedfile", function(file) {
                // Clear hidden input
                $('#product_video_hidden').val('');
                
                // Delete temp file from server if it was uploaded
                if (file.uploadedName) {
                    $.ajax({
                        url: "{{ route('vendor.products.delete-temp-video') }}",
                        type: "POST",
                        data: {
                            filename: file.uploadedName,
                            _token: "{{ csrf_token() }}"
                        }
                    });
                }
            });
        },
        
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });
    
    // ============ SORTABLE IMAGES ============
    @if(isset($product->product_images) && $product->product_images->count() > 1)
        $("#sortable-images").sortable({
            update: function(event, ui) {
                var sortedIDs = $(this).sortable("toArray", { attribute: "data-id" });
                
                $.ajax({
                    url: "{{ route('vendor.products.update-image-sorting') }}",
                    type: "POST",
                    data: {
                        sorted_images: sortedIDs.map(function(id, index) {
                            return { id: id, sort: index + 1 };
                        }),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log("Images reordered successfully");
                    }
                });
            }
        });
        $("#sortable-images").disableSelection();
    @endif
    
    // ============ DELETE CONFIRMATION ============
    $(document).on('click', '.confirmDelete', function() {
        var module = $(this).data('module');
        var id = $(this).data('id');
        var deleteUrl = '';
        
        switch(module) {
            case 'product-main-image':
                deleteUrl = "{{ route('vendor.products.delete-main-image', ':id') }}".replace(':id', id);
                break;
            case 'product-image':
                deleteUrl = "{{ route('vendor.products.delete-image', ':id') }}".replace(':id', id);
                break;
            case 'product-video':
                deleteUrl = "{{ route('vendor.products.delete-video', ':id') }}".replace(':id', id);
                break;
        }
        
        if (confirm('Are you sure you want to delete this ' + module.replace('-', ' ') + '?')) {
            window.location.href = deleteUrl;
        }
    });
});
</script>

<style>
/* Dropzone custom styles */
.dropzone {
    border: 2px dashed #007bff !important;
    border-radius: 5px;
    background: #f8f9fa;
    padding: 20px;
    min-height: 150px;
}

.dropzone .dz-message {
    margin: 3em 0;
    color: #6c757d;
    font-size: 1.2em;
}

.dropzone .dz-preview .dz-image {
    border-radius: 5px;
}

.dropzone .dz-preview .dz-remove {
    font-size: 14px;
    text-decoration: none;
}

.dropzone .dz-preview.dz-file-preview .dz-image {
    background: #999;
}

/* Sortable images */
.sortable-wrapper {
    min-height: 100px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    border: 1px dashed #dee2e6;
}

.sortable-item {
    transition: transform 0.2s;
}

.sortable-item:hover {
    transform: scale(1.05);
    z-index: 10;
}

.drag-instruction {
    font-size: 0.9em;
    color: #6c757d;
    font-style: italic;
}
</style>