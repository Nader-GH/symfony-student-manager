<?php

// Create data directory if it doesn't exist
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$dataFile = $dataDir . '/sections.json';

// Sample sections data
$sections = [
    [
        'id' => 1,
        'designation' => '1st Year A',
        'level' => 'Year 1',
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 2,
        'designation' => '1st Year B',
        'level' => 'Year 1',
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 3,
        'designation' => '2nd Year A',
        'level' => 'Year 2',
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 4,
        'designation' => '2nd Year B',
        'level' => 'Year 2',
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 5,
        'designation' => '3rd Year A',
        'level' => 'Year 3',
        'created_at' => date('Y-m-d H:i:s')
    ],
];

// Write data to JSON file
file_put_contents($dataFile, json_encode($sections, JSON_PRETTY_PRINT));

echo "✓ Created data/sections.json\n";
echo "✓ Initialized " . count($sections) . " sample sections\n";
echo "\n✓ Database initialization complete!\n";
