<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\IndexerElementBundle\Tests\Indexer\IndexibleVoter;

use Phlexible\Bundle\IndexerBundle\Document\DocumentIdentity;
use Phlexible\Bundle\IndexerElementBundle\Indexer\DocumentDescriptor;
use Phlexible\Bundle\IndexerElementBundle\Indexer\IndexibleVoter\ChainIndexibleVoter;
use Phlexible\Bundle\IndexerElementBundle\Indexer\IndexibleVoter\IndexibleVoterInterface;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeNode;

/**
 * Chain indexible voter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainIndexibleVoterTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexibleChainReturnsAllowOnAllAllowed()
    {
        $node = new ContentTreeNode();
        $siteroot = new Siteroot();
        $identity = new DocumentIdentity('element_74_de');
        $descriptor = new DocumentDescriptor($identity, $node, $siteroot, 'de');

        $voter1 = $this->prophesize(IndexibleVoterInterface::class);
        $voter2 = $this->prophesize(IndexibleVoterInterface::class);

        $voter1->isIndexible($descriptor)->willReturn(ChainIndexibleVoter::VOTE_ALLOW);
        $voter2->isIndexible($descriptor)->willReturn(ChainIndexibleVoter::VOTE_ALLOW);

        $voter = new ChainIndexibleVoter(array($voter1->reveal(), $voter2->reveal()));
        $result = $voter->isIndexible($descriptor);

        $this->assertSame(ChainIndexibleVoter::VOTE_ALLOW, $result);
    }

    public function testIndexibleChainReturnsDenyOnSingleDeny()
    {
        $node = new ContentTreeNode();
        $siteroot = new Siteroot();
        $identity = new DocumentIdentity('element_74_de');
        $descriptor = new DocumentDescriptor($identity, $node, $siteroot, 'de');

        $voter1 = $this->prophesize(IndexibleVoterInterface::class);
        $voter2 = $this->prophesize(IndexibleVoterInterface::class);

        $voter1->isIndexible($descriptor)->willReturn(ChainIndexibleVoter::VOTE_ALLOW);
        $voter2->isIndexible($descriptor)->willReturn(ChainIndexibleVoter::VOTE_DENY);

        $voter = new ChainIndexibleVoter(array($voter1->reveal(), $voter2->reveal()));
        $result = $voter->isIndexible($descriptor);

        $this->assertSame(ChainIndexibleVoter::VOTE_DENY, $result);
    }

    public function testIndexibleChainReturnsDenyOnFirstDeny()
    {
        $node = new ContentTreeNode();
        $siteroot = new Siteroot();
        $identity = new DocumentIdentity('element_74_de');
        $descriptor = new DocumentDescriptor($identity, $node, $siteroot, 'de');

        $voter1 = $this->prophesize(IndexibleVoterInterface::class);
        $voter2 = $this->prophesize(IndexibleVoterInterface::class);

        $voter1->isIndexible($descriptor)->willReturn(ChainIndexibleVoter::VOTE_DENY);
        $voter2->isIndexible($descriptor)->shouldNotBeCalled();

        $voter = new ChainIndexibleVoter(array($voter1->reveal(), $voter2->reveal()));
        $result = $voter->isIndexible($descriptor);

        $this->assertSame(ChainIndexibleVoter::VOTE_DENY, $result);
    }
}