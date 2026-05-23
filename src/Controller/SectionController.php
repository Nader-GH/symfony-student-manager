<?php

namespace App\Controller;

class SectionController {
    
    public function list() {
        $search = $_GET['search'] ?? '';
        $dataFile = __DIR__ . '/../../data/sections.json';

        if (!file_exists($dataFile)) {
            return 'Data not initialized. Please run: php init_db.php';
        }

        // Load sections from JSON
        $json = file_get_contents($dataFile);
        $sections = json_decode($json, true) ?: [];

        // Filter by search term
        if (!empty($search)) {
            $sections = array_filter($sections, function($section) use ($search) {
                $searchLower = strtolower($search);
                return (
                    stripos($section['designation'], $search) !== false ||
                    stripos($section['level'], $search) !== false
                );
            });
            $sections = array_values($sections); // Re-index array
        }

        // Sort by designation
        usort($sections, function($a, $b) {
            return strcmp($a['designation'], $b['designation']);
        });

        // Render template
        ob_start();
        require __DIR__ . '/../../templates/section/list.html.twig.php';
        return ob_get_clean();
    }
}
