<?php

/*
 * This file is part of the phlexible indexer page package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\IndexerPagerBundle\Tests\Indexer\ContentFilter;

use Phlexible\Bundle\IndexerPagerBundle\Indexer\ContentFilter\HtmlContentFilter;

/**
 * HTML content filter test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HtmlContentFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterControlCharacters()
    {
        $html = "A\nB\rC\tD";

        $filter = new HtmlContentFilter();
        $result = $filter->filter($html);

        $this->assertSame('A B C D', $result);
    }

    public function testFilterWhitespace()
    {
        $html = 'A     B';

        $filter = new HtmlContentFilter();
        $result = $filter->filter($html);

        $this->assertSame('A B', $result);
    }

    public function testFilterTags()
    {
        $html = '<b>A</b><i>B</i>';

        $filter = new HtmlContentFilter();
        $result = $filter->filter($html);

        $this->assertSame('A B', $result);
    }
}
