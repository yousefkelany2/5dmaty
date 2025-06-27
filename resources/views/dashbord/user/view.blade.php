@extends('dashbord.layout.main');
@section('content')
<a href= "{{ route("user.create") }}" class="btn btn-success  mb-3">Add user</a>
<div class="card">
    <div class="card-body">
      <h4 class="card-title">user table</h4>
      </p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th> # </th>
            <th> Name </th>
            <th> Phone </th>
            <th> Email </th>

            <th>Edit/Delete</th>
          </tr>
        </thead>
        <tbody>
        @forelse ($User as $key => $user)
        <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->email }}</td>


            <td class=" d-flex gap-2">
                <a href="{{ route("user.show",$user->id) }}" class="btn btn-info ">Update</a>

                <form action="{{ route("user.destroy",$user->id) }}" method="POST">
                    @csrf
                    @method("DELETE")
                    <input type="submit" value="Delete" class="btn btn-danger">

                </form>
            </td>
        </tr>

        @empty

        @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
