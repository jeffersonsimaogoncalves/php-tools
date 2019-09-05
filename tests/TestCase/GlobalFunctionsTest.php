<?php
/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace JeffersonSimaoGoncalves\Tools\Test;

use App\ExampleChildClass;
use App\ExampleClass;
use App\ExampleOfStringable;
use App\ExampleOfTraversable;
use BadMethodCallException;
use PHPUnit\Framework\Error\Deprecated;
use stdClass;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Test for `add_slash_term()` global function
     * @test
     */
    public function testAddSlashTerm()
    {
        $expected = DS . 'tmp' . DS;
        $this->assertSame($expected, add_slash_term(DS . 'tmp'));
        $this->assertSame($expected, add_slash_term(DS . 'tmp' . DS));
    }

    /**
     * Test for `array_clean()` global function
     * @test
     */
    public function testArrayClean()
    {
        $filterMethod = function ($value) {
            return $value && $value != 'third';
        };

        $array = ['first', 'second', false, 0, 'second', 'third', null, '', 'fourth'];
        $this->assertSame(['first', 'second', 'third', 'fourth'], array_clean($array));
        $this->assertSame(['first', 'second', 'fourth'], array_clean($array, $filterMethod));

        $array = ['a' => 'first', 0 => 'second', false, 'c' => 'third', 'd' => 'second'];
        $this->assertSame(['a' => 'first', 0 => 'second', 'c' => 'third'], array_clean($array));
        $this->assertSame(['a' => 'first', 0 => 'second'], array_clean($array, $filterMethod));

        $expected = ['a' => 'first', 1 => false, 'c' => 'third', 'd' => 'second'];
        $this->assertSame($expected, array_clean($array, $filterMethod, ARRAY_FILTER_USE_KEY));
    }

    /**
     * Test for `array_key_first()` global function
     * @test
     */
    public function testArrayKeyFirst()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals(0, array_key_first($array));
        $this->assertEquals('a', array_key_first(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_key_first([]));
    }

    /**
     * Test for `array_key_last()` global function
     * @test
     */
    public function testArrayKeyLast()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals(2, array_key_last($array));
        $this->assertEquals('c', array_key_last(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_key_last([]));
    }

    /**
     * Test for `array_value_first()` global function
     * @test
     */
    public function testArrayValueFirst()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('first', array_value_first($array));
        $this->assertEquals('first', array_value_first(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_value_first([]));
    }

    /**
     * Test for `array_value_first_recursive()` global function
     * @test
     */
    public function testArrayValueFirstRecursive()
    {
        $this->assertEquals(null, array_value_first_recursive([]));
        foreach ([
            ['first', 'second', 'third', 'fourth'],
            ['first', ['second', 'third'], ['fourth']],
            [['first', 'second'], ['third'], ['fourth']],
            [[['first'], 'second'], ['third'], [['fourth']]],
        ] as $array) {
            $this->assertEquals('first', array_value_first_recursive($array));
        }
    }

    /**
     * Test for `array_value_last()` global function
     * @test
     */
    public function testArrayValueLast()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('third', array_value_last($array));
        $this->assertEquals('third', array_value_last(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_value_last([]));
    }

    /**
     * Test for `array_value_last_recursive()` global function
     * @test
     */
    public function testArrayValueLastRecursive()
    {
        $this->assertEquals(null, array_value_last_recursive([]));
        foreach ([
            ['first', 'second', 'third', 'fourth'],
            ['first', ['second', 'third'], ['fourth']],
            [['first', 'second'], ['third'], ['fourth']],
            [[['first'], 'second'], ['third'], [['fourth']]],
        ] as $array) {
            $this->assertEquals('fourth', array_value_last_recursive($array));
        }
    }

    /**
     * Test for `clean_url()` global function
     * @test
     */
    public function testCleanUrl()
    {
        foreach ([
            'http://mysite.com',
            'http://mysite.com/',
            'http://mysite.com#fragment',
            'http://mysite.com/#fragment',
        ] as $url) {
            $this->assertRegExp('/^http:\/\/mysite\.com\/?$/', clean_url($url));
        }

        foreach ([
            'relative',
            '/relative',
            'relative/',
            '/relative/',
            'relative#fragment',
            'relative/#fragment',
            '/relative#fragment',
            '/relative/#fragment',
        ] as $url) {
            $this->assertRegExp('/^\/?relative\/?$/', clean_url($url));
        }

        foreach ([
            'www.mysite.com',
            'http://www.mysite.com',
            'https://www.mysite.com',
            'ftp://www.mysite.com',
        ] as $url) {
            $this->assertRegExp('/^((https?|ftp):\/\/)?mysite\.com$/', clean_url($url, true));
        }

        foreach ([
            'http://mysite.com',
            'http://mysite.com/',
            'http://www.mysite.com',
            'http://www.mysite.com/',
        ] as $url) {
            $this->assertEquals('http://mysite.com', clean_url($url, true, true));
        }
    }

    /**
     * Test for `create_file()` global function
     * @test
     */
    public function testCreateFile()
    {
        $filename = TMP . 'dirToBeCreated' . DS . 'exampleFile';
        $this->assertTrue(create_file($filename));
        $this->assertStringEqualsFile($filename, '');

        unlink($filename);
        $this->assertTrue(create_file($filename, 'string'));
        $this->assertStringEqualsFile($filename, 'string');

        $this->skipIf(IS_WIN);
        $this->assertFalse(create_file(DS . 'noExistingDir' . DS . 'file'));
    }

    /**
     * Test for `create_tmp_file()` global function
     * @test
     */
    public function testCreateTmpFile()
    {
        $filename = create_tmp_file();
        $this->assertRegexp(sprintf('/^%s[\w\d\.]+$/', preg_quote(TMP, '/')), $filename);
        $this->assertStringEqualsFile($filename, '');

        $filename = create_tmp_file('string');
        $this->assertRegexp(sprintf('/^%s[\w\d\.]+$/', preg_quote(TMP, '/')), $filename);
        $this->assertStringEqualsFile($filename, 'string');
    }

    /**
     * Test for `deprecationWarning()` global function
     * @test
     */
    public function testDeprecationWarning()
    {
        $currentErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        deprecationWarning('This method is deprecated');
        error_reporting($currentErrorReporting);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessageRegExp('/^This method is deprecated/');
        $this->expectExceptionMessageRegExp('/You can disable deprecation warnings by setting `error_reporting\(\)` to `E_ALL & ~E_USER_DEPRECATED`\.$/');
        deprecationWarning('This method is deprecated');
    }

    /**
     * Test for `dir_tree()` global function
     * @test
     */
    public function testDirTree()
    {
        $expectedDirs = [
            TMP . 'exampleDir',
            TMP . 'exampleDir' . DS . '.hiddenDir',
            TMP . 'exampleDir' . DS . 'emptyDir',
            TMP . 'exampleDir' . DS . 'subDir1',
            TMP . 'exampleDir' . DS . 'subDir2',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3',
        ];
        $expectedFiles = createSomeFiles();

        $this->assertEquals([$expectedDirs, $expectedFiles], dir_tree(TMP . 'exampleDir'));
        $this->assertEquals([$expectedDirs, $expectedFiles], dir_tree(TMP . 'exampleDir' . DS));

        //Excludes some files
        foreach ([
            ['file2'],
            ['file2', 'file3'],
            ['.hiddenFile'],
            ['.hiddenFile', 'file2', 'file3'],
        ] as $exceptions) {
            $currentExpectedFiles = array_clean($expectedFiles, function ($value) use ($exceptions) {
                return !in_array(basename($value), $exceptions);
            });
            $this->assertEquals([$expectedDirs, $currentExpectedFiles], dir_tree(TMP . 'exampleDir', $exceptions));
        }

        //Excludes a directory
        list($result) = dir_tree(TMP . 'exampleDir', 'subDir2');
        $this->assertNotContains(TMP . 'exampleDir' . DS . 'subDir2', $result);
        $this->assertNotContains(TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3', $result);

        //Excludes hidden files
        foreach ([true, '.', ['.']] as $exceptions) {
            list($result) = dir_tree(TMP . 'exampleDir', $exceptions);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenDir', $result);

            list(, $result) = dir_tree(TMP . 'exampleDir', $exceptions);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenDir' . DS . 'file7', $result);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenFile', $result);
        }

        //Using a file or a no existing file
        foreach ([create_tmp_file(), TMP . 'noExisting'] as $directory) {
            $this->assertEquals([[], []], dir_tree($directory));
        }
    }

    /**
     * Test for `fileperms_as_octal()` global function
     * @test
     */
    public function testFilepermsAsOctal()
    {
        $this->assertSame(IS_WIN ? '0666' : '0600', fileperms_as_octal(create_tmp_file()));
    }

    /**
     * Test for `fileperms_to_string()` global function
     * @test
     */
    public function testFilepermsToString()
    {
        $this->assertSame('0755', fileperms_to_string(0755));
        $this->assertSame('0755', fileperms_to_string('0755'));
    }

    /**
     * Test for `get_child_methods()` global function
     * @test
     */
    public function testGetChildMethods()
    {
        $this->assertEquals(['throwMethod', 'childMethod', 'anotherChildMethod'], get_child_methods(ExampleChildClass::class));

        //This class has no parent, so the result is similar to the `get_class_methods()` method
        $this->assertEquals(get_class_methods(ExampleClass::class), get_child_methods(ExampleClass::class));

        //No existing class
        $this->assertNull(get_child_methods('\NoExistingClass'));
    }

    /**
     * Test for `get_class_short_name()` global function
     * @test
     */
    public function testGetClassShortName()
    {
        foreach (['\App\ExampleClass', 'App\ExampleClass', ExampleClass::class] as $className) {
            $this->assertEquals('ExampleClass', get_class_short_name($className));
        }
    }

    /**
     * Test for `get_extension()` global function
     * @test
     */
    public function testGetExtension()
    {
        foreach ([
            'backup.sql' => 'sql',
            'backup.sql.bz2' => 'sql.bz2',
            'backup.sql.gz' => 'sql.gz',
            'text.txt' => 'txt',
            'TEXT.TXT' => 'txt',
            'noExtension' => null,
            'txt' => null,
            '.txt' => null,
            '.hiddenFile' => null,
        ] as $filename => $expectedExtension) {
            $this->assertEquals($expectedExtension, get_extension($filename));
        }

        foreach ([
            'backup.sql.gz',
            '/backup.sql.gz',
            '/full/path/to/backup.sql.gz',
            'relative/path/to/backup.sql.gz',
            ROOT . 'backup.sql.gz',
            '/withDot./backup.sql.gz',
            'C:\backup.sql.gz',
            'C:\subdir\backup.sql.gz',
            'C:\withDot.\backup.sql.gz',
        ] as $filename) {
            $this->assertEquals('sql.gz', get_extension($filename));
        }

        foreach ([
            'http://example.com/backup.sql.gz',
            'http://example.com/backup.sql.gz#fragment',
            'http://example.com/backup.sql.gz?',
            'http://example.com/backup.sql.gz?name=value',
        ] as $url) {
            $this->assertEquals('sql.gz', get_extension($url));
        }
    }

    /**
     * Test for `get_hostname_from_url()` global function
     * @test
     */
    public function testGetHostnameFromUrl()
    {
        $this->assertNull(get_hostname_from_url('page.html'));

        foreach (['http://127.0.0.1', 'http://127.0.0.1/'] as $url) {
            $this->assertEquals('127.0.0.1', get_hostname_from_url($url));
        }

        foreach (['http://localhost', 'http://localhost/'] as $url) {
            $this->assertEquals('localhost', get_hostname_from_url($url));
        }

        foreach ([
            '//google.com',
            'http://google.com',
            'http://google.com/',
            'http://www.google.com',
            'https://google.com',
            'http://google.com/page',
            'http://google.com/page?name=value',
        ] as $url) {
            $this->assertEquals('google.com', get_hostname_from_url($url));
        }
    }

    /**
     * Test for `is_absolute()` global function
     * @test
     */
    public function testIsAbsolute()
    {
        $this->assertTrue(is_absolute(DS . 'path' . DS));
        $this->assertFalse(is_absolute('path' . DS));
    }

    /**
     * Test for `is_external_url()` global function
     * @test
     */
    public function testIsExternalUrl()
    {
        foreach ([
            '//google.com',
            '//google.com/',
            'http://google.com',
            'http://google.com/',
            'http://www.google.com',
            'http://www.google.com/',
            'http://www.google.com/page.html',
            'https://google.com',
            'relative.html',
            '/relative.html',
        ] as $url) {
            $this->assertFalse(is_external_url($url, 'google.com'));
        }

        foreach ([
            '//site.com',
            'http://site.com',
            'http://www.site.com',
            'http://subdomain.google.com',
        ] as $url) {
            $this->assertTrue(is_external_url($url, 'google.com'));
        }
    }

    /**
     * Test for `is_html()` global function
     * @test
     */
    public function testIsHtml()
    {
        $this->assertTrue(is_html('<b>string</b>'));
        $this->assertFalse(is_html('string'));
    }

    /**
     * Test for `is_iterable()` global function
     * @test
     */
    public function testIsIterable()
    {
        $this->assertTrue(is_iterable([]));
        $this->assertTrue(is_iterable(new ExampleOfTraversable()));
        $this->assertFalse(is_iterable('string'));
        $this->assertFalse(is_iterable(new ExampleChildClass()));
    }

    /**
     * Test for `is_json()` global function
     * @test
     */
    public function testIsJson()
    {
        $this->assertTrue(is_json('{"a":1,"b":2,"c":3,"d":4,"e":5}'));
        $this->assertFalse(is_json('this is a no json string'));
        $this->assertFalse(is_json(false));
    }

    /**
     * Test for `is_positive()` global function
     * @test
     */
    public function testIsPositive()
    {
        $this->assertTrue(is_positive(1));
        $this->assertTrue(is_positive('1'));

        foreach ([0, -1, 1.1, '0', '1.1'] as $string) {
            $this->assertFalse(is_positive($string));
        }
    }

    /**
     * Test for `is_slash_term()` global function
     * @test
     */
    public function testIsSlashTerm()
    {
        foreach ([
            'path/',
            '/path/',
            'path\\',
            '\\path\\',
        ] as $path) {
            $this->assertTrue(is_slash_term($path));
        }

        foreach ([
            'path',
            '/path',
            '\\path',
            'path.ext',
            '/path.ext',
        ] as $path) {
            $this->assertFalse(is_slash_term($path));
        }
    }

    /**
     * Test for `is_stringable()` global function
     * @test
     */
    public function testIsStringable()
    {
        foreach (['1', 1, 1.1, -1, 0, true, false] as $value) {
            $this->assertTrue(is_stringable($value));
        }

        foreach ([null, [], new stdClass()] as $value) {
            $this->assertFalse(is_stringable($value));
        }

        //This class implements the `__toString()` method
        $this->assertTrue(is_stringable(new ExampleOfStringable()));

        $errorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(can_be_string('string'));
        error_reporting($errorReporting);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('`can_be_string()` function is deprecated. Use `is_stringable()');
        can_be_string('string');
    }

    /**
     * Test for `is_url()` global function
     * @test
     */
    public function testIsUrl()
    {
        foreach ([
            'https://www.example.com',
            'http://www.example.com',
            'www.example.com',
            'http://example.com',
            'http://example.com/file',
            'http://example.com/file.html',
            'www.example.com/file.html',
            'http://example.com/subdir/file',
            'ftp://www.example.com',
            'ftp://example.com',
            'ftp://example.com/file.html',
            'http://example.com/name-with-brackets(3).jpg',
        ] as $url) {
            $this->assertTrue(is_url($url), 'Failed asserting that `' . $url . '` is a valid url');
        }

        foreach ([
            'example.com',
            'folder',
            DS . 'folder',
            DS . 'folder' . DS,
            DS . 'folder' . DS . 'file.txt',
        ] as $url) {
            $this->assertFalse(is_url($url));
        }
    }

    /**
     * Test for `is_writable_resursive()` global function
     * @test
     */
    public function testIsWritableRecursive()
    {
        $this->assertTrue(is_writable_resursive(TMP));
        $this->assertFalse(is_writable_resursive(TMP . 'noExisting'));
    }

    /**
     * Test for `objects_map()` global function
     * @test
     */
    public function testObjectsMap()
    {
        $arrayOfObjects = [new ExampleClass(), new ExampleClass()];

        $result = objects_map($arrayOfObjects, 'setProperty', ['publicProperty', 'a new value']);
        $this->assertEquals(['a new value', 'a new value'], $result);

        foreach ($arrayOfObjects as $object) {
            $this->assertEquals('a new value', $object->publicProperty);
        }

        //With a no existing method
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Class `' . ExampleClass::class . '` does not have a method `noExistingMethod`');
        objects_map([new ExampleClass()], 'noExistingMethod');
    }

    /**
     * Test for `rmdir_recursive()` global function
     * @test
     */
    public function testRmdirRecursive()
    {
        $files = createSomeFiles();
        rmdir_recursive(TMP . 'exampleDir');
        array_map([$this, 'assertFileNotExists'], $files);
        array_map([$this, 'assertDirectoryNotExists'], array_map('dirname', $files));

        //Does not delete a file
        $filename = create_tmp_file();
        rmdir_recursive($filename);
        $this->assertFileExists($filename);
    }

    /**
     * Test for `rtr()` global function
     * @test
     */
    public function testRtr()
    {
        $this->assertSame('my/folder', rtr(ROOT . 'my' . DS . 'folder'));

        //Resets the ROOT value, removing the final slash
        putenv('ROOT=' . rtrim(ROOT, DS));
        $this->assertSame('my/folder', rtr(ROOT . 'my' . DS . 'folder'));
    }

    /**
     * Test for `string_ends_with()` global function
     * @test
     */
    public function testStringEndsWith()
    {
        $string = 'a test with some words';
        foreach (['', 's', 'some words', $string] as $var) {
            $this->assertTrue(string_ends_with($string, $var));
        }
        foreach ([' ', 'b', 'a test'] as $var) {
            $this->assertFalse(string_ends_with($string, $var));
        }
    }

    /**
     * Test for `string_starts_with()` global function
     * @test
     */
    public function testStringStartsWith()
    {
        $string = 'a test with some words';
        foreach (['', 'a', 'a test', $string] as $var) {
            $this->assertTrue(string_starts_with($string, $var));
        }
        foreach ([' ', 'some words', 'test'] as $var) {
            $this->assertFalse(string_starts_with($string, $var));
        }
    }

    /**
     * Test for `unlink_resursive()` global function
     * @test
     */
    public function testUnlinkRecursive()
    {
        $files = createSomeFiles();

        //Creates some links
        if (!IS_WIN) {
            foreach ([create_tmp_file(), create_tmp_file()] as $filename) {
                $link = TMP . 'exampleDir' . DS . 'link_to_' . basename($filename);
                @symlink($filename, $link);
                $files[] = $link;
            }
        }

        unlink_recursive(TMP . 'exampleDir');
        array_map([$this, 'assertFileNotExists'], $files);

        //Directories still exist
        $this->skipIf(IS_WIN);
        array_map([$this, 'assertDirectoryExists'], array_map('dirname', $files));
    }

    /**
     * Test for `url_to_absolute()` global function
     * @test
     */
    public function testUrlToAbsolute()
    {
        foreach (['http', 'https', 'ftp'] as $scheme) {
            $paths = [
                $scheme . '://localhost/mysite/subdir/anothersubdir',
                $scheme . '://localhost/mysite/subdir/anothersubdir/a_file.html',
            ];

            foreach ($paths as $path) {
                foreach ([
                    'http://localhost/mysite' => 'http://localhost/mysite',
                    'http://localhost/mysite/page.html' => 'http://localhost/mysite/page.html',
                    '//localhost/mysite' => $scheme . '://localhost/mysite',
                    'page2.html' => $scheme . '://localhost/mysite/subdir/anothersubdir/page2.html',
                    '/page3.html' => $scheme . '://localhost/page3.html',
                    '../page4.html' => $scheme . '://localhost/mysite/subdir/page4.html',
                    '../../page5.html' => $scheme . '://localhost/mysite/page5.html',
                    'http://external.com' => 'http://external.com',
                ] as $url => $expected) {
                    $this->assertSame($expected, url_to_absolute($path, $url));
                }
            }
        }

        $this->assertSame('http://example.com/page6.html', url_to_absolute('http://example.com', 'page6.html'));
    }

    /**
     * Test for `which()` global function on Unix
     * @test
     */
    public function testWhichOnUnix()
    {
        $expected = IS_WIN ? '"C:\Program Files\Git\usr\bin\cat.exe"' : '/bin/cat';
        $this->assertEquals($expected, which('cat'));
        $this->assertNull(which('noExistingBin'));
    }
}
