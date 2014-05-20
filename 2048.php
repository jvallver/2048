<?php

define('KEY_UP', 'w');
define('KEY_LEFT', 'a');
define('KEY_RIGHT', 'd');
define('KEY_DOWN', 's');

class _2048 {

    public function __construct() {
        $this->board = [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]];
    }

    public function prepareNewShift() {
        $numberToAdd = $this->__rand(0,1) == 0? 2: 4;
        $position = $this->getRandomPosition();
        $row = $position/4;
        $column = $position % 4;
        $this->board[$row][$column] = $numberToAdd;
    }

    public function moveLeft() {
        $this->moveHorizontal(1);
    }

    public function moveRight() {
        $this->moveHorizontal(-1);
    }

    public function moveUp() {
        $this->moveVertical(1);
    }

    public function moveDown() {
        $this->moveVertical(-1);
    }

    public function render() {
        $template = "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n| %-4d | %-4d | %-4d | %-4d |\n --------------------------- \n| %-4d | %-4d | %-4d | %-4d |\n --------------------------- \n| %-4d | %-4d | %-4d | %-4d |\n --------------------------- \n| %-4d | %-4d | %-4d | %-4d |\n ---------------------------";
        $template = call_user_func_array('sprintf', array_merge([$template], $this->board[0], $this->board[1], $this->board[2], $this->board[3]));
        $template = str_replace(' 0 ', '   ', $template);
        $this->__displayBoard($template);
    }

    public function checkEndOfGame() {
        for($i=0; $i<count($this->board); $i++) {
            for($j=0; $j<count($this->board[$i]); $j++) {
                if($this->board[$i][$j] == "2048")
                    return true;
            }
        }
        return false;
    }

    public function run() {
        $keyPress = "";
        $this->prepareNewShift();
        while($keyPress != "q" && !$this->checkEndOfGame()) {
            if($keyPress == KEY_UP)
                $this->doMove(function() {
                    $this->moveUp();
                });
            else if($keyPress == KEY_LEFT)
                $this->doMove(function() {
                    $this->moveLeft();
                });
            else if($keyPress == KEY_RIGHT)
                $this->doMove(function() {
                    $this->moveRight();
                });
            else if($keyPress == KEY_DOWN)
                $this->doMove(function() {
                    $this->moveDown();
                });
            $this->render();
            $keyPress = $this->__getKeyPressed();
        }
    }

    //Utils
    public function __displayBoard($string) {
        echo $string;
    }

    public function __rand($min, $max) {
        return rand($min, $max);
    }


    public function __getKeyPressed() {
        $line = readline("\n");
        system("clear");
        return $line;
    }

    private function doMove($move) {
        $this->prepareNewShift();
        $move();
    }

    public function getRandomPosition() {
        $position = $this->__rand(0,15);
        if($this->board[$position/4][$position%4] == 0)
            return $position;
        return $this->getRandomPosition();
    }

    private function moveHorizontal($direction) {
        if($direction == -1) {
            $this->board = array_map(function($row) {
                return array_reverse($row);
            }, $this->board);
        }
        $this->mergeTiles(function($i, $j) {
            return $j != 0 && $this->board[$i][$j] != 0 && $this->board[$i][$j-1] == $this->board[$i][$j];
        }, 0, -1);
        $this->moveTiles(function($i, $j) {
            return $j != 0 && $this->board[$i][$j] != 0 && $this->board[$i][$j-1] == 0;
        }, 0, -1);
        if($direction == -1) {
            $this->board = array_map(function($row) {
                return array_reverse($row);
            }, $this->board);
        }
    }

    private function moveVertical($direction) {
        if($direction == -1)
            $this->board = array_reverse($this->board);
        $this->mergeTiles(function($i, $j) {
            return $i != 0 && $this->board[$i][$j] != 0 && $this->board[$i-1][$j] == $this->board[$i][$j];
        }, -1, 0);
        $this->moveTiles(function($i, $j) {
            return $i != 0 && $this->board[$i][$j] != 0 && $this->board[$i-1][$j] == 0;
        }, -1, 0);
        if($direction == -1)
            $this->board = array_reverse($this->board);
    }

    private function mergeTiles($evaluateMergeCondition, $rowLookup, $colLookup) {
        for($i=0; $i<count($this->board); $i++) {
            for($j=0; $j<count($this->board[$i]); $j++) {
                if($evaluateMergeCondition($i, $j)) {
                    $this->board[$i+$rowLookup][$j+$colLookup] += $this->board[$i][$j];
                    $this->board[$i][$j] = 0;
                }
            }
        }
    }

    private function moveTiles($evaluateMoveCondition, $rowLookup, $colLookup) {
        for($i=0; $i<count($this->board); $i++) {
            for($j=0; $j<count($this->board[$i]); $j++) {
                if($evaluateMoveCondition($i, $j)) {
                    $this->board[$i+$rowLookup][$j+$colLookup] = $this->board[$i][$j];
                    $this->board[$i][$j] = 0;
                    $i = 0;
                    $j = 0;
                }
            }
        }
    }

}

if (!count(debug_backtrace()))
{
    $_2048 = new _2048();
    $_2048->run();
}


?>