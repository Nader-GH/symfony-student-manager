<?php

namespace App\Controller;

class PageController {
    
    public function statistics() {
        $studentsFile = __DIR__ . '/../../data/students.json';
        $sectionsFile = __DIR__ . '/../../data/sections.json';

        if (!file_exists($studentsFile) || !file_exists($sectionsFile)) {
            return 'Data not initialized. Please run: php init_db.php';
        }

        // Load students and sections from JSON
        $studentsJson = file_get_contents($studentsFile);
        $students = json_decode($studentsJson, true) ?: [];

        $sectionsJson = file_get_contents($sectionsFile);
        $sections = json_decode($sectionsJson, true) ?: [];

        // Calculate statistics per section
        $sectionStats = [];
        foreach ($sections as $section) {
            $sectionId = $section['id'];
            
            // Get students in this section
            $sectionStudents = array_filter($students, function($student) use ($sectionId) {
                return $student['section_id'] == $sectionId;
            });
            
            $count = count($sectionStudents);
            
            // Calculate average age
            $averageAge = 0;
            if ($count > 0) {
                $totalAge = 0;
                $today = new \DateTime();
                
                foreach ($sectionStudents as $student) {
                    $dob = \DateTime::createFromFormat('Y-m-d', $student['date_of_birth']);
                    if ($dob) {
                        $age = $today->diff($dob)->y;
                        $totalAge += $age;
                    }
                }
                
                $averageAge = round($totalAge / $count, 1);
            }
            
            $sectionStats[] = [
                'section' => $section,
                'student_count' => $count,
                'average_age' => $averageAge,
                'students' => array_values($sectionStudents)
            ];
        }

        // Render template
        ob_start();
        require __DIR__ . '/../../templates/page/statistics.html.twig.php';
        return ob_get_clean();
    }
}
