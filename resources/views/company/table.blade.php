<div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Added By</th>
            <th>Created</th>
            <th>Updated By</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->user->name ?? "-" }}</td>
                    <td>{{  \Carbon\Carbon::parse($d->created_at)->format('Y F G') }}</td>
                    <td>{{ $d->updatedBy->name ?? "-" }}</td>
                    <td>
                        @if($d->status)
                            <label class="badge badge-success">Active</label>
                        @else
                            <label class="badge badge-warning">Inactive</label>
                        @endif
                    </td>
                    <td data-id="{{ $d->id }}" data-name="{{ $d->name }}">

                        @hasPermission('edit')
                          <button class="btn btn-sm btn-secondary btn-edit">
                            Edit
                            </button>
                        @endhasPermission
                        
                        @hasPermission('delete')
                            <button class="btn btn-sm btn-{{ $d->status ? 'danger': 'success' }} btn-enable" data-status="{{ $d->status ? 0 : 1 }}">
                                @if($d->status) Disable @else Enable @endif
                            </button>
                        @endhasPermission
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
  <div class="d-flex justify-content-end mt-5">
    {{ $data->links() }}
  </div>
  @if($data->count() < 0)
    <h5 class="text-center mt-3 mb-0">No available data</h5>
  @endif
