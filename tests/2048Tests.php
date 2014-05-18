<?php

include("../2048.php");

class _2048Tests extends PHPUnit_Framework_TestCase {

    public $sut;

    public function setUp() {
        $this->sut = $this->getMock('_2048', array('__rand', '__displayBoard'));
    }

    public function test_construct_called_shouldCreateBoard() {
        $this->assertEquals($this->sut->board, [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]);
    }

    /**
     * @dataProvider getRandomPositionProvider
     */
    public function test_getRandomPosition($string, $board, $positions, $expected) {
        $this->sut->board = $board;
        $this->mockRandFunction($positions);
        $actual = $this->sut->getRandomPosition();
        $this->assertEquals($actual, $expected, $string);
    }

    /**
     * @dataProvider prepareNewShiftProvider
     */
    public function test_prepareNewShift($string, $randNumber, $numberPosition, $expected) {
        $this->sut = $this->getMock('_2048', array('__rand', 'getRandomPosition'));
        $this->sut->board = [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]];
        $this->mockRandFunction([$randNumber]);
        $this->mockRandomPosition($numberPosition);
        $this->sut->prepareNewShift();
        $this->assertEquals($this->sut->board, $expected, $string);
    }

    /**
     * @dataProvider moveLeftProvider
     */
    public function test_moveLeft($string, $board, $expected) {
        $this->exerciseMoveTest($board);
        $this->sut->moveLeft();
        $this->moveTestAssertion($expected, $string);
    }

    /**
     * @dataProvider moveRightProvider
     */
    public function test_moveRight($string, $board, $expected) {
        $this->exerciseMoveTest($board);
        $this->sut->moveRight();
        $this->moveTestAssertion($expected, $string);
    }

    /**
     * @dataProvider moveUpProvider
     */
    public function test_moveUp($string, $board, $expected) {
        $this->exerciseMoveTest($board);
        $this->sut->moveUp();
        $this->moveTestAssertion($expected, $string);
    }

    /**
     * @dataProvider moveDownProvider
     */
    public function test_moveDown($string, $board, $expected) {
        $this->exerciseMoveTest($board);
        $this->sut->moveDown();
        $this->moveTestAssertion($expected, $string);
    }

    /**
     * @dataProvider renderProvider
     */
    public function test_render($string, $board, $expected) {
        $this->sut->board = $board;
        $this->sut->expects($this->exactly(1))->method('__displayBoard')->with($expected);
        $this->sut->render();
    }

    public function test_run_called_prepareNewShift() {
        $this->prepareRunTest(["q"]);
        $this->sut->expects($this->exactly(1))->method('prepareNewShift')->with();
        $this->exerciseRunTest();
    }

    /**
     * @dataProvider runTestProvider
     */
    public function test_run_userPushButton($string, $keyPressed, $moveUpTimesCalled, $moveDownTimesCalled, $moveLeftTimesCalled, $moveRightTimesCalled) {
        $this->prepareRunTestWithRenderMocked($keyPressed);
        $this->sut->expects($this->any())->method('checkEndOfGame')->will($this->onConsecutiveCalls(false));
        $this->sut->expects($this->exactly($moveUpTimesCalled))->method('moveUp')->with();
        $this->sut->expects($this->exactly($moveDownTimesCalled))->method('moveDown')->with();
        $this->sut->expects($this->exactly($moveLeftTimesCalled))->method('moveLeft')->with();
        $this->sut->expects($this->exactly($moveRightTimesCalled))->method('moveRight')->with();
        $this->exerciseRunTest();
    }

    public function test_run_called_render() {
        $this->prepareRunTest(["q"]);
        $this->sut->expects($this->exactly(1))->method('render')->with();
        $this->exerciseRunTest();
    }

    public function test_run_called_checkForEndOfGame() {
        $this->prepareRunTestWithRenderMocked(["q"]);
        $this->sut->expects($this->exactly(1))->method('checkEndOfGame')->with();
        $this->exerciseRunTest();
    }

    /**
     * @dataProvider checkEndOfGameProvider
     */
    public function test_checkEndOfGame($string, $board, $expected) {
        $this->sut->board = $board;
        $actual = $this->sut->checkEndOfGame();
        $this->assertEquals($actual, $expected, $string);
    }

    /* Providers */
    public function getRandomPositionProvider() {
        return array(
            array('getRandomPosition_withEmptyBoard_returnCorrectPosition', [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [2], 2),
            array('getRandomPosition_withEmptyBoardAndOtherRandomResponse_returnCorrectPosition', [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [4], 4),
            array('getRandomPosition_withCollision_returnCorrectPosition', [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [1, 4], 4),
            array('getRandomPosition_withTwoCollisions_returnCorrectPosition', [[0,0,2,0], [4,0,0,0], [0,0,0,0], [0,0,0,0]], [2, 4, 8], 8)
        );
    }

    public function prepareNewShiftProvider() {
        return array(
            array('prepareNewShift_randomReturnZero_addTwoToBoard', 0, 1, [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('prepareNewShift_randomReturnOne_addFourToBoard', 1, 1, [[0,4,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('prepareNewShift_firstPosition_addTwoToCorrectPlace', 0, 0, [[2,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('prepareNewShift_differentPosition_addTwoToCorrectPlace', 0, 4, [[0,0,0,0], [2,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('prepareNewShift_anotherPosition_addTwoToCorrectPlace', 0, 10, [[0,0,0,0], [0,0,0,0], [0,0,2,0], [0,0,0,0]])
        );
    }

    public function moveLeftProvider() {
        return array(
            array('test_moveLeft_withOneNumber_moveTheNumberToLeft', [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[2,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveLeft_withNumbersNumberInDifferentRows_moveTheNumbersToLeft', [[0,2,0,0], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[2,0,0,0], [4,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveLeft_withCollision_moveTheNumbersToLeft', [[0,2,0,4], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[2,4,0,0], [4,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveLeft_withCollisionThatCanBeMerged_moveTheNumbersToLeft', [[0,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveLeft_withTwoCollisionThatCanBeMerged_moveTheNumbersToLeft', [[2,2,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,4,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveLeft_withComplexCollisionThatCanBeMerged_moveTheNumbersToLeft', [[2,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[4,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveLeft_withAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToLeft', [[2,4,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[2,4,4,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]])
        );
    }

    public function moveRightProvider() {
        return array(
            array('test_moveRight_withOneNumber_moveTheNumberToRight', [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveRight_withNumbersNumberInDifferentRows_moveTheNumbersToRight', [[0,2,0,0], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,2], [0,0,0,4], [0,0,0,0], [0,0,0,0]]),
            array('test_moveRight_withCollision_moveTheNumbersToRight', [[0,2,0,4], [0,0,4,0], [0,0,0,0], [0,0,0,0]], [[0,0,2,4], [0,0,0,4], [0,0,0,0], [0,0,0,0]]),
            array('test_moveRight_withCollisionThatCanBeMerged_moveTheNumbersToRight', [[0,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,4], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveRight_withTwoCollisionThatCanBeMerged_moveTheNumbersToRight', [[2,2,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,4,4], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveRight_withComplexCollisionThatCanBeMerged_moveTheNumbersToRight', [[2,2,2,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,2,4], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveRight_withAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToRight', [[2,4,2,2], [0,0,0,0], [0,0,0,0], [0,0,0,0]], [[0,2,4,4], [0,0,0,0], [0,0,0,0], [0,0,0,0]])
        );
    }

    public function moveUpProvider() {
        return array(
            array('test_moveUp_withOneNumber_moveTheNumberToTop', [[0,0,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveUp_withNumbersNumberInDifferentColumns_moveTheNumbersToTop', [[0,0,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]], [[0,4,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveUp_withCollision_moveTheNumbersToTop', [[0,0,0,0], [0,2,0,0], [0,4,0,2], [0,0,0,0]], [[0,2,0,2], [0,4,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveUp_withCollisionThatCanBeMerged_moveTheNumbersToTop', [[0,0,0,0], [0,2,0,0], [0,2,0,0], [0,0,0,0]], [[0,4,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveUp_withTwoCollisionThatCanBeMerged_moveTheNumbersToTop', [[0,2,0,0], [0,2,0,0], [0,2,0,0], [0,2,0,0]], [[0,4,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveUp_withComplexCollisionThatCanBeMerged_moveTheNumbersToTop', [[0,2,0,0], [0,2,0,0], [0,2,0,0], [0,0,0,0]], [[0,4,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]]),
            array('test_moveUp_withAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToTop', [[0,2,0,0], [0,4,0,0], [0,2,0,0], [0,2,0,0]], [[0,2,0,0], [0,4,0,0], [0,4,0,0], [0,0,0,0]])
        );
    }

    public function moveDownProvider() {
        return array(
            array('test_moveDown_withOneNumber_moveTheNumberToBottom', [[0,0,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,2,0,0]]),
            array('test_moveDown_withNumbersNumberInDifferentColumns_moveTheNumbersToBottom', [[0,0,0,0], [0,4,0,0], [0,0,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,4,0,0]]),
            array('test_moveDown_withCollision_moveTheNumbersToBottom', [[0,0,0,0], [0,4,0,2], [0,2,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,0,0], [0,4,0,0], [0,2,0,2]]),
            array('test_moveDown_withCollisionThatCanBeMerged_moveTheNumbersToBottom', [[0,0,0,0], [0,2,0,0], [0,2,0,0], [0,0,0,0]], [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,4,0,0]]),
            array('test_moveDown_withTwoCollisionThatCanBeMerged_moveTheNumbersToBottom', [[0,2,0,0], [0,2,0,0], [0,2,0,0], [0,2,0,0]], [[0,0,0,0], [0,0,0,0], [0,4,0,0], [0,4,0,0]]),
            array('test_moveDown_withComplexCollisionThatCanBeMerged_moveTheNumbersToBottom', [[0,0,0,0], [0,2,0,0], [0,2,0,0], [0,2,0,0]], [[0,0,0,0], [0,0,0,0], [0,2,0,0], [0,4,0,0]]),
            array('test_moveDown_withAnotherComplexCollisionThatCanBeMerged_moveTheNumbersToBottom', [[0,2,0,0], [0,2,0,0], [0,4,0,0], [0,2,0,0]], [[0,0,0,0], [0,4,0,0], [0,4,0,0], [0,2,0,0]])
        );
    }

    public function renderProvider() {
        return array(
            array('withEmptyBoard_displayBoardCorrectly', [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithNumber_displayBoardCorrectly', [[0,2,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      | 2    |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithBigNumber_displayBoardCorrectly', [[0,128,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      | 128  |      |      |\n --------------------------- \n|      | 2    |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------"),
            array('boardWithNumberContainingAZero_displayBoardCorrectly', [[0,1024,0,0], [0,2,0,0], [0,0,0,0], [0,0,0,0]], "← a → d ↓ s ↑ w | quit - q\n\n --------------------------- \n|      | 1024 |      |      |\n --------------------------- \n|      | 2    |      |      |\n --------------------------- \n|      |      |      |      |\n --------------------------- \n|      |      |      |      |\n ---------------------------")
        );
    }

    public function runTestProvider() {
        return array(
            array('userPushUpButton_moveUp', ["w", "q"], 1, 0, 0, 0),
            array('userPushDownButton_moveDown', ["s", "q"], 0, 1, 0, 0),
            array('userPushLeftButton_moveLeft', ["a", "q"], 0, 0, 1, 0),
            array('userPushRightButton_moveRight', ["d", "q"], 0, 0, 0, 1),
            array('userPushRightAndLeftButtons_moveRightAndLeft', ["d", "a", "q"], 0, 0, 1, 1),
            array('userCloseApp_doNotDoAnything', ["q"], 0, 0, 0, 0)
        );
    }

    public function checkEndOfGameProvider() {
        return array(
            array('withEmptyBoard_returnFalse', [[0,0,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], false),
            array('withBoardWith2048Number_returnTrue', [[0,2048,0,0], [0,0,0,0], [0,0,0,0], [0,0,0,0]], true),
            array('withBoardWithout2048Number_returnFalse', [[0,56,0,2], [0,128,0,0], [0,4,8,0], [0,0,0,0]], false),
        );
    }

    /* Utils */
    private function exerciseMoveTest($board) {
        $this->sut->board = $board;
    }

    private function prepareRunTest($keyPressed) {
        $this->sut = $this->getMock('_2048', array('prepareNewShift', '__getKeyPressed', 'moveDown', 'moveUp', 'moveRight', 'moveLeft', 'checkEndOfGame', 'render'));
        $this->sut->expects($this->any())->method('__getKeyPressed')->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $keyPressed));
    }

    private function exerciseRunTest() {
        $this->sut->run();
    }

    private function moveTestAssertion($expected, $string) {
        $this->assertEquals($this->sut->board, $expected, $string);
    }

    private function mockRandFunction($calls) {
        $this->sut->expects($this->any())
            ->method('__rand')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $calls));
    }

    private function mockRandomPosition($call) {
        $this->sut->expects($this->any())
            ->method('getRandomPosition')
            ->will($this->onConsecutiveCalls($call));
    }

    public function mockRender()
    {
        $this->sut->expects($this->any())->method('render')->with();
    }

    public function prepareRunTestWithRenderMocked($keyPressed)
    {
        $this->prepareRunTest($keyPressed);
        $this->mockRender();
    }

}

?>
