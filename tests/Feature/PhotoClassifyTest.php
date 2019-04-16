<?php

namespace PhotoClassifyTest\Feature;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Yangyao\PhotoClassify\Command\ClassifyCommand;


class PhotoClassifyTest extends TestCase
{
    /** @var $commandTester CommandTester */
    private $commandTester;

    protected function setUp()
    {

        $application = new Application();
        $application->add(new ClassifyCommand());
        $command = $application->find('app:run');
        $this->commandTester = new CommandTester($command);

    }

    public function testPhotoClassify()
    {
        $source =  __DIR__.'/../resources/source/';
        $target =  __DIR__.'/../resources/target/';
        $this->commandTester->execute(['source' =>$source,'target'=>$target]);

        $filename = $target.'2016-06/test-image.jpg';

        $this->assertFileExists($filename);

        @unlink($filename);
        @rmdir($target.'2016-06');
    }


}