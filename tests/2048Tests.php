<?php

include("../2048.php");

class _2048Tests extends PHPUnit_Framework_TestCase {

    public $sut;

    public function setUp() {
        $this->sut = $this->getMock('_2048', array('__rand', '__displayBoard', '__getKeyPressed'));
    }

    public function test_construct_called_shouldCreateBoard() {
        $this->assertEquals($this->sut->board, [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]);
    }

    /**
     * @dataProvider runTestProvider
     */
    public function test_run_doMove($string, $keyPressed, $board, $expected, $randReturnValues = array(0,8,1,5,0,1)) {
        $this->prepareRunTest($keyPressed, $board, $randReturnValues);
        $this->exerciseRunTest();
        $this->assertEquals($expected, $this->sut->board);
    }

    /**
     * @dataProvider displayBoardTestProvider
     */
    public function test_run_displayBoard($string, $board, $keyPress, $expectedDisplayCall) {
        $this->prepareRunTest($keyPress, $board, array(0,8,1,5,0,1));
        $this->sut->expects($this->exactly(1))->method('__displayBoard')->with($expectedDisplayCall);
        $this->exerciseRunTest();
    }

    /* Providers */
    public function displayBoardTestProvider() {
        return array(
            array('withEmptyBoard_displayBoardCorrectly', [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], ['q'], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n| 2    |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithNumber_displayBoardCorrectly', [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], ['q'], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      | 2    |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n| 2    |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithBigNumber_displayBoardCorrectly', [[0,128,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], ['q'], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      | 128  |      |      |\n --------------------------- \n|      | 2    |      |      |\n --------------------------- \n| 2    |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithNumberContainingAZero_displayBoardCorrectly', [[0,1024,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], ['q'], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      | 1024 |      |      |\n --------------------------- \n|      | 2    |      |      |\n --------------------------- \n| 2    |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithNumber2048Tile_showYouWinText', [[0,2048,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], ['dummy'], 'You Win!')
        );
    }

    public function runTestProvider() {
        return array(
            array('userCloseApp_prepareFirstShiftAndClose', ["q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,0,0], [2,0,0,0], [0,0,0,0]]),
            array('calledWithInvalidKey_prepareOnlyFirstShift', ["dummy", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,0,0], [2,0,0,0], [0,0,0,0]]),

            array('userPushDownButtonWithEmptyBoard_moveTheNumberToBottom', ["s", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [0,0,0,0], [2,0,0,0]]),
            array('userPushDownButtonWithOneNumber_moveTheNumberToBottom', ["s", "q"], [[0,0,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [0,0,0,0], [2,2,0,0]]),
            array('userPushDownButtonWithNumbersNumberInDifferentColumns_moveTheNumbersToBottom', ["s", "q"], [[0,0,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [0,0,0,0], [2,4,0,0]]),
            array('userPushDownButtonWithCollision_moveTheNumbersToBottom', ["s", "q"], [[0,0,0,0], [0,4,0,2], [0,2,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [0,4,0,0], [2,2,0,2]]),
            array('userPushDownButtonWithCollisionThatCanBeMerged_moveTheNumbersToBottom', ["s", "q"], [[0,0,0,0], [0,2,0,0], [0,2,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [0,0,0,0], [2,4,0,0]]),
            array('userPushDownButtonWithTwoCollisionThatCanBeMerged_moveTheNumbersToBottom', ["s", "q"], [[0,2,0,0], [0,2,0,0], [0,2,0,0], [0,2,0,0]], [[0,0,0,0], [0,4,0,0], [0,4,0,0], [2,4,0,0]]),
            array('userPushDownButtonWithComplexCollisionThatCanBeMerged_moveTheNumbersToBottom', ["s", "q"], [[0,0,0,0], [0,2,0,0], [0,2,0,0], [0,2,0,0]], [[0,0,0,0], [0,4,0,0], [0,2,0,0], [2,4,0,0]]),
            array('userPushDownButtonWithAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToBottom', ["s", "q"], [[0,2,0,0], [0,2,0,0], [0,4,0,0], [0,2,0,0]], [[4,0,0,0], [0,4,0,0], [0,4,0,0], [2,2,0,0]]),

            array('userPushTopButtonWithEmptyBoard_moveTheNumberToTop', ["w", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[2,0,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithOneNumber_moveTheNumberToTop', ["w", "q"], [[0,0,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], [[2,2,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithNumbersNumberInDifferentColumns_moveTheNumbersToTop', ["w", "q"], [[0,0,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]], [[2,4,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithCollision_moveTheNumbersToTop', ["w", "q"], [[0,0,0,0], [0,0,2,0], [0,0,4,2], [0,0,0,0]], [[2,0,2,2], [0,4,4,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithCollisionThatCanBeMerged_moveTheNumbersToTop', ["w", "q"], [[0,0,0,0], [0,0,2,0], [0,2,0,0], [0,0,0,0]], [[2,2,2,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithTwoCollisionThatCanBeMerged_moveTheNumbersToTop', ["w", "q"], [[0,2,0,0], [0,0,2,0], [0,0,2,0], [0,0,2,0]], [[2,2,4,0], [0,4,2,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithComplexCollisionThatCanBeMerged_moveTheNumbersToTop', ["w", "q"], [[0,0,2,0], [0,0,2,0], [0,0,2,0], [0,0,0,0]], [[2,0,4,0], [0,4,2,0], [0,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonWithAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToTop', ["w", "q"], [[0,0,2,0], [0,0,4,0], [0,0,2,0], [0,0,2,0]], [[2,0,2,0], [0,4,4,0], [0,0,4,0], [0,0,0,0]]),

            array('userPushRightButtonWithEmptyBoard_moveRight', ["d", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithOneNumber_moveTheNumberToRight', ["d", "q"], [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,2], [0,4,0,0], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithNumbersNumberInDifferentRows_moveTheNumbersToRight', ["d", "q"], [[0,2,0,0], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,2], [0,4,0,4], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithCollision_moveTheNumbersToRight', ["d", "q"], [[0,2,0,4], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[0,0,2,4], [0,4,0,4], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithCollisionThatCanBeMerged_moveTheNumbersToRight', ["d", "q"], [[0,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,4], [0,4,0,0], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithTwoCollisionThatCanBeMerged_moveTheNumbersToRight', ["d", "q"], [[2,2,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,4,4], [0,4,0,0], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithComplexCollisionThatCanBeMerged_moveTheNumbersToRight', ["d", "q"], [[2,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,2,4], [0,4,0,0], [0,0,0,2], [0,0,0,0]]),
            array('userPushRightButtonWithAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToRight', ["d", "q"], [[2,4,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2,4,4], [0,4,0,0], [0,0,0,2], [0,0,0,0]]),

            array('userPushLeftButtonWithEmptyBoard_moveLeft', ["a", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithOneNumber_moveTheNumberToLeft', ["a", "q"], [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[2,0,0,0], [0,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithNumbersNumberInDifferentRows_moveTheNumbersToLeft', ["a", "q"], [[0,2,0,0], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[2,0,0,0], [4,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithCollision_moveTheNumbersToLeft', ["a", "q"], [[0,2,0,4], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[2,4,0,0], [4,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithCollisionThatCanBeMerged_moveTheNumbersToLeft', ["a", "q"], [[0,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,0,0,0], [0,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithTwoCollisionThatCanBeMerged_moveTheNumbersToLeft', ["a", "q"], [[2,2,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,4,0,0], [0,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithComplexCollisionThatCanBeMerged_moveTheNumbersToLeft', ["a", "q"], [[2,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,2,0,0], [0,4,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushLeftButtonWithAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToLeft', ["a", "q"], [[2,4,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[2,4,4,0], [0,4,0,0], [2,0,0,0], [0,0,0,0]]),

            array('userPushLeftButtonButBoardHas2048Tile_dontDoTheMove', ["a"], [[0,2048,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2048,0,0], [0,0,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushRightButtonButBoardHas2048Tile_dontDoTheMove', ["d"], [[0,2048,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2048,0,0], [0,0,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushTopButtonButBoardHas2048Tile_dontDoTheMove', ["w"], [[0,2048,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2048,0,0], [0,0,0,0], [2,0,0,0], [0,0,0,0]]),
            array('userPushDownButtonButBoardHas2048Tile_dontDoTheMove', ["s"], [[0,2048,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2048,0,0], [0,0,0,0], [2,0,0,0], [0,0,0,0]]),

            array('userPushLeftButtonWithDifferentRandValues_doMoveCorrectly', ["a", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [4,0,2,0], [0,0,0,0], [0,0,0,0]], array(1,4,0,6,1,0)),
            array('userPushRightButtonWithDifferentRandValues_doMoveCorrectly', ["d", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,2,4], [0,0,0,0], [0,0,0,0]], array(1,4,0,6,1,0)),
            array('userPushUpButtonWithDifferentRandValues_doMoveCorrectly', ["w", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,0,0,0], [0,0,2,0], [0,0,0,0], [0,0,0,0]], array(1,4,0,6,1,0)),
            array('userPushBottomButtonWithDifferentRandValues_doMoveCorrectly', ["s", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,2,0], [0,0,0,0], [4,0,0,0]], array(1,4,0,6,1,0)),

            array('userPushRightAndLeftButtons_moveRightAndLeft', ["d", "a", "q"], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2,0,0], [4,0,0,0], [2,0,0,0], [0,0,0,0]]),
        );
    }

    /* Utils */
    private function prepareRunTest($keyPressed, $board, $randReturnValues) {
        $this->sut->expects($this->any())->method('__getKeyPressed')->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $keyPressed));
        $this->sut->board = $board;
        $this->sut->method('__rand')->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $randReturnValues));
    }

    private function exerciseRunTest() {
        $this->sut->run();
    }

}

?>
