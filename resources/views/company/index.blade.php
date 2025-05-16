<x-app-layout>  
    <div class="content-wrapper">

        <div class="my-2">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title"> Company Manager </h3>
            @hasPermission('add')
              <button class="btn btn-primary align-self-end btn-show" data-href="{{ route('company.store') }}">Add Company</button>
            @endhasPermission
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 ">
            <div class="card">
              <div class="card-body table-body">
                  @include('company.table')
              </div>
            </div>
          </div>
        </div>
    </div>

      <!-- Modal -->
      <div class="modal fade" id="companyModal" tabindex="-1" aria-labelledby="companyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-header">
              <h5 class="modal-title" id="companyModalLabel">Create Company</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form action="" id="frmSaveCompany">
                @csrf
                <input type="hidden" name="id" id="companyId" value="">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" class="name" id="company_name" required>
                        <span class="error text-danger mt-1"></span>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" form="frmSaveCompany" class="btn btn-primary">Save changes</button>
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

                $(document).on('click', '.btn-show', function(){
                  let url = $(this).attr('data-href');

                  $('#frmSaveCompany').attr('action', url);

                  $('#companyModal').modal('show');
                })

                $(document).on('submit', '#frmSaveCompany', function(e){
                  e.preventDefault();

                  let url = $(this).attr('action');
                  let frm = $(this).serialize();
                  
                  $.blockUI({
                      baseZ: 2000
                  }); 

                  clearAllError();

                  $.ajax({
                    url: url,
                    data: frm,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response){
                      $('#companyModal').modal('hide');
                      popupMessage('Success', response.message, 'success' );
                      fetchContent();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                      if( XMLHttpRequest.status == 422 ){

                          var errors = XMLHttpRequest.responseJSON.errors;

                          popupMessage('Error', 'Failed! Please check user data before saving again.', 'error' );

                          $.each(errors, function(i, e){
                              $('[name="'+i+'"]').parent().find('span.error').html(`*${e}`);
                          });
                      }
                      else{
                          popupMessage('Error', 'Failed!', 'error');
                      }
                    },
                    complete: function ( response ){
                      $.unblockUI();
                    }
                  })
                })

                $(document).on('click', '.btn-edit', function(){

                  let id = $(this).parent('td').attr('data-id');
                  let name = $(this).parent('td').attr('data-name');

                  $('#companyId').val(id);
                  $('#company_name').val(name);
                  $('#frmSaveCompany').attr('action', "{{ route('company.update') }}");

                  $('#companyModal').modal('show');
                });

                $(document).on('click', '.btn-enable', function(){
                  let id = $(this).parent('td').attr('data-id');
                  let status = $(this).attr('data-status');
                  let message = status ? "Disable" : "Enable";

                  Swal.fire({
                      icon: 'warning',
                      text: ` Are you sure you want to ${message} this company?`,
                      showCancelButton: true,
                      confirmButtonText: "Save",
                    }).then((result) => {
                      if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('company.update-status') }}",
                            type: 'POST',
                            data: { id : id,
                                    status: status,
                                    _token: "{{ csrf_token() }}" },
                            dataType: 'json',
                            success: function (response){
                              popupMessage('Success', response.message, 'success' );
                              fetchContent();
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                popupMessage('Error', 'Failed!', 'error');
                            },
                            complete: function ( response ){
                              $.unblockUI();
                            }
                        })
                      } 
                  });
                })

                function fetchContent( page = 1) {
                  $.blockUI();

                  $.ajax({
                    url: "{{ route('company.fetch-content') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: { page : page },
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

                $('#companyModal').on('hidden.bs.modal', function (e) {
                  clearAllError();
                  ('#frmSaveCompany')[0].reset;
                })

              });
          </script>
      @endpush
</x-app-layout>