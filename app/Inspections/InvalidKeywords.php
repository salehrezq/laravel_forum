<?php

namespace App\Inspections;

/**
 * Description of InvalidKeywords
 *
 * @author Saleh
 */
class InvalidKeywords {

    protected $invalidKeywords = [
        'yahoo customer support'
    ];

    public function detect($content) {

        $detected = false;

        foreach ($this->invalidKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $detected = true;
                break;
            }
        }
        return $detected;
    }

}
