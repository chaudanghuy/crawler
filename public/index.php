<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Cache\Backend\Libmemcached as BackMemCached;

error_reporting(E_ALL);
ini_set("display_errors", 1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers the services that
     * provide a full stack framework. These default services can be overidden with custom ones.
     */
    $di = new FactoryDefault();

    // Set up the database service
    $di->set(
            'db', function () {
        return new PdoMysql(
                [
            'host' => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname' => $config->database->dbname,
                ]
        );
    }
    );

    // Cache data for one hour
    $frontCache = new FrontData(
            [
        "lifetime" => 3600,
            ]
    );

    // Create the component that will cache "Data" to a "Memcached" backend
    // Memcached connection settings
    $cache = new BackMemCached(
            $frontCache, [
        "servers" => [
            [
                "host" => "127.0.0.1",
                "port" => "11211",
                "weight" => "1",
            ]
        ]
            ]
    );

    /**
     * Include Services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Starting the application
     * Assign service locator to the application
     */
    $app = new Micro($di);

    $app->before(function() use ($app) {
        $origin = $app->request->getHeader("ORIGIN") ? $app->request->getHeader("ORIGIN") : '*';

        $app->response->setHeader("Access-Control-Allow-Origin", $origin)
                ->setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE,OPTIONS')
                ->setHeader("Access-Control-Allow-Headers", 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
                ->setHeader("Access-Control-Allow-Credentials", true);
    });

    /**
     * Include Application
     */
    include APP_PATH . '/app.php';


    $app->get("/crawl/categories", function () use ($app) {
        include APP_PATH . '/vendor/autoload.php';
        
        $phql = "SELECT * FROM Categories";

        $categories = $app->modelsManager->executeQuery($phql);

        $data = array();
        foreach ($categories as $category) {
            $categoryId = $category->id;

            // Parse URL
            $parts = parse_url(trim($category->crawl_url));

            parse_str($parts['query'], $query);

            $GdscCd = !empty($query['GdscCd']) ? $query['GdscCd'] : null;

            // Setup params for crawling
            $page = 1;
            $GdlcCd = $query['GdlcCd'];
            $GdmcCd = $query['GdmcCd'];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "http://gcategory.gmarket.co.kr/SearchService/SeachListTemplateAjax");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "type=LIST&page=$page&pageSize=60&keyword=&GdlcCd=$GdlcCd&GdmcCd=$GdmcCd&GdscCd=$GdscCd&priceStart=&priceEnd=&searchType=LIST&IsOversea=False&isDeliveryFeeFree=&isDiscount=False&isGmileage=False&isGStamp=False&isGmarketBest=&orderType=&listType=LIST&IsBookCash=False&IsGlobalSort=True&DelFee=&CurrPage=cpp&isGlobalSite=true");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

            $result = curl_exec($ch);

            $decode = json_decode($result);

            $dom = Sunra\PhpSimple\HtmlDomParser::str_get_html(trim($decode->message));

            foreach ($dom->find("tr") as $elem) {
                preg_match_all('/<a .*?>(.*?)<\/a>/', $elem->find('li[class=discount_price]', 0)->innertext, $matches);

                $data[] = [
                    'title' => $elem->find('.thumb_nail', 0)->alt,
                    'img' => $elem->find('.thumb_nail', 0)->src,
                    'shipping' => $elem->find('.cpp_icon', 0)->plaintext,
                    'origin_price' => !empty($elem->find('.orgin_price', 0)->innertext) ? $elem->find('.orgin_price', 0)->innertext : 0,
                    'href' => sprintf("%s%s", 'http://item2.gmarket.co.kr/English/detailview/item.aspx?goodscode=', $elem->find('.item_name a', 0)->gdno),
                    'ship_fee' => $elem->find('td[class=center]', 0)->plaintext,
                    'discount_price' => $matches[1][0],
                    'exchange_rate' => !empty($elem->find('.exchange_rate', 0)->innertext) ? $elem->find('.exchange_rate', 0)->innertext : 0,
                    'discount' => !empty($elem->find('li[class=discount]', 0)->innertext) ? $elem->find('li[class=discount]', 0)->innertext : 0,
                    'iframe_url' => 'http://mg.gmarket.co.kr/ItemDetail?GoodsCode=' . $elem->find('.item_name a', 0)->gdno,
                ];
            }

            break;
        }

        $response = new Response();

        $response->setJsonContent($data);

        return $response;
    });

    /**
     * Handle the request
     */
    $app->handle();
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
