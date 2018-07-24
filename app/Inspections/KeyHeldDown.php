<?php

namespace App\Inspections;

/**
 * Description of KeyHeldDown
 *
 * @author Saleh
 */
class KeyHeldDown {

    public function detect($content) {

        if (preg_match('/(.)\\1{4,}/', $content) > 0) {
            return true;
        }

        return false;
    }

}
