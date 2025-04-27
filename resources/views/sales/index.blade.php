<x-app-layout>  
    <div class="content-wrapper">

        <div class="my-2">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title"> Sales Manager </h3>
            <button class="btn btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Sale Category</button>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 ">
            <div class="card">
              <div class="card-body">
                <table class="table text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Created</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>11112</td>
                      <td>Sale category a</td>
                      <td>12 April 2025</td>
                      <td><label class="badge badge-success">Active</label></td>
                      <td>
                        <button class="btn btn-sm btn-secondary btn-edit">
                            Edit
                        </button>
                        |
                        <button class="btn btn-sm btn-danger btn-edit">
                          Delete
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>11113</td>
                      <td>Sale category b</td>
                      <td>15 April 2025</td>
                      <td><label class="badge badge-success">Active</label></td>
                      <td>
                        <button class="btn btn-sm btn-secondary btn-edit">
                          Edit
                        </button>
                        |
                        <button class="btn btn-sm btn-danger btn-edit">
                          Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Create Sale Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form action="">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            
          </div>
        </div>
      </div>
</x-app-layout>