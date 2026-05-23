<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
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
            max-width: 1000px;
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
        
        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: white;
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
        
        .section-badge {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student List</h1>
        
        <form method="GET" class="filter-form">
            <select name="section" id="sectionFilter">
                <option value="">All Sections</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?php echo htmlspecialchars($section['id']); ?>" 
                        <?php echo ($sectionFilter === (string)$section['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($section['designation']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
            <?php if (!empty($sectionFilter)): ?>
                <a href="?"><button type="button" class="clear-btn">Clear Filter</button></a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($students)): ?>
            <div class="results-info">
                Found <?php echo count($students); ?> student<?php echo count($students) !== 1 ? 's' : ''; ?>
                <?php if (!empty($sectionFilter)): 
                    $selectedSection = array_column($sections, null, 'id')[$sectionFilter] ?? null;
                    if ($selectedSection):
                ?>
                    in "<?php echo htmlspecialchars($selectedSection['designation']); ?>"
                <?php endif; endif; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Section</th>
                        <th>Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): 
                        $section = null;
                        if (!empty($student['section_id'])) {
                            $section = array_column($sections, null, 'id')[$student['section_id']] ?? null;
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['phone']); ?></td>
                            <td>
                                <?php if ($section): ?>
                                    <span class="badge section-badge"><?php echo htmlspecialchars($section['designation']); ?></span>
                                <?php else: ?>
                                    <span style="color: #ccc;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">
                <?php if (!empty($sectionFilter)): ?>
                    No students found in this section
                <?php else: ?>
                    No students available
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
