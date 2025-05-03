<x-app-layout>  
    <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> Invoices </h3>
        </div>
        <div class="row">
          <div class="col-md-12 mb-5">
            <div class="d-flex justify-content-between">
              <form action="" id="frmSearchFilter">
                <div class="d-flex">
                    <div class="form-group">
                      <input type="text" class="form-control" name="filterAll" id="filterAll" placeholder="05-00001">
                    </div>
                    <div class="form-group mx-3">
                      <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
              </form>
              <div class="">
                <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-success mb-2">Add new invoice</a>
              </div>
            </div>
            <div class="card">
                <div class="card-body table-body">
                  @include('invoice.table')
                </div>
              </div>
          </div>
        </div>
      </div>
      @push('scripts')
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
          $(document).ready(function(){

            $(document).on('click', '.pagination a', function(e){
              e.preventDefault(); 
              var page = $(this).attr('href').split('page=')[1];
              fetchContent(page);
            }); 
        
            $(document).on('click', '.btn-remove', function(){
              let id = $(this).attr('data-id');
            
              Swal.fire({
                  icon: 'warning',
                  text: ` Are you sure you want to remove this invoice?`,
                  showCancelButton: true,
                  confirmButtonText: "Yes",
              }).then((result) => {
                  if (result.isConfirmed) {
                    $.blockUI();
                    $.ajax({
                      url: "{{ route('invoices.delete') }}",
                      data: { id : id,
                          _token: "{{ csrf_token() }}"
                      },
                      type: 'POST',
                      dataType: 'json',
                      success: function ( response ){
                        popupMessage('Success', response.message, 'success');
                        fetchContent();
                      },
                      error: function ( response ){
                        popupMessage('Error', 'Failed!', 'error');
                      },
                      complete: function ( response ){
                        $.unblockUI();
                      }
                    });
                  } 
              });
            })

            $(document).on('submit', '#frmSearchFilter', function(e){
              e.preventDefault();

              fetchContent();
            })

            function fetchContent(page = 1) {
              $.blockUI();

              let code = $('#filterAll').val();

              $.ajax({
                url: "{{ route('invoices.fetch-content') }}",
                type: 'GET',
                data: { page : page,
                      code: code
                 },
                dataType: 'json',
                success: function (response){
                  $('.table-body').html( response.html );
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    popupMessage('Error', 'Failed!', 'error');
                },
                complete: function ( response ){
                  $.unblockUI();
                }
              })
            }

          })
        </script>
      @endpush
</x-app-layout>