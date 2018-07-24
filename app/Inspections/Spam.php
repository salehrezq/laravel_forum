<?php

namespace App\Inspections;

/**
 * Description of Spam
 *
 * @author Saleh
 */
class Spam {

    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class
    ];

    public function detect($content) {

        $isSpam = false;

        foreach ($this->inspections as $inspection) {
            if (app($inspection)->detect($content)) {
                $isSpam = true;
                break;
            }
        }

        return $isSpam;
    }

}
