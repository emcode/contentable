<?php

namespace Contentable\Driver;

class DriverExample01Test extends AbstractGlobDriverTest
{
    protected function getFixturePath()
    {
        return "./examples/article-ex-01";
    }

    protected function getContentTypeName()
    {
        return "article";
    }

    public function getExistingArticleSlugs()
    {
        return [
            ['another-lorem-ipsum'],
            ['some-interesting-article'],
            ['yet-more-code-to-test']
        ];
    }

    /**
     * @dataProvider getExistingArticleSlugs
     */
    public function testDriverFindsExistingEntities($articleSlug)
    {
        $entity = $this->driver->loadEntity(['slug' => $articleSlug]);
        $this->assertInstanceOf('Contentable\Entity', $entity);
    }

    public function getNonExistingArticleSlugs()
    {
        return [
            ['non-existing-article-slug'],
            ['lorem'],
            ['ome-interesting-article'],
            ['more-to-test'],
            ['-']
        ];
    }

    /**
     * @dataProvider getNonExistingArticleSlugs
     * @expectedException \Contentable\Component\Exception\SourceFileNotFoundException
     */
    public function testDriverThrowsOnNonExistingEntities($nonExistingArticleSlug)
    {
        $this->driver->loadEntity(['slug' => $nonExistingArticleSlug]);
    }

    public function testFindMethodWithoutParamsReturnsArray()
    {
        $result = $this->driver->find();
        $this->assertInternalType('array', $result);
    }

    /**
     * @depends testFindMethodWithoutParamsReturnsArray
     */
    public function testFindMethodWithoutParamsFindsAllArticles()
    {
        $result = $this->driver->find();
        $this->assertCount(3, $result, 'All three articles were found');
    }

    /**
     * @depends testFindMethodWithoutParamsFindsAllArticles
     */
    public function testFindMethodWithoutParamsReturnsArrayWithAllPossibleDynamicTokens()
    {
        $allTokens = $this->driver->getBaseComponentPathTokens();
        $result = $this->driver->find();

        foreach($result as $resultIndex => $resultItem)
        {
            foreach($allTokens as $tokenName => $tokenPattern)
            {
                $this->assertArrayHasKey($tokenName, $resultItem, sprintf('%s result item has %s token defined', $resultIndex, $tokenName));
            }
        }
    }
}