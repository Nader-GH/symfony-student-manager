<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Search</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        input[type="text"] {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        button {
            padding: 10px 25px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        
        button:hover {
            background: #0052a3;
        }
        
        .clear-btn {
            background: #6c757d;
        }
        
        .clear-btn:hover {
            background: #5a6268;
        }
        
        .results-info {
            color: #666;
            font-size: 13px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            color: #555;
        }
        
        tbody tr:hover {
            background: #f9f9f9;
        }
        
        .no-results {
            text-align: center;
            color: #999;
            padding: 40px 20px;
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: #e7f3ff;
            color: #0066cc;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Section Search</h1>
        
        <form method="GET" class="search-form">
            <input 
                type="text" 
                name="search" 
                placeholder="Search by designation or level..." 
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
            >
            <button type="submit">Search</button>
            <?php if (!empty($_GET['search'])): ?>
                <a href="?"><button type="button" class="clear-btn">Clear</button></a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($sections)): ?>
            <div class="results-info">
                Found <?php echo count($sections); ?> section<?php echo count($sections) !== 1 ? 's' : ''; ?>
                <?php if (!empty($_GET['search'])): ?>
                    for "<?php echo htmlspecialchars($_GET['search']); ?>"
                <?php endif; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Designation</th>
                        <th>Level</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($section['id']); ?></td>
                            <td><?php echo htmlspecialchars($section['designation']); ?></td>
                            <td>
                                <?php if (!empty($section['level'])): ?>
                                    <span class="badge"><?php echo htmlspecialchars($section['level']); ?></span>
                                <?php else: ?>
                                    <span style="color: #ccc;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($section['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">
                <?php if (!empty($_GET['search'])): ?>
                    No sections found matching "<?php echo htmlspecialchars($_GET['search']); ?>"
                <?php else: ?>
                    No sections available
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
