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
                <th>Unit</th>
                <th>Temp (°C)</th>
                <th>Humidity (%)</th>
                <th>Relay 1</th>
                <th>Relay 2</th>
                <th>SID</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->unit }}</td>
                    <td>{{ $row->temp }}</td>
                    <td>{{ $row->hum }}</td>
                    <td>{{ $row->relay1 }}</td>
                    <td>{{ $row->relay2 }}</td>
                    <td>{{ $row->sid }}</td>
                    <td>{{ $row->created_at }}</td>
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