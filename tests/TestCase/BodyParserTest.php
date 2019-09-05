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

use JeffersonSimaoGoncalves\Tools\BodyParser;
use JeffersonSimaoGoncalves\Tools\TestSuite\TestCase;

/**
 * BodyParserTest class
 */
class BodyParserTest extends TestCase
{
    /**
     * Test for `extractLinks()` method
     * @test
     */
    public function testExtractLinks()
    {
        $getExtractedLinksMethod = function ($html) {
            return (new BodyParser($html, 'http://localhost'))->extractLinks();
        };

        $expected = [
            'http://localhost/page.html',
            'http://localhost/area.htm',
            'http://localhost/file.mp3',
            'http://localhost/helloworld.swf',
            'http://localhost/frame1.html',
            'http://localhost/frame2.html',
            'http://localhost/pic.jpg',
            'http://localhost/style.css',
            'http://localhost/script.js',
            'http://localhost/file2.mp3',
            'http://localhost/subtitles_en.vtt',
            'http://localhost/movie.mp4',
        ];
        $html = '<a href="/page.html#fragment">Link</a>
<map name="example"><area href="area.htm"></map>
<audio src="/file.mp3"></audio>
<embed src="helloworld.swf">
<frame src="frame1.html"></frame>
<iframe src="frame2.html"></iframe>
<img src="pic.jpg" />
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="script.js" />
<audio><source src="file2.mp3" type="audio/mpeg"></audio>
<video><track src="subtitles_en.vtt"></video>
<video src="//localhost/movie.mp4"></video>';
        $this->assertEquals($expected, $getExtractedLinksMethod($html));

        $html = '<html><body>' . $html . '</body></html>';
        $this->assertEquals($expected, $getExtractedLinksMethod($html));

        $html = '<b>No links here!</b>';
        $this->assertEquals([], $getExtractedLinksMethod($html));

        $html = '<a href="page.html">Link</a>' . PHP_EOL . '<a href="http://localhost/page.html">Link</a>';
        $expected = ['http://localhost/page.html'];
        $this->assertEquals($expected, $getExtractedLinksMethod($html));

        //Checks that the result is the same as that saved in the
        //  `extractedLinks` property as a cache
        $expected = ['link.html'];
        $BodyParser = new BodyParser($html, 'http://localhost');
        $this->setProperty($BodyParser, 'extractedLinks', $expected);
        $this->assertEquals($expected, $BodyParser->extractLinks());

        //No HTML
        $this->assertEquals([], $getExtractedLinksMethod('no html'));
    }
}
