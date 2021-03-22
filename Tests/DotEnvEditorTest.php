<?php

namespace Atournayre\Component\DotEnvEditor\Tests;

use Atournayre\Component\DotEnvEditor\DotEnvEditor;
use Atournayre\Component\DotEnvEditor\Exception\DotEnvEditorMissingVariableException;
use PHPUnit\Framework\TestCase;

class DotEnvEditorTest extends TestCase
{
    protected function setUp(): void
    {
        copy(__DIR__.'/datas/.env.php', __DIR__.'/datas/.env.test.php');
    }

    protected function tearDown(): void
    {
        unlink(__DIR__.'/datas/.env.test.php');
    }

    public function testGetUnexistingVariable()
    {
        $this->expectException(DotEnvEditorMissingVariableException::class);
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $dotEnvEditor->get('NonExisting');
    }

    public function testGetVAR1Variable()
    {
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $this->assertEquals('value1', $dotEnvEditor->get('VAR1'));
    }

    public function testAddVAR2Variable()
    {
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $dotEnvEditor->add('VAR2', 'value2');
        $this->assertEquals('value2', $dotEnvEditor->get('VAR2'));
    }

    public function testResetVAR1Variable()
    {
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $dotEnvEditor->reset('VAR1');
        $this->assertEquals('', $dotEnvEditor->get('VAR1'));
    }

    public function testResetUnexistingVariable()
    {
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $dotEnvEditor->reset('VAR2');
        $this->assertEquals('', $dotEnvEditor->get('VAR2'));
    }

    public function testSaveFile()
    {
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $dotEnvEditor->add('VAR2', 'value2');
        $dotEnvEditor->save();

        $dotEnvEditor2 = new DotEnvEditor();
        $dotEnvEditor2->load(__DIR__.'/datas/.env.test.php');
        $this->assertEquals('value2', $dotEnvEditor->get('VAR2'));
    }

    public function testGetVariablesAsArray()
    {
        $dotEnvEditor = new DotEnvEditor();
        $dotEnvEditor->load(__DIR__.'/datas/.env.test.php');
        $dotEnvEditor->add('VAR2', 'value2');

        $expectedArray = [
            'VAR1' => 'value1',
            'VAR2' => 'value2',
        ];
        $this->assertEquals($expectedArray, $dotEnvEditor->toArray());
    }
}
