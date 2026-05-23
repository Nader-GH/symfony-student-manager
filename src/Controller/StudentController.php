<?php

namespace App\Controller;

class StudentController {
    
    public function list() {
        $sectionFilter = $_GET['section'] ?? '';
        $searchTerm = $_GET['search'] ?? '';
        $dataFile = __DIR__ . '/../../data/students.json';
        $sectionsFile = __DIR__ . '/../../data/sections.json';

        if (!file_exists($dataFile)) {
            return 'Student data not initialized. Please run: php init_db.php';
        }

        // Load students from JSON
        $json = file_get_contents($dataFile);
        $students = json_decode($json, true) ?: [];

        // Load sections for reference
        $sections = [];
        if (file_exists($sectionsFile)) {
            $json = file_get_contents($sectionsFile);
            $sections = json_decode($json, true) ?: [];
        }

        // Filter by section if specified
        if (!empty($sectionFilter)) {
            $students = array_filter($students, function($student) use ($sectionFilter) {
                return (string)$student['section_id'] === (string)$sectionFilter;
            });
            $students = array_values($students); // Re-index array
        }

        // Filter by search term if specified
        if (!empty($searchTerm)) {
            $students = array_filter($students, function($student) use ($searchTerm) {
                return (
                    stripos($student['first_name'], $searchTerm) !== false ||
                    stripos($student['last_name'], $searchTerm) !== false ||
                    stripos($student['email'], $searchTerm) !== false
                );
            });
            $students = array_values($students); // Re-index array
        }

        // Sort by last name, then first name
        usort($students, function($a, $b) {
            $cmp = strcmp($a['last_name'], $b['last_name']);
            return $cmp !== 0 ? $cmp : strcmp($a['first_name'], $b['first_name']);
        });

        // Render template
        ob_start();
        require __DIR__ . '/../../templates/student/list.html.twig.php';
        return ob_get_clean();
    }
}
