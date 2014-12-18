<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DTL\Glob;

use Prophecy\PhpUnit\ProphecyTestCase;
use DTL\Glob\Parser\SelectorParser;

class SelectorParserTest extends ProphecyTestCase
{
    /**
     * @var SelectorParser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new SelectorParser();
    }

    public function provideParse()
    {
        return array(
            array(
                '/\bar',
                array(
                    array('\bar', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),

            array(
                '/z*',
                array(
                    array('z*', SelectorParser::T_PATTERN | SelectorParser::T_LAST),
                ),
            ),
            array(
                '/',
                array(
                ),
            ),
            array(
                '/foo',
                array(
                    array('foo', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),
            array(
                '/foo/bar',
                array(
                    array('foo', SelectorParser::T_STATIC),
                    array('bar', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),
            array(
                '/*/bar',
                array(
                    array('*', SelectorParser::T_PATTERN),
                    array('bar', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),
            array(
                '/\*/bar',
                array(
                    array('*', SelectorParser::T_STATIC),
                    array('bar', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),

            // literal asterix "\\\*" should be "\\*"
            array(
                '/\\\\\*/boo',
                array(
                    array('\\\\*', SelectorParser::T_STATIC),
                    array('boo', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),

            // one literal asterix and a non-espaped asterix
            array(
                '/\\\\\*/boo/\\\\*/booze',
                array(
                    array('\\\\*', SelectorParser::T_STATIC),
                    array('boo', SelectorParser::T_STATIC),
                    array('\\\\*', SelectorParser::T_PATTERN),
                    array('booze', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),

            // non-escaped asterix ("\\*")
            array(
                '/\\\*/boo',
                array(
                    array('\\\\*', SelectorParser::T_PATTERN),
                    array('boo', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),

            // two non-espaped asterixes
            array(
                '/\\\*/boo/\\\*/boom',
                array(
                    array('\\\\*', SelectorParser::T_PATTERN),
                    array('boo', SelectorParser::T_STATIC),
                    array('\\\\*', SelectorParser::T_PATTERN),
                    array('boom', SelectorParser::T_STATIC | SelectorParser::T_LAST),
                ),
            ),
        );
    }

    /**
     * @dataProvider provideParse
     */
    public function testParse($path, $expected)
    {
        $res = $this->parser->parse($path);
        $this->assertSame($res, $expected);
    }
}
