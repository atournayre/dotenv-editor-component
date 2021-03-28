<?php

namespace Atournayre\Component\DotEnvEditor\Tests;

use Atournayre\Component\DotEnvEditor\DotEnvEditor;
use Atournayre\Component\DotEnvEditor\Exception\DotEnvEditorAddVariableTypeException;
use Atournayre\Component\DotEnvEditor\Exception\DotEnvEditorMissingVariableException;
use PHPUnit\Framework\TestCase;

class DotEnvEditorTest extends TestCase
{
    /**
     * @var DotEnvEditor
     */
    private $dotEnvEditor;

    const PATH_TO_DOTENV_DOTETEST_DOTPHP = __DIR__.'/datas/.env.test.php';

    protected function setUp(): void
    {
        copy(__DIR__.'/datas/.env.php', self::PATH_TO_DOTENV_DOTETEST_DOTPHP);
        $this->dotEnvEditor = new DotEnvEditor(self::PATH_TO_DOTENV_DOTETEST_DOTPHP);
    }

    protected function tearDown(): void
    {
        unlink(self::PATH_TO_DOTENV_DOTETEST_DOTPHP);
    }

    public function testGetUnexistingVariable()
    {
        $this->expectException(DotEnvEditorMissingVariableException::class);
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->get('NonExisting');
    }

    public function testGetVAR1Variable()
    {
        $this->dotEnvEditor->load();
        $this->assertEquals('value1', $this->dotEnvEditor->get('VAR1'));
    }

    public function testAddVAR2Variable()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', 'value2');
        $this->assertEquals('value2', $this->dotEnvEditor->get('VAR2'));
    }

    public function testResetVAR1Variable()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->reset('VAR1');
        $this->assertEquals('', $this->dotEnvEditor->get('VAR1'));
    }

    public function testResetUnexistingVariable()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->reset('VAR2');
        $this->assertEquals('', $this->dotEnvEditor->get('VAR2'));
    }

    public function testSaveFile()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', 'value2');
        $this->dotEnvEditor->save();

        $dotEnvEditor2 = new DotEnvEditor(self::PATH_TO_DOTENV_DOTETEST_DOTPHP);
        $dotEnvEditor2->load();
        $this->assertEquals('value2', $this->dotEnvEditor->get('VAR2'));
    }

    public function testGetVariablesAsArray()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', 'value2');

        $expectedArray = [
            'VAR1' => 'value1',
            'VAR2' => 'value2',
        ];
        $this->assertEquals($expectedArray, $this->dotEnvEditor->toArray());
    }

    public function testGetVariablesAsArrayWithTrueValue()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', true);

        $this->assertIsBool($this->dotEnvEditor->get('VAR2'));
    }

    public function testGetVariablesAsArrayWithFalseValue()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', false);

        $this->assertIsBool($this->dotEnvEditor->get('VAR2'));
    }

    public function testGetVariablesAsArrayWithNullValue()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2');

        $this->assertNull($this->dotEnvEditor->get('VAR2'));
    }

    public function testAddVariableArrayIsInvalid()
    {
        $this->expectException(DotEnvEditorAddVariableTypeException::class);
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', array());
    }

    public function testAddVariableIntIsValid()
    {
        $this->dotEnvEditor->load();
        $this->dotEnvEditor->add('VAR2', 1);

        $this->assertEquals(1, $this->dotEnvEditor->get('VAR2'));
    }
}
