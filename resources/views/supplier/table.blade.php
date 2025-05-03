<div class="table-responsive">
  <table class="table table-bordered text-center align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>TIN#</th>
        <th>Address</th>
        <th>Added By</th>
        <th>Created</th>
        <th>Updated By</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->tin }}</td>
                <td>{{ $d->address }}</td>
                <td>{{ $d->user->name ?? "-" }}</td>
                <td>{{  \Carbon\Carbon::parse($d->created_at)->format('Y F G') }}</td>
                <td>{{ $d->updatedBy->name ?? "-" }}</td>
                <td data-id="{{ $d->id }}" data-name="{{ $d->name }}"  data-tin="{{ $d->tin }}"  data-address="{{ $d->address }}" data-classification="{{ $d->classification }}">
                    <button class="btn btn-sm btn-secondary btn-edit">
                        Edit
                    </button>
                    |
                    <button class="btn btn-sm btn-danger btn-delete">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
</div>
  <div class="d-flex justify-content-end mt-5">
    {{ $data->links() }}
  </div>