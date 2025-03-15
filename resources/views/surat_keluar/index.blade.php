@extends('layouts.main')
@section('title', __('Surat Keluar'))
@section('custom-css')
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    @auth
                        {{-- @if (Auth::user()->role == 0 || Auth::user()->role == 6) --}}
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-suratkeluar"
                            onclick="addSuratKeluar()"><i class="fas fa-plus"></i> Add New Surat Keluar</button>
                        {{-- @endif --}}
                    @endauth
                    <div class="card-tools">
                        <form>
                            <div class="input-group input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="font-bold">*Wajib Upload Terlebih Dahulu Surat Keluar Yang Telah Dibuat</p>
                <div class="card-body">

                    {{-- Filter by Nomor Po dan Tanggal --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter-po-no">Filter Nomor Surat Keluar</label>
                                <input type="text" class="form-control" id="filter-po-no"
                                    placeholder="Masukkan Nomor PO">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter-po-date">Filter Tanggal Surat Keluar</label>
                                <input type="date" class="form-control" id="filter-po-date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-secondary mt-4" id="clear-filter">Clear Filter</button>
                        </div>
                    </div>
                    {{-- End Filter by Nomor Po dan Tanggal --}}

                    
                    <table id="table" class="table table-sm table-bordered table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th><input type="checkbox" id="select-all"></th>
                                <th>No.</th>
                                <th>{{ __('Tanggal') }}</th>
                                <th>{{ __('Nomor') }}</th>
                                <th>{{ __('Jenis') }}</th>
                                <th>{{ __('Tujuan') }}</th>
                                <th>{{ __('Uraian') }}</th>
                                <th>{{ __('Arsip Elektronik') }}</th>
                                <th>{{ __('File') }}</th>
                                <th>{{ __('PIC') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $key => $d)
                                @php
                                    $data = $d->toArray();
                                @endphp
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="hapus[]"
                                        value="{{ $d->id }}"></td>
                                    <td class="text-center">{{ $items->firstItem() + $key }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($data['created_at'])->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if ($data['type'] == 0)
                                            SK-{{ $data['no_surat'] }}
                                        @elseif ($data['type'] == 1)
                                            PI-{{ $data['no_surat'] }}
                                        @elseif ($data['type'] == 2)
                                            M-{{ $data['no_surat'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data['type'] == 0)
                                            Surat Kuasa
                                        @elseif ($data['type'] == 1)
                                            Pakta Integritas
                                        @elseif ($data['type'] == 2)
                                            Memo
                                        @endif
                                    </td>
                                    <td>{{ $data['tujuan'] }}</td>
                                    <td>{{ $data['uraian'] }}</td>
                                    <td class="text-center">
                                        {{ $data['status'] == 0 ? '' : ($data['status'] == 1 ? 'Ada' : 'CANCEL') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($data['file'] == null)
                                            -
                                        @else
                                            @if ($data['id_user'] == Auth::user()->id)
                                                <a href="{{ asset('sk/' . $data['file']) }}" target="_blank">
                                                    Download
                                                </a>
                                            @else
                                                Ada
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $data['pic'] }}</td>
                                    <td class="text-center">
                                        {{-- @if (Auth::user()->role == 0 || Auth::user()->role == 6) --}}
                                        @if (Auth::user()->id == $data['id_user'])
                                            <button title="Edit Shelf" type="button" class="btn btn-success btn-xs"
                                                data-toggle="modal" data-target="#add-suratkeluar"
                                                onclick="editSuratKeluar({{ json_encode($data) }})"><i
                                                    class="fas fa-edit"></i></button>
                                            <button title="Hapus Produk" type="button" class="btn btn-danger btn-xs"
                                                data-toggle="modal" data-target="#delete-suratkeluar"
                                                onclick="deleteSuratKeluar({{ json_encode($data) }})"><i
                                                    class="fas fa-trash"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="8">{{ __('No data.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-danger" id="delete-selected"
                            data-token="{{ csrf_token() }}">Hapus yang dipilih</button>
                </div>
            </div>
            <div>
                {{ $items->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @auth

            {{-- @if (Auth::user()->role == 0 || Auth::user()->role == 6) --}}
            <div class="modal fade" id="add-suratkeluar">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 id="modal-title" class="modal-title">{{ __('Add New Surat Keluar') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form role="form" id="save" action="{{ route('surat_keluar.save') }}" method="post"
                                enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                <input type="hidden" id="surat_keluar_id" name="surat_keluar_id">
                                <div class="form-group row">
                                    <label for="tanggal" class="col-sm-4 col-form-label">{{ __('Tanggal') }}</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="tanggal" name="tanggal">
                                    </div>
                                </div>
                                {{-- select option --}}
                                <div class="form-group row">
                                    <label for="direksi" class="col-sm-4 col-form-label">{{ __('Direksi') }}</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="direksi" name="direksi">
                                            <option value="d1">D1</option>
                                            <option value="d2">D2</option>
                                            <option value="d3">D3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="type" class="col-sm-4 col-form-label">{{ __('Jenis Surat') }}</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="type" name="type">
                                            <option value="0">Surat Kuasa</option>
                                            <option value="1">Pakta Integritas</option>
                                            <option value="2">Memo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tujuan" class="col-sm-4 col-form-label">{{ __('Tujuan') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="tujuan" name="tujuan">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="uraian" class="col-sm-4 col-form-label">{{ __('Keterangan') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="uraian" name="uraian">
                                    </div>
                                </div>
                                {{-- <div class="form-group row">
                                        <label for="file" class="col-sm-4 col-form-label">{{ __('File') }}</label>
                                        <div class="col-sm-8">
                                            <input type="file" class="" id="file" name="file">
                                        </div>
                                    </div> --}}
                            </form>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button id="button-save" type="button" class="btn btn-primary"
                                onclick="$('#save').submit();">{{ __('Add') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="delete-suratkeluar">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 id="modal-title" class="modal-title">{{ __('Delete Surat Keluar') }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form role="form" id="delete" action="{{ route('surat_keluar.delete') }}" method="post">
                                @csrf
                                @method('delete')
                                <input type="hidden" id="delete_id" name="delete_id">
                            </form>
                            <div>
                                <p>Anda yakin ingin menghapus surat keluar nomor <span id="delete_name"
                                        class="font-weight-bold"></span>?</p>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
                            <button id="button-save" type="button" class="btn btn-danger"
                                onclick="$('#delete').submit();">{{ __('Ya, hapus') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- @endif --}}
        @endauth
    </section>
@endsection
@section('custom-js')
    <script>
        $(document).ready(function() {
            $("#nomor").inputmask({
                "mask": "999/EDP-FJ/99/9999",
            });
        });

        function resetForm() {
            $('#save').trigger("reset");
            $('#surat_keluar_id').val('');
            $('#tanggal').val('');
            $('#nomor').val('');
            $('#keterangan').val('');
            $('#file').val('');
        }

        //checkbox
        $('#select-all').change(function() {
            var checkboxes = $(this).closest('table').find(':checkbox');
            checkboxes.prop('checked', $(this).is(':checked'));
        });

        // Function to handle delete selected items
        $('#delete-selected').click(function() {
            var ids = [];
            $('input[name="hapus[]"]:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                var token = $(this).data('token');
                $.ajax({
                    url: 'suratkeluar-imss/hapus-multiple',
                    type: 'POST',
                    data: {
                        _token: token,
                        ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            // Menghapus status checked dari semua checkbox
                            $('input[name="hapus[]"]').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            // Memuat ulang halaman setelah berhasil menghapus data
                            location.reload();
                            alert('Data berhasil dihapus');
                        } else {
                            alert('Gagal menghapus data');
                        }
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menghapus data');
                    }
                });
            } else {
                alert('Pilih setidaknya satu item untuk dihapus');
            }
        });
        //End CheckBox


        //Filter by Nomor dan tgl PO
        $(document).ready(function() {
            $('#clear-filter').on('click', function() {
                $('#filter-po-no, #filter-po-date').val('');
                filterTable();
            });

            $('#filter-po-no, #filter-po-date').on('keyup change', function() {
                filterTable();
            });

            function filterTable() {
                var filterNoPO = $('#filter-po-no').val().toUpperCase();
                var filterDatePO = $('#filter-po-date').val();

                $('table tbody tr').each(function() {
                    var noPO = $(this).find('td:nth-child(4)').text().toUpperCase();
                    var datePO = $(this).find('td:nth-child(3)').text();
                    var id = $(this).find('td:nth-child(1)')
                .text(); // Ubah indeks kolom ke indeks ID PO jika perlu

                    // Ubah string tanggal ke objek Date untuk perbandingan
                    var dateParts = datePO.split("/");
                    var poDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[
                    0]); // Format: tahun, bulan, tanggal

                    // Ubah string filterDatePO ke objek Date
                    var filterDateParts = filterDatePO.split("-");
                    var filterPODate = new Date(filterDateParts[0], filterDateParts[1] - 1, filterDateParts[
                        2]); // Format: tahun, bulan, tanggal

                    if ((noPO.indexOf(filterNoPO) > -1 || filterNoPO === '') &&
                        (poDate.getTime() === filterPODate.getTime() || filterDatePO === '')) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
        //End Filter by Nomor dan tgl PO



        function addSuratKeluar() {
            resetForm();
            $('#modal-title').text("Add New Surat Keluar");
            $('#button-save').text("Add");
        }

        function editSuratKeluar(data) {
            console.log(data)
            resetForm();
            $('#modal-title').text("Edit Surat Keluar");
            $('#button-save').text("Simpan");
            $('#surat_keluar_id').val(data.id);
            //change date format to yyyy-mm-dd
            var date = new Date(data.created_at);
            date = date.toISOString().substr(0, 10);
            $('#tanggal').val(date);
            //type select option
            $('#type').val(data.type);
            $('#tujuan').val(data.tujuan);
            $('#no_surat').val(data.no_surat);
            $('#uraian').val(data.uraian);
            //append file input to form id save in latest div
            // <div class="form-group row">
            //                             <label for="file" class="col-sm-4 col-form-label">{{ __('File') }}</label>
            //                             <div class="col-sm-8">
            //                                 <input type="file" class="" id="file" name="file">
            //                             </div>
            //                         </div>
            $('#save').append(
                '<div class="form-group row">' +
                '<label for="file" class="col-sm-4 col-form-label">File</label>' +
                '<div class="col-sm-8">' +
                '<input type="file" class="" id="file" name="file">' +
                '</div>' +
                '</div>'
            );

        }

        function deleteSuratKeluar(data) {
            $('#delete_id').val(data.id);
            $('#delete_name').text(data.no_surat);
        }
    </script>
    <script src="/plugins/toastr/toastr.min.js"></script>
    @if (Session::has('success'))
        <script>
            toastr.success('{!! Session::get('success') !!}');
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            toastr.error('{!! Session::get('error') !!}');
        </script>
    @endif
    @if (!empty($errors->all()))
        <script>
            toastr.error('{!! implode('', $errors->all('<li>:message</li>')) !!}');
        </script>
    @endif
@endsection
