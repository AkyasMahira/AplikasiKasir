@extends('admin.template.master')

@section('css')
     <!-- DataTables -->
 
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
                <a href="{{ route('produk.index') }}" class="btn btn-warning float-right">Kembali</a>
            </div>
            <div class="card-body">
               <form id="form-tambah-stok" action="{{ route('produk.updateStok', $produk->id) }}" method="POST">
                @csrf
                @method('PUT')
                <label for="">Nama Produk</label>
                <input type="text" name="NamaProduk" value="{{ $produk->NamaProduk }}" class="form-control" readonly>
                <label for="">Stok</label>
                <input type="number" name="Harga" value="{{ $produk->Stok }}" class="form-control" readonly>
                <label for="">Tambahkan Stok</label>
                <input type="number" name="Stok" class="form-control" required>
                <button class="btn btn-primary mt-2" type="submit">Tambah Stok</button>
               </form>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
  <script>
    $(document).ready(function(){
      $('#form-tambah-stok').submit(function(e){
        e.preventDefault();
        let dataForm = $(this).serialize();

        $.ajax({
          type: 'PUT',
          url: $(this).attr('action'),
          data: dataForm,
          dataType: 'json',
          success: function(data){
            Swal.fire({
            icon: 'success',
            title: "Success",
            text: data.message,
            confirmButtonText: "OK"
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "{{ route('produk.index') }}";
            }
          })
          },
          error:function(data){
            console.log(data.message);
            Swal.fire({
            icon: 'error',
            title: "Error",
            text: data.message,
            confirmButtonText: "OK"
          })
          if(data.status == 500){
            Swal.fire({
            icon: 'error',
            title: "Error",
            text: data.responseJSON.message,
            confirmButtonText: "OK"
          })
          }
        }
      });
      });
    });  
  </script>
@endsection
