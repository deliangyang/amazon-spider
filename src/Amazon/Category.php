<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/17/18
 * Time: 10:02 PM
 */
namespace AmazonSpider\Amazon;

class Category extends BaseHttp
{

    protected $url = 'https://www.amazon.com/Best-Sellers/zgbs/ref=zg_bs_unv_hg_0_hg_1';

    public function execute()
    {
        $this->crawlCategory();
        $this->cacheCategory();
    }

    protected function crawlCategory()
    {
        $categories = [];
        $crawl = $this->getHtmlDom($this->url);
        if ($crawl) {
            $nodes = $crawl->filterXPath('//ul[@id="zg_browseRoot"]/ul/li/a');
            foreach ($nodes as $key => $val) {
                $categories[] = [
                    'name' => $val->textContent,
                    'url' => $val->getAttribute('href'),
                ];
            }
        }
        return $categories;
    }

    public function cacheCategory()
    {
        $filename = __DIR__ . '/../../config/categories.php';
        $categories = var_export($this->crawlCategory(), true);
        $content =<<<PHP
<?php
return {$categories};
PHP;
        file_put_contents($filename, $content);
    }
}
