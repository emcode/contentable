<?php

namespace Contentable\Driver;

class I18nExample02Test extends AbstractGlobDriverTest
{
    protected function getFixturePath()
    {
        return "./examples/i18n-ex-02";
    }

    protected function getContentTypeName()
    {
        return "post";
    }

    public function getExistingArticleSlugs()
    {
        return [
            ['english-code-to-test', 'en-us'],
            ['english-interesting-article', 'en-us'],
            ['english-lorem-ipsum', 'en-us'],
            ['polish-lorem-ipsum', 'pl-pl'],
            ['polish-interesting-article', 'pl-pl'],
            ['polish-code-to-test', 'pl-pl'],
        ];
    }

    /**
     * @dataProvider getExistingArticleSlugs
     */
    public function testDriverFindsExistingEntities($articleSlug, $language)
    {
        $entity = $this->driver->loadEntity(['slug' => $articleSlug, 'lang' => $language]);
        $this->assertInstanceOf('Contentable\Entity', $entity);
    }

    public function getNonExistingArticleSlugs()
    {
        return [
            ['english-code-to-test', 'pl-pl'],
            ['english-interesting-articl', 'en-us'],
            ['-', 'en-us'],
            ['polish', 'pl-pl'],
            ['olish-interesting-articl', 'pl-pl'],
            ['polish-code-test', 'pl-pl'],
        ];
    }

    /**
     * @dataProvider getNonExistingArticleSlugs
     * @expectedException \Contentable\Component\Exception\SourceFileNotFoundException
     */
    public function testDriverThrowsOnNonExistingEntities($articleSlug, $language)
    {
        $this->driver->loadEntity(['slug' => $articleSlug, 'lang' => $language]);
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
        $this->assertCount(6, $result, 'All three articles were found');
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