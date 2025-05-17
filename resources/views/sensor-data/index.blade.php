<!DOCTYPE html>
<html>
<head>
    <title>Sensor Data</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <h1>Sensor Data (Latest 100)</h1>
    <table>
<thead>
    <tr>
        <th>ID</th>
        <th>Time</th>
        <th>Device</th>
        <th>Temp (°C)</th>
        <th>Temp (°F)</th>
        <th>Humidity (%)</th>
        <th>Heat Index (°F)</th>
        <th>SID</th>
    </tr>
</thead>
<tbody>
    @forelse ($data as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->created_at }}</td>
            <td>{{ $row->device_id }}</td>
            <td>{{ $row->temperature }}</td>
            <td>{{ $row->temperature_f }}</td>
            <td>{{ $row->humidity }}</td>
            <td>{{ $row->heat_index_f }}</td>
            <td>{{ $row->sid }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8">No data found.</td>
        </tr>
    @endforelse
</tbody>
    </table>
</body>
</html>