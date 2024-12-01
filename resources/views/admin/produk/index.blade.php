@extends('admin.template.master')

@section('css')
     <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="{{ asset('') }}plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Modal -->
<div class="modal fade" id="modalTambahStok" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Tambah Stok</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form-tambah-stok" method="post">
      <div class="modal-body">
        <input type="hidden" name="id_produk" id="id_produk">
        <label for=""> Jumlah Produk </label>
        <input type="number" name="Stok" id="nilaiTambahStok" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div> 
      </form>
    </div>
  </div>
</div>

@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">{{ $title }}</a></li>
              <li class="breadcrumb-item active">{{ $subtitle }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $title }}</h3>
                <a href="{{ route('produk.create') }}" class="btn btn-primary float-right"><i class="fa-solid fas fa-plus"></i></a>
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            </div>
            <div class="card-body">
              <button type="button" class="btn btn-primary mb-2" id="btnCetakLabel">Cetak Label</button>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produks as $produk)
                            <tr>
                                <td>
                                  <div class="form-check">
                                    <input class="form-check-input" name="id_produk[]" type="checkbox" value="{{ $produk->id }}" id="id_produk_label">
                                    </label>
                                  </div>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $produk->NamaProduk }}</td>
                                <td>{{ rupiah($produk->Harga) }}</td>
                                <td>{{ $produk->Stok }}</td>
                                <td>
                                    <form id="form-delete-produk-{{ $produk->id }}" action="{{ route('produk.destroy', $produk->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-sm btn-primary"><i class="fa-solid fas fa-pen"></i></a>
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fas fa-trash"></i></button>
                                        {{-- <a href="{{ route('produk.editStok', ['id' => $produk->id]) }}" class="btn btn-sm btn-primary">Tambah Stok</a> --}}
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-sm btn-warning" id="btnTambahStok" data-toggle="modal" data-target="#modalTambahStok" data-id_produk="{{ $produk->id }}">
                                         <i class="fa-solid fas fa-plus"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
  <!-- DataTables  & Plugins -->
<script src="{{ asset('') }}plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="{{ asset('') }}plugins/jszip/jszip.min.js"></script>
<script src="{{ asset('') }}plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{ asset('') }}plugins/pdfmake/vfs_fonts.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="{{ asset('') }}plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Page specific script -->
<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
  </script>

<script>
  $('#form-delete-produk').submit(function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'Apakah anda yakin?',
      text: "Anda tidak akan dapat mengembalikan ini!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
      if (result.isConfirmed) {
        $(this).unbind().submit();
      }
    })
  });

  $(document).on('click', '#btnTambahStok', function() {
    let id_produk = $(this).data('id_produk');
    $('#id_produk').val(id_produk);  // Set the product ID in the hidden input
});

$(document).on('submit', '#form-tambah-stok', function(e) {
    e.preventDefault();

    // Get the product ID and stock value
    let id_produk = $('#id_produk').val();
    let stokValue = $('#nilaiTambahStok').val();

    // Data object to send
    let dataForm = {
        Stok: stokValue,
        _token: '{{ csrf_token() }}'
    };

    $.ajax({
        type: 'PUT',
        url: "{{ url('produk/edit') }}/" + id_produk + "/tambahStok",
        data: dataForm,
        dataType: 'json',
        success: function(data) {
            Swal.fire({
                icon: 'success',
                title: "Success",
                text: data.message,
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('produk.index') }}";
                }
            });
            $('#modalTambahStok').modal('hide');
            $('#form-tambah-stok')[0].reset();
        },
        error: function(data) {
            Swal.fire({
                icon: 'error',
                title: "Error",
                text: data.responseJSON?.message || 'An error occurred',
                confirmButtonText: "OK"
            });
        }
    });
});
</script>
<script>
  $(document).on('click', '#btnCetakLabel', function() {
    let id_produk = []; // Inisialisasi array untuk menyimpan ID produk yang dipilih
    $('input[name="id_produk[]"]:checked').each(function() {
      id_produk.push($(this).val()); // Menambahkan ID produk yang dipilih ke dalam array
    });

    // Memulai request AJAX untuk mencetak label produk
    $.ajax({
      type: 'POST',
      url: "{{ route('produk.cetakLabel') }}",
      data: {
        _token: '{{ csrf_token() }}', // Kirimkan token CSRF untuk keamanan
        id_produk: id_produk // Kirimkan array ID produk yang dipilih
      },
      dataType: 'json',
      success: function(data) {
        window.open(data.url, '_blank');
      },
      error: function(data) {
        // Menampilkan pesan error jika terjadi kesalahan pada server
        Swal.fire({
          icon: 'error',
          title: "Error",
          text: data.responseJSON?.message || 'Terjadi kesalahan.',
          confirmButtonText: "OK"
        });
      }
    });
  });
</script>

@endsection
