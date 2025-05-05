<table class="table table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Raw Password</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                {{ $user->raw_password }}
                    <button class="btn btn-sm btn-outline-secondary btn-copy" data-text="{{ $user->raw_password }}">
                        Copy
                    </button>
                </td>
                <td>
                <span class="badge {{ $user->status ? 'bg-success' : 'bg-secondary' }}">
                    {{ $user->status ? 'Active' : 'Disabled' }}
                </span>
                </td>
                <td>
                <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $user->id }}">Edit</button>

                @if($user->id != 1)
                    <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $user->id }}">Delete</button>
                @endif

                <button class="btn btn-sm btn-secondary btn-toggle" data-id="{{ $user->id }}" data-status="{{ $user->status }}">
                    {{ $user->status ? 'Disable' : 'Enable' }}
                </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
  