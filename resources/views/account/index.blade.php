<x-app-layout>  
  <div class="content-wrapper">
    <div class="my-2">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="page-title"> Account Title Manager </h3>
        <button class="btn btn-primary align-self-end" id="btnAddAccount" data-bs-toggle="modal" data-bs-target="#accountModal">Add Account</button>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Code</th>
                    <th>Account Title</th>
                    <th>Sub Accounts</th>
                    <th>Status</th>
                    <th>Added By</th>
                    <th>Created</th>
                    <th>Updated By</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="accountTitleTable">
                  @foreach ($accountTitles as $d)
                    <tr>
                      <td>{{ $d->code }}</td>
                      <td>{{ $d->title }}</td>
                      <td class="text-start">
                        @foreach ($d->subs as $sub)
                          <div>- {{ $sub->name }} ({{ $sub->code }})</div>
                        @endforeach
                      </td>
                      <td>
                          @if($d->status)
                              <label class="badge badge-success">Active</label>
                          @else
                              <label class="badge badge-warning">Inactive</label>
                          @endif
                      </td>
                      <td>{{ $d->user->name ?? "-" }}</td>
                      <td>{{ $d->created_at->format('d F Y') }}</td>
                      <td>{{ $d->updatedBy->name ?? "-" }}</td>
                      <td data-id="{{ $d->id }}">
                        <button class="btn btn-sm btn-secondary btn-edit" 
                          data-id="{{ $d->id }}"
                        >
                          Edit
                        </button>
                        <button class="btn btn-sm btn-{{ $d->status ? 'danger': 'success' }} btn-enable" data-status="{{ $d->status ? 0 : 1 }}">
                          @if($d->status) Disable @else Enable @endif
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
              {{ $accountTitles->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Create Account Title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="accountForm">
          <div class="modal-body">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="accountId">

            <div class="mb-3">
              <label class="form-label">Account Title Name</label>
              <input type="text" class="form-control" name="title" id="accountTitleInput" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Account Code</label>
              <input type="text" class="form-control" name="code" id="accountCodeInput" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Sub Accounts</label>
              <div id="subAccounts"></div>
              <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btnAddSub">Add Sub Account</button>
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Edit Account Title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(document).ready(function() {

      // Open Modal for Create
      $('#btnAddAccount').on('click', function() {
        $('#accountModalLabel').text('Create Account Title');
        $('#accountForm')[0].reset();
        $('#accountId').val('');
        $('#formMethod').val('POST');
        $('#subAccounts').html('');
      });

      // Open Modal for Edit
      $(document).on('click', '.btn-edit', function() {
        let id    = $(this).data('id');

        $.blockUI();
        
        $.ajax({
          url: "{{ route('accounts.fetch-modal') }}",
          method: 'GET',
          data: {id: id},
          success: function(response) {
            $('#editAccountModal .modal-body').html(response.html);
            $('#editAccountModal').modal('show');
          },
          error: function(xhr) {
            console.log(xhr.responseText);
          }, 
          complete: function ( response ){
            $.unblockUI();
          }
        });
      });

      // Add New Sub Account
      $('#btnAddSub').on('click', function() {
        let index = $('#subAccounts .sub-account-item').length;
        $('#subAccounts').append(`
          <div class="d-flex mb-2 align-items-center sub-account-item">
            <input type="text" name="subs[${index}][name]" class="form-control me-2" placeholder="name" required>
            <input type="text" name="subs[${index}][code]" class="form-control me-2" placeholder="code" required>
            <button type="button" class="btn btn-danger btn-sm btn-remove-sub">x</button>
          </div>
        `);
      });

      $(document).on('click', '#btneditAddSub', function() {
        const currentTimeInSeconds = Math.floor(Date.now() / 1000);
          $('#editSubAccounts').append(`
            <div class="d-flex mb-2 align-items-center sub-account-item">
              <input type="text" name="subs[${currentTimeInSeconds}][name]" class="form-control me-2" placeholder="name" required>
              <input type="text" name="subs[${currentTimeInSeconds}][code]" class="form-control me-2" placeholder="code" required>
              <button type="button" class="btn btn-danger btn-sm btn-remove-sub">x</button>
            </div>
          `);
      });

      // Remove Sub Account
      $(document).on('click', '.btn-remove-sub', function() {
        $(this).closest('.sub-account-item').remove();
      });

      // Submit Form
      $(document).on('submit', '#accountForm', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        let id = $('#accountId').val();
        let url = id ? `/accounts/${id}/update` : '/accounts/store';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
          url: url,
          method: method,
          data: formData,
          success: function(response) {
            popupMessage('Success', response.message, 'success' );
            setTimeout(() => {
              location.reload();
            }, 2000);
          },
          error: function(xhr) {
            popupMessage('Error', 'Please make sure that the code is unique', 'error' );
          }
        });
      });

      $(document).on('submit', '#updateAccountForm', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        let id  = $('#accountId').val();
        let url = $(this).attr('action');

        $.ajax({
          url: url,
          method: 'POST',
          data: formData,
          success: function(response) {
            popupMessage('Success', response.message, 'success' );
            setTimeout(() => {
              location.reload();
            }, 2000);
          },
          error: function(xhr) {
            popupMessage('Error', 'Please make sure that the code is unique', 'error' );
          }
        });
      });

      $(document).on('click', '.btn-enable', function(){
        let id = $(this).parent('td').attr('data-id');
        let status = $(this).attr('data-status');
        let message = status ? "Disable" : "Enable";

        Swal.fire({
            icon: 'warning',
            text: ` Are you sure you want to ${message} this account title?`,
            showCancelButton: true,
            confirmButtonText: "Save",
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                  url: "{{ route('accounts.update-status') }}",
                  type: 'POST',
                  data: { id : id,
                          status: status,
                          _token: "{{ csrf_token() }}" },
                  dataType: 'json',
                  success: function (response){
                    popupMessage('Success', response.message, 'success' );
                    setTimeout(() => {
                      location.reload();
                    }, 2000);
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
});

  </script>
  @endpush
</x-app-layout>
