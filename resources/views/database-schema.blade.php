<!DOCTYPE html>
<html>
<head>
    <title>Database Schema</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Database Schema</h1>

    @foreach ($schema as $table => $columns)
        <h2>Table: {{ $table }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Column Name</th>
                    <th>Data Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($columns as $column)
                    <tr>
                        <td>{{ $column['name'] }}</td>
                        <td>{{ $column['type'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
