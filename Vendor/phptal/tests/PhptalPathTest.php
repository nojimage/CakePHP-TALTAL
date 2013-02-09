<?php
/**
 * PHPTAL templating engine
 *
 * PHP Version 5
 *
 * @category HTML
 * @package  PHPTAL
 * @author   Laurent Bedubourg <lbedubourg@motion-twin.com>
 * @author   Kornel Lesiński <kornel@aardvarkmedia.co.uk>
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version  SVN: $Id: PhptalPathTest.php 888 2010-06-08 09:48:33Z kornel $
 * @link     http://phptal.org/
 */



class PhptalPathTest_DummyClass
{
    public $foo;

    protected function protTest()
    {
        return 'prot-method';
    }

    public $protTest = 'prot-property';

    public function pubTest()
    {
        return 'pub-method';
    }

    public $pubTest = 'pub-property';
}

class PhptalPathTest extends PHPTAL_TestCase
{
    function testZeroIndex()
    {
        $data   = array(1, 0, 3);
        $result = PHPTAL_Context::path($data, '0');
        $this->assertEquals(1, $result);
    }

    function testProtectedMethodIgnored()
    {
        $tpl = $this->newPHPTAL();
        $tpl->obj = new PhptalPathTest_DummyClass();
        $tpl->setSource('<test tal:content="obj/protTest"></test>');

        $this->assertEquals('<test>prot-property</test>', $tpl->execute());
    }

    function testPublicMethodFirst()
    {
        $tpl = $this->newPHPTAL();
        $tpl->obj = new PhptalPathTest_DummyClass();
        $tpl->setSource('<test tal:content="obj/pubTest"></test>');

        $this->assertEquals('<test>pub-method</test>', $tpl->execute());
    }

    function testDefinedButNullProperty()
    {
        $src = <<<EOS
<span tal:content="o/foo"/>
<span tal:content="o/foo | string:blah"/>
<span tal:content="o/bar" tal:on-error="string:ok"/>
EOS;
        $exp = <<<EOS
<span></span>
<span>blah</span>
ok
EOS;

        $tpl = $this->newPHPTAL();
        $tpl->setSource($src, __FILE__);
        $tpl->o = new PhptalPathTest_DummyClass();
        $res = $tpl->execute();

        $this->assertEquals($exp, $res);
    }
}


