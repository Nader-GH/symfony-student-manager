<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Average Age per Section</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 0;
        }
        
        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            padding: 30px;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            border-left: 5px solid #667eea;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.2);
        }
        
        .stat-card h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 13px;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        .stat-value {
            color: #333;
            font-weight: 600;
        }
        
        .age-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .summary {
            padding: 30px;
            background: #f8f9fa;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        
        .summary-item-value {
            font-size: 28px;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .summary-item-label {
            color: #666;
            font-size: 13px;
            font-weight: 500;
        }
        
        .section-level {
            display: inline-block;
            background: #e7f3ff;
            color: #0066cc;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .no-students {
            color: #999;
            font-style: italic;
            font-size: 12px;
        }
        
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .chart-container h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
        }
        
        .bar-chart {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .bar-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .bar-label {
            min-width: 150px;
            font-size: 13px;
            font-weight: 500;
            color: #333;
        }
        
        .bar-value {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 30px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 10px;
            color: white;
            font-weight: 600;
            font-size: 12px;
            min-width: 50px;
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Statistics Dashboard</h1>
            <p class="subtitle">Average Age per Section Analysis</p>
        </div>
        
        <div class="summary">
            <h3 style="color: #333; margin-bottom: 15px;">Overall Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-item-value"><?php echo count($sections); ?></div>
                    <div class="summary-item-label">Total Sections</div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-value"><?php echo count($students); ?></div>
                    <div class="summary-item-label">Total Students</div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-value"><?php 
                        $totalAge = 0;
                        $countWithDob = 0;
                        $today = new \DateTime();
                        foreach ($students as $student) {
                            $dob = \DateTime::createFromFormat('Y-m-d', $student['date_of_birth']);
                            if ($dob) {
                                $age = $today->diff($dob)->y;
                                $totalAge += $age;
                                $countWithDob++;
                            }
                        }
                        echo $countWithDob > 0 ? round($totalAge / $countWithDob, 1) : 'N/A';
                    ?></div>
                    <div class="summary-item-label">Overall Avg Age</div>
                </div>
            </div>
        </div>
        
        <div class="chart-container">
            <h2>Average Age by Section</h2>
            <div class="bar-chart">
                <?php 
                // Find max average age for scaling
                $maxAge = max(array_map(function($s) { return $s['average_age']; }, $sectionStats)) ?: 25;
                ?>
                <?php foreach ($sectionStats as $stat): ?>
                    <div class="bar-item">
                        <div class="bar-label"><?php echo htmlspecialchars($stat['section']['designation']); ?></div>
                        <div class="bar-value" style="width: <?php echo ($stat['average_age'] / $maxAge * 100); ?>%;">
                            <?php echo $stat['average_age']; ?> years
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="stats-grid">
            <?php foreach ($sectionStats as $stat): ?>
                <div class="stat-card">
                    <h2>
                        <div class="section-icon"><?php echo htmlspecialchars($stat['section']['id']); ?></div>
                        <?php echo htmlspecialchars($stat['section']['designation']); ?>
                    </h2>
                    <div class="section-level"><?php echo htmlspecialchars($stat['section']['level']); ?></div>
                    
                    <div style="margin-top: 15px;">
                        <div class="stat-item">
                            <span class="stat-label">Students Enrolled</span>
                            <span class="stat-value"><?php echo $stat['student_count']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Average Age</span>
                            <span class="stat-value"><span class="age-badge"><?php echo $stat['average_age']; ?> yrs</span></span>
                        </div>
                        
                        <?php if ($stat['student_count'] > 0): ?>
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                <div style="font-weight: 600; color: #333; font-size: 12px; margin-bottom: 8px; text-transform: uppercase;">Students</div>
                                <?php foreach ($stat['students'] as $student): ?>
                                    <div style="font-size: 12px; color: #666; padding: 4px 0; border-bottom: 1px solid #f0f0f0;">
                                        <?php 
                                            $dob = \DateTime::createFromFormat('Y-m-d', $student['date_of_birth']);
                                            $age = $dob ? (new \DateTime())->diff($dob)->y : 'N/A';
                                        ?>
                                        <span style="font-weight: 500;"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
                                        <span style="float: right; color: #999;">Age: <?php echo $age; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-students">
                                No students enrolled
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
