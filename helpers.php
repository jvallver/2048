<?php

class Helpers {
    public function rand($min, $max) {
        return rand($min, $max);
    }

    public function displayBoard($string) {
        echo $string;
    }

    public function getKeyPressed() {
        $line = readline("\n");
        system("clear");
        return $line;
    }
}

?>