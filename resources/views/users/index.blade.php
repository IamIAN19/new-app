<x-app-layout>  
    <div class="content-wrapper">
      <div class="my-2">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="page-title">User Manager</h3>
          <button class="btn btn-primary align-self-end btn-show">Add User</button>
        </div>
      </div>
  
      <div class="row">
        <div class="col-md-12 ">
          <div class="card">
            <div class="card-body table-body">
            </div>
          </div>
        </div>
      </div>
    </div>
  
    <!-- Modal -->
    <div class="modal fade" id="UserModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title" id="userModalLabel">Create User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body">
            <form id="frmSaveUser">
                @csrf
                <input type="hidden" name="id" id="userId">
            
                <div class="form-group mb-2">
                    <label for="">Name</label>
                    <input type="text" class="form-control" name="name" id="user_name" required>
                    <span class="error text-danger mt-1"></span>
                </div>
            
                <div class="form-group mb-2">
                    <label for="">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                    <span class="error text-danger mt-1"></span>
                </div>

                <div class="form-group mb-2">
                    <label>Password</label>
                    <input type="text" class="form-control" name="raw_password" id="raw_password" required>
                    <span class="error text-danger mt-1"></span>
                </div>
            </form>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" form="frmSaveUser" class="btn btn-primary">Save</button>
          </div>
          
        </div>
      </div>
    </div>
  
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      $(document).ready(function() {
        fetchUsers();
  
        $(document).on('click', '.btn-show', function() {
          $('#UserModalLabel').text('Create User');
          $('#frmSaveUser').attr('action', "{{ url('/users/store') }}");
          $('#frmSaveUser')[0].reset();
          $('#userId').val('');
          $('#UserModal').modal('show');
        });
  
        $(document).on('click', '.btn-edit', function() {
          let id = $(this).data('id');
          $.get('/users/edit/' + id, function(user) {
            $('#UserModalLabel').text('Edit User');
            $('#frmSaveUser').attr('action', '/users/update/' + user.id);
            $('#userId').val(user.id);
            $('#email').val(user.email);
            $('#user_name').val(user.name);
            $('#raw_password').val(user.raw_password);
            $('#UserModal').modal('show');
          });
        });
  
        $(document).on('click', '.btn-delete', function() {
          let id = $(this).data('id');
          Swal.fire({
            icon: 'warning',
            text: 'Are you sure you want to delete this user?',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: '/users/delete/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                  fetchUsers();
                  Swal.fire('Deleted!', 'User deleted.', 'success');
                }
              });
            }
          });
        });
  
        $(document).on('click', '.btn-toggle', function() {
          let id = $(this).data('id');
          let status = $(this).data('status');
          let message = status ? "Disable" : "Enable";
          Swal.fire({
            icon: 'question',
            text: `Are you sure you want to ${message} this user?`,
            showCancelButton: true,
            confirmButtonText: 'Yes'
          }).then((result) => {
            if (result.isConfirmed) {
              $.post('/users/toggle-status/' + id, { _token: '{{ csrf_token() }}' }, function() {
                fetchUsers();
              });
            }
          });
        });
  
        $(document).on('click', '.btn-copy', function() {
          let passField = $('#raw_password')[0];
          passField.select();
          document.execCommand('copy');
        });
  
        $(document).on('submit', '#frmSaveUser', function(e) {
          e.preventDefault();
          let url = $(this).attr('action');
          let data = $(this).serialize();
          clearAllError();
  
          $.post(url, data, function() {
            $('#UserModal').modal('hide');
            fetchUsers();
            Swal.fire('Saved!', 'User saved successfully.', 'success');
          }).fail(function(xhr) {
            if (xhr.status == 422) {
              let errors = xhr.responseJSON.errors;
              $.each(errors, function(i, msg) {
                $(`[name="${i}"]`).next('.error').html(`*${msg}`);
              });
            } else {
              Swal.fire('Error', 'Something went wrong!', 'error');
            }
          });
        });

        $(document).on('click', '.btn-copy', function () {
            let text = $(this).data('text');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text)
                .then(() => {
                    Swal.fire('Copied!', 'Password copied to clipboard.', 'success');
                })
                .catch(() => {
                    fallbackCopyText(text);
                });
            } else {
                fallbackCopyText(text);
            }

            function fallbackCopyText(text) {
                const tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(text).select();
                document.execCommand('copy');
                tempInput.remove();
                Swal.fire('Copied!', 'Password copied to clipboard.', 'success');
            }
        });
  
        function clearAllError() {
          $('.error').html('');
        }
  
        function fetchUsers() {
          $.get("{{ url('/users/list') }}", function(data) {
            $('.table-body').html(data);
          });
        }
  
        $('#UserModal').on('hidden.bs.modal', function () {
          clearAllError();
        });
      });
    </script>
    @endpush
  </x-app-layout>
  