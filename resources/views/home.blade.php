@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        <div class="mt-3">
                            <h5>{{ __('Create New Client') }}</h5>
                            <form id="createClientForm" method="POST" action="{{ route('clients.create') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">{{ __('Client Name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="redirect">{{ __('Redirect URI') }}</label>
                                    <input type="url" class="form-control" id="redirect" name="redirect" required>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ __('Create Client') }}</button>
                            </form>
                        </div>

                        <div class="card-body mt-3">
                            <h5>{{ __('List Client') }}</h5>
                            <table id="clientTable" class="table table-responsive text-center">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Secret</th>
                                        <th>Provider</th>
                                        <th>Redirect</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Client -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Client') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editClientForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editClientId">
                        <div class="form-group">
                            <label for="editName">{{ __('Client Name') }}</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="editRedirect">{{ __('Redirect URI') }}</label>
                            <input type="url" class="form-control" id="editRedirect" name="redirect" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#clientTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('clients.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'secret',
                        name: 'secret'
                    },
                    {
                        data: 'provider',
                        name: 'provider'
                    },
                    {
                        data: 'redirect',
                        name: 'redirect'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            // Event handler untuk tombol delete dengan SweetAlert
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var clientId = $(this).data('id'); // Dapatkan ID client dari tombol delete

                // Tampilkan SweetAlert untuk konfirmasi penghapusan
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/clients/' + clientId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}' // Kirimkan token CSRF
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                $('#clientTable').DataTable().ajax
                                    .reload(); // Reload tabel DataTables setelah penghapusan berhasil
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting the client.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Event handler untuk tombol edit
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                var clientId = $(this).data('id'); // Dapatkan ID client dari tombol edit
                $.ajax({
                    url: '/clients/' + clientId + '/edit',
                    type: 'GET',
                    success: function(data) {
                        $('#editClientId').val(data
                            .id); // Isi form dengan data yang didapat dari server
                        $('#editName').val(data.name);
                        $('#editRedirect').val(data.redirect);
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while fetching the client data.',
                            'error'
                        );
                    }
                });
            });

            // Submit form edit dengan SweetAlert
            $('#editClientForm').on('submit', function(e) {
                e.preventDefault();
                var clientId = $('#editClientId').val();
                $.ajax({
                    url: '/clients/' + clientId,
                    type: 'PUT',
                    data: $(this).serialize(), // Kirimkan data dari form
                    success: function(response) {
                        Swal.fire(
                            'Updated!',
                            response.message,
                            'success'
                        );
                        $('#editModal').modal('hide');
                        $('#clientTable').DataTable().ajax
                            .reload(); // Reload tabel setelah pengeditan berhasil
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while updating the client.',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
@endpush
