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
 * @version  SVN: $Id: TalesExistTest.php 888 2010-06-08 09:48:33Z kornel $
 * @link     http://phptal.org/
 */



class TalesExistTest extends PHPTAL_TestCase
{
    function testLevel1()
    {
        $tpl = $this->newPHPTAL('input/tales-exist-01.html');
        $tpl->foo = 1;
        $res = $tpl->execute();
        $res = normalize_html($res);
        $exp = normalize_html_file('output/tales-exist-01.html');
        $this->assertEquals($exp, $res, $tpl->getCodePath());
    }

    function testLevel2()
    {
        $o = new stdClass();
        $o->foo = 1;
        $tpl = $this->newPHPTAL('input/tales-exist-02.html');
        $tpl->o = $o;
        $res = $tpl->execute();
        $res = normalize_html($res);
        $exp = normalize_html_file('output/tales-exist-02.html');
        $this->assertEquals($exp, $res);
    }
}

