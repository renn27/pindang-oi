<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>data tim kerja</title>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>
<body>

<h3>data tim kerja</h3>

<table id="tim-kerja-table" class="display">
    <thead>
        <tr>
            <th>id</th>
            <th>nama tim</th>
            <th>id ketua</th>
            <th>dibuat</th>
            <th>aksi</th>
        </tr>
    </thead>
</table>

<script>
$(function () {
    $('#tim-kerja-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('tim-kerja.data') }}",
        columns: [
            { data: 'id_tim_kerja', name: 'id_tim_kerja' },
            { data: 'nama_tim', name: 'nama_tim' },
            { data: 'id_ketua', name: 'id_ketua' },
            { data: 'created_at', name: 'created_at' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ]
    });
});
</script>

</body>
</html>
