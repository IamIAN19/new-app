<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- plugins:css -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <!-- endinject -->
    
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <!-- Optional: Your own stylesheets -->
    <style>
      .table th, .table td {
        padding: .5rem !important;
     }

     .customDateFilterInput, #filterAll{
        height:45px;
     }
     
    </style>
    @stack('styles')
    <!-- endinject -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
  </head>
  <body>
    <div class="wrapper">
      @include('layouts.side-navigation-new')

      <div class="main">
        @include('layouts.top-navigation')

        <main class="content">
          <div class="container-fluid p-0">
            {{ $slot }}
          </div>
        </main>

        <footer class="footer">
          <div class="container-fluid">
            <div class="row text-muted">
              <div class="col-6 text-start">
                <p class="mb-0">
                  <a class="text-muted" href="#" target="_blank"><strong>MyInvoice</strong></a>
                </p>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
    <script src=" {{ asset('assets/js/app.js') }}"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- End plugin js for this page -->
 
    <script>
        (function($){
          "use strict";
            flatpickr(".customDateFilterInput", {
                mode: "range", // For single date selection
                inline: false,   // Display inline calendar
                showMonths: 2,  // Show two months side by side
                dateFormat: 'm/d/Y',
                locale: {
                    rangeSeparator: " - ",  // Replace "to" with " - "
                }
            });
      })()
    </script>

    <!-- jQuery Toast JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

    <!-- Bootstrap Bundle JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
          $('.table').addClass('table-hover');
        })
        
        function popupMessage(heading, text, icon = 'info', position = 'top-right') {
          $.toast({
              heading: heading,
              text: text,
              icon: icon,
              loader: true,
              loaderBg: '#9EC600',
              position: position,
              hideAfter: 5000
          });
        }

        function clearAllError()
        {
          $('span.error').text('');
        }
    </script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    @stack('scripts')
  </body>
</html>