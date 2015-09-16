<?php

/**
 * This file is part of the Jfx project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jfx\Tika\tests;

require __DIR__ . '/bootstrap.php';

use Jfx\Tika\TikaWrapper;
use Jfx\Tika\TikaWrapperInterface;

class TikaTest extends \PHPUnit_Framework_TestCase
{
    public function testSetterGetter()
    {
        $tw = new TikaWrapper();
        $this->assertEquals(TikaWrapperInterface::DEFAULT_TIMEOUT, $tw->getTimeout());

        $tw->setTimeout(10);
        $this->assertEquals(10, $tw->getTimeout());

        $tw->setBinary('fake_binary');
        $this->assertEquals('fake_binary', $tw->getBinary());

        $tw->setJavaBinary('java');
        $this->assertEquals('java', $tw->getJavaBinary());

        $this->assertEquals('UTF8', $tw->getOutputEncoding());

        $tw->setOutputEncoding('ISO-8859-15');
        $this->assertEquals('ISO-8859-15', $tw->getOutputEncoding());
    }

    public function testOutPutFormat()
    {
        $tw = new TikaWrapper();

        // Default Value
        $this->assertEquals('text', $tw->getOutputFormat());

        $tw->setOutputFormat('json');
        $this->assertEquals('json', $tw->getOutputFormat());
    }

    public function testCommand()
    {
        $tw = new TikaWrapper();

        $tw->setFile(new \SplFileInfo(__DIR__ . '/files/doc.pdf'));

        $expectedCmd = "'" . $tw->getJavaBinary() . "' '-jar' '" . $tw->getBinary() . "' ";
        $expectedCmd .= "'--text' '--encoding=UTF8' '" . $tw->getFile()->getPathname() . "'";

        $cmp = $tw->buildCommand();

        $this->assertEquals($expectedCmd, $cmp);
    }

    public function testExtract()
    {
        $tw = new TikaWrapper();
        $tw->setFile(new \SplFileInfo(__DIR__ . '/files/doc.pdf'));

        $expected = 'La marine en ira mal';
        $this->assertEquals($expected, $tw->extract());
    }

    public function testExtractBadFile()
    {
        $tw = new TikaWrapper();

        $this->setExpectedException('Jfx\\Tika\\Exception\\InvalidFileException');
        $tw->setFile(new \SplFileInfo(__DIR__ . '/files/doc_fake.pdf'));
    }

    public function testExtractUnreadableFile()
    {
        $tw = new TikaWrapper();

        @chmod(__DIR__ . '/files/doc_unreadable.pdf', 0000);
        $this->setExpectedException('Jfx\\Tika\\Exception\\InvalidFileException');
        $tw->setFile(new \SplFileInfo(__DIR__ . '/files/doc_unreadable.pdf'));
        @chmod(__DIR__ . '/files/doc_unreadable.pdf', 0644);
    }

    public function testExtractBadEncodingParam()
    {
        $tw = new TikaWrapper();
        $tw->setFile(new \SplFileInfo(__DIR__ . '/files/doc.pdf'));
        $tw->setOutputEncoding('PLOPO');

        $this->setExpectedException('Jfx\\Tika\\Exception\\Command\\UnsupportedEncodingException');
        $tw->extract();
    }

    public function testExtractBadOutputFormatParam()
    {
        $tw = new TikaWrapper();
        $tw->setFile(new \SplFileInfo(__DIR__ . '/files/doc.pdf'));

        $this->setExpectedException('Jfx\\Tika\\Exception\\UnsupportedOutputFormatException');
        $tw->setOutputFormat('PLOPO');
    }
}
