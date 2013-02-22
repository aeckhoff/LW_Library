<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
require_once dirname(__FILE__) . '/../Config/phpUnitConfig.php';
require_once dirname(__FILE__) . '/../../Filesystem/Directory.php';
require_once dirname(__FILE__) . '/../../Filesystem/File.php';

/**
 * Test class for File.
 * Generated by PHPUnit on 2013-02-22 at 09:50:04.
 */
class FileTest extends \PHPUnit_Framework_TestCase
{

    private $file;
    private $directoryObject_base;
    private $directoryObject2;
    private $config;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = phpUnitConfig::getConfig();

        $this->directoryObject_base = LwLibrary\Filesystem\Directory::getInstance($this->config["path"]["web_resource"]);
        $this->assertTrue(is_object($this->directoryObject_base));
        $this->assertTrue($this->directoryObject_base->check());

        $this->directoryObject2 = LwLibrary\Filesystem\Directory::getInstance($this->config["path"]["web_resource"] . "test_lw_file/");
        if (!$this->directoryObject2->check()) {
            $this->assertTrue($this->directoryObject_base->add("test_lw_file"));
            $this->addfile($this->directoryObject2->getPath(), "test.txt");
        } else {
            $this->directoryObject2->delete(true);
            $this->setUp();
        }
        $this->assertTrue(is_object($this->directoryObject2));
        $this->assertTrue($this->directoryObject2->check());


        $this->file = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath(), "test.txt");
        $this->assertTrue($this->file->check());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->assertTrue($this->directoryObject2->delete(true));
        $this->assertFalse($this->directoryObject2->check());
    }

    /**
     * @todo Implement testGetInstance().
     */
    public function testGetInstance()
    {
        $file = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath(), "test.txt");
        $this->assertTrue(is_object($file));

        $file2 = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath() . "gibtEsNicht/", "gibtEsNicht.txt");
        $this->assertTrue(is_object($file2));
    }

    /**
     * @todo Implement testGetType().
     */
    public function testGetType()
    {
        $this->assertEquals("file", $this->file->getType());
    }

    /**
     * @todo Implement testGetDateformat().
     */
    public function testGetDateformat()
    {
        $this->assertEquals("d.m.Y H:i", $this->file->getDateFormat());
    }

    /**
     * @todo Implement testGetFilename().
     */
    public function testGetFilename()
    {
        $this->assertEquals("test.txt", $this->file->getFilename());
    }

    /**
     * @todo Implement testGetName().
     */
    public function testGetName()
    {
        $this->assertFalse($this->file->getName());
    }

    /**
     * @todo Implement testGetPath().
     */
    public function testGetPath()
    {
        $this->assertEquals($this->directoryObject2->getPath(), $this->file->getPath());
    }

    /**
     * @todo Implement testSetDateFormat().
     */
    public function testSetDateFormat()
    {
        $this->file->setDateFormat();
        $this->assertEquals("d.m.Y h:i", $this->file->getDateFormat());
        $this->file->setDateFormat("Ymd");
        $this->assertEquals("Ymd", $this->file->getDateFormat());
    }

    /**
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
        $this->assertTrue($this->file->delete());
        try {
            $this->file->delete();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "[Loeschen nicht moeglich] File existiert nicht: " . $this->file->getPath() . $this->file->getFilename());
        }

        $file = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath() . "gibtEsNicht/", "gibtEsNicht.txt");
        $this->assertTrue(is_object($file));

        try {
            $file->delete();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "[Loeschen nicht moeglich] Verzeichnis existiert nicht: ". $file->getPath());
        }
    }

    /**
     * @todo Implement testRename().
     */
    public function testRename()
    {
        $this->addfile($this->directoryObject2->getPath(), "test2.txt"); # adding 2nd file to the directory
        $secFile = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath(), "test2.txt");
        $this->assertTrue(is_object($secFile));
        $this->assertTrue($secFile->check());
        
        try {
            $this->file->rename("test2.txt");
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "[Umbenennen nicht moeglich] File existiert bereits");
        }
        
        $this->assertTrue($this->file->rename("test3.txt"));
        try {
            $this->file->check();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "File existiert nicht: " . $this->file->getPath() . $this->file->getFilename());
        }
        
        $renamedFile = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath(), "test3.txt");
        $this->assertTrue(is_object($renamedFile));
        $this->assertTrue($renamedFile->check());
        
        $this->assertTrue($renamedFile->rename("test2.txt", true));
        
        try {
            $secFile->check();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "File existiert nicht: " . $secFile->getPath() . $secFile->getFilename());
        }
    }

    /**
     * @todo Implement testMove().
     */
    public function testMove()
    {
        $this->directoryObject2->add("test/");
        $dirTest = LwLibrary\Filesystem\Directory::getInstance($this->directoryObject2->getPath()."test/"); 
        $this->assertTrue(is_object($dirTest));
        $this->assertTrue($dirTest->check());

        $this->addfile($dirTest->getPath(), "test.txt");
        $file = LwLibrary\Filesystem\File::getInstance($dirTest->getPath(), "test.txt");
        $this->addfile($this->directoryObject2->getPath(), "test2.txt");
        $secFile = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath(), "test2.txt");
        
        $this->assertTrue($secFile->move($dirTest->getPath()));

        try {
            $this->file->move($dirTest->getPath());
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "[Verschieben nicht moeglich] Eine Datei mit dem Dateiname [ " . $this->file->getFilename() . " ] existiert bereits.");
        }

        $this->assertTrue($this->file->move($dirTest->getPath(), false, true));
        try {
            $this->file->check();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "File existiert nicht: " . $this->file->getPath() . $this->file->getFilename());
        }

        $this->assertTrue($file->check());
        
        $dirTest->delete(true);
    }

    /**
     * @todo Implement testCopy().
     */
    public function testCopy()
    {
        $this->directoryObject2->add("test/");
        $dirTest = LwLibrary\Filesystem\Directory::getInstance($this->directoryObject2->getPath()."test/");
        
        $this->addfile($dirTest->getPath(), "test.txt");
        
        try {
            $this->file->copy($dirTest->getPath());
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "[Kopieren nicht moeglich] Eine Datei mit dem Dateiname [ " . $this->file->getFilename() . " ] existiert bereits.");
        }

        $this->assertTrue($this->file->copy($dirTest->getPath(), false, true));
        
        $file = LwLibrary\Filesystem\File::getInstance($dirTest->getPath(), "test2.txt");
        try {
            $file->check();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "File existiert nicht: " . $file->getPath() . $file->getFilename());
        }
        $this->assertTrue($this->file->copy($dirTest->getPath(), "test2.txt"));
        $this->assertTrue($file->check());
        
        $dirTest->delete(true);
    }

    /**
     * @todo Implement testGetSize().
     */
    public function testGetSize()
    {
        $this->assertEquals("25 Bytes", $this->file->getSize());
        $this->assertEquals(25, $this->file->getSize(true));
    }

    /**
     * @todo Implement testGetDate().
     */
    public function testGetDate()
    {
        $this->file->setDateFormat("d.m.Y");
        $this->assertEquals(date("d.m.Y"), $this->file->getDate());
    }

    /**
     * @todo Implement testGetRights().
     */
    public function testGetRights()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetExtension().
     */
    public function testGetExtension()
    {
        $this->assertEquals("txt", $this->file->getExtension());
    }

    /**
     * @todo Implement testCheck().
     */
    public function testCheck()
    {
        $this->assertTrue($this->file->check());
        $file = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath(), "gibtEsNicht.txt");
        
        try {
            $file->check();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "File existiert nicht: " . $file->getPath() . $file->getFilename());
        }
        
        $file = LwLibrary\Filesystem\File::getInstance($this->directoryObject2->getPath()."gibtEsNicht/", "gibtEsNicht.txt");
        try {
            $file->check();
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "Verzeichnis existiert nicht: " . $file->getPath());
        }
    }

    /**
     * @todo Implement testGetNextFilename().
     */
    public function testGetNextFilename()
    {
        $this->assertEquals($this->file->getPath()."test_2.txt", $this->file->getNextFilename());
    }

    public function addfile($path, $filename)
    {
        $string = "test text ohne viel sinn!";
        $fileopen = fopen($path . $filename, "w+");
        $ok = fwrite($fileopen, $string);
        fclose($fileopen);
        $this->assertEquals($ok, strlen($string));
    }

}