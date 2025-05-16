<x-app-layout>  
    @push('styles')
      <style>
        select{
          color: #000000 !important;
        }
      </style>
    @endpush
    <div class="content-wrapper">

        <div class="my-2">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title">Supplier Manager </h3>
              @hasPermission('add')
                <button class="btn btn-primary align-self-end btn-show">Add Supplier</button>
              @endhasPermission
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 ">
            <div class="card">
              <div class="card-body table-body">
                @include('supplier.table')
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Create Supplier</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form action="" id="frmSupplier">
                    @csrf
                    <input type="hidden" name="id" id="supplierId">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" required>
                        <span class="error text-danger mt-1"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Tin</label>
                        <input type="text" class="form-control" name="tin" required>
                        <span class="error text-danger mt-1"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Classification</label>
                        <select name="classification" id="" class="form-control p-3" required>
                            <option value="">Choose one</option>
                            <option value="vat">Vat</option>
                            <option value="non_vat">Non Vat</option>
                        </select>
                        <span class="error text-danger mt-1"></span>
                    </div>
                    <div class="form-group">
                        <label for="">Address</label>
                        <input type="text" class="form-control" name="address" required>
                        <span class="error text-danger mt-1"></span>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" form="frmSupplier" class="btn btn-primary">Save changes</button>
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
              $('#frmSupplier').attr('action', "{{ route('supplier.store') }}");
              $('#supplierModal').modal('show');
            })

            $(document).on('click', '.btn-edit', function(){
              let id   = $(this).parent('td').attr('data-id');
              let name = $(this).parent('td').attr('data-name');
              let tin  = $(this).parent('td').attr('data-tin');
              let address = $(this).parent('td').attr('data-address');
              let classification = $(this).parent('td').attr('data-classification');

              $('#frmSupplier').find('input[name="id"]').val(id);
              $('#frmSupplier').find('input[name="name"]').val(name);
              $('#frmSupplier').find('input[name="tin"]').val(tin);
              $('#frmSupplier').find('input[name="address"]').val(address);
              $('#frmSupplier').find('select').val(classification);
              

              $('#frmSupplier').attr('action', "{{ route('supplier.update') }}");

              $('#supplierModal').modal('show');
            });

            $(document).on('click', '.btn-delete', function(){

              let id= $(this).parent('td').attr('data-id');

              Swal.fire({
                    icon: 'warning',
                    text: ` Are you sure you want to remove this supplier?`,
                    showCancelButton: true,
                    confirmButtonText: "Save",
                  }).then((result) => {
                    if (result.isConfirmed) {
                      $.ajax({
                          url: "{{ route('supplier.remove') }}",
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

            $(document).on('submit', '#frmSupplier', function(e){
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
                    $('#supplierModal').modal('hide');
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

            function fetchContent(page = 1) {
              $.blockUI();

              $.ajax({
                url: "{{ route('supplier.fetch-content') }}",
                type: 'GET',
                data: {page : page },
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

            $('#frmSupplier').on('hidden.bs.modal', function (e) {
              clearAllError();
              ('#frmSaveSalesCategory')[0].reset;
            })

          });
      </script>
    @endpush
</x-app-layout>