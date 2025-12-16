<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kategóriák listája</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
        }
        
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        header h1 {
            margin: 0;
            padding: 10px 0;
            font-size: 18pt;
            color: #333;
        }
        
        header .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 9pt;
            color: #666;
        }
        
        footer .page-number:after {
            content: counter(page);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #2563eb;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .export-info {
            text-align: right;
            font-size: 9pt;
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">📚 Library System</div>
        <h1>Kategóriák listája</h1>
    </header>
    
    <footer>
        <div>
            Library System - Könyvtár Kezelő Rendszer
        </div>
        <div>
            Oldal: <span class="page-number"></span>
        </div>
    </footer>
    
    <main>
        <div class="export-info">
            Exportálás dátuma: {{ $exportDate }}
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
