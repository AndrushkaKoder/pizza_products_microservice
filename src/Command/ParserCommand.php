<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Service\Binder\ProductCategoryBinder;
use App\Service\File\FileSaver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:parser',
    description: 'Parse products from outer site',
)]
class ParserCommand extends Command
{
    private const PAGE_URL = 'https://tashirpizza.ru/kaluga';
    private const BASE_URL = 'https://tashirpizza.ru';

    public function __construct(
        private EntityManagerInterface $manager,
        private ProductCategoryBinder  $binder,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $page = $this->getPage();

        if (!$page) {
            $output->write('Page not found. Check cookies' . PHP_EOL);
            return Command::FAILURE;
        }

        $data = $this->getData();

        if (!$data) {
            $output->write('Parse errors' . PHP_EOL);
            return Command::FAILURE;
        }

        $result = $this->saveData($data);
        if (!$result) {
            $output->write($result . PHP_EOL);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @return string
     * Parse page
     */
    private function getPage(): string
    {
        $headers = [
            "User-Agent" => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
            "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "Accept-Language" => "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
            //Куки на сайте https://tashirpizza.ru генерируются раз в час (антипарсинг). При запуске команды перейти на сайт и взять свежие куки
            "Cookie" => "__lhash_=bf72586d0261689839dee4af732b114c; tmr_lvid=60b3df8a5f13202472956435efe39337; tmr_lvidTS=1736491497200; _ym_uid=1736491498806050096; _ym_d=1736491498; __js_p_=946,10800,0,0,0; __jhash_=319; __jua_=Mozilla%2F5.0%20%28X11%3B%20Linux%20x86_64%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F131.0.0.0%20Safari%2F537.36; __hash_=d5e31958d1273ebc878a85e5a7d08bf7; city_id=2; cookie_name=c414f3c580bb138e51f9b688fa7b9a44518a947d~6784af942bab45-97191185; _gid=GA1.2.1702547553.1736748950; _gat_UA-163981186-1=1; _ym_isad=2; _ga_GKW2YSN7N0=GS1.1.1736748950.2.0.1736748950.60.0.0; _ga=GA1.1.2005579809.1736491497; city_selected=2; domain_sid=rx5FJtL9P3vy07fKqayRK%3A1736748951571; tmr_detect=0%7C1736748952706",
            "Referer" => "https://tashirpizza.ru/kaluga",
            'Sec-Ch-Ua' => 'Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24',
            'Upgrade-Insecure-Requests' => 1,
            'Sec-Fetch-User' => '?1',
            'Sec-Fetch-Site' => 'same-origin'
        ];

        $client = HttpClient::create();

        return $client->request('GET', self::PAGE_URL, ['headers' => $headers])->getContent();
    }

    /**
     * @return array
     * Parse categories
     */
    private function getData(): array
    {
        $crawler = new Crawler($this->getPage());

        $data = $crawler->filter('.catalogs')->children('section')->each(function (Crawler $catalog) {
            $result = [];

            $catalogTitle = $catalog->filter('h1');
            if ($catalogTitle->count()) {
                $catalogTitle = $catalogTitle->text();
            } else {
                $catalogTitle = $catalog->filter('strong')->text();
            }

            if ($catalog->count()) {
                $result[$catalogTitle] = $catalog->filter('.products')
                    ->filter('figure')
                    ->each(function (Crawler $product) {

                        $result = [];

                        $img = $product->filter('img');
                        if ($img->count()) {
                            $result['img'] = self::BASE_URL . $img->attr('data-src');
                        }

                        $name = $product->filter('.name');
                        if ($name->count()) {
                            $result['name'] = trim($name->innerText());
                        }

                        $description = $product->filter('.descr');
                        if ($description->count()) {
                            $result['description'] = trim($description->innerText());
                        }

                        $price = $product->filter('.price');
                        if ($price->count()) {
                            $result['price'] = preg_replace('/\D+/', '', $price->innerText());
                        }

                        $weight = $product->filter('.weight');
                        if ($weight->count()) {
                            $result['weight'] = preg_replace('/\D+/', '', $weight->innerText());
                        }

                        return $result;
                    });
            }

            return $result;
        });

        $result = [];

        if ($data) {
            foreach ($data as $key => $item) {
                foreach ($item as $categoryName => $products) {
                    $result[$categoryName] = $products;
                }
            }
        }

        return $result;
    }

    private function saveData(array $data): bool|string
    {
        foreach ($data as $key => $items) {
            $category = new Category();
            $category->setName($key);

            foreach ($items as $item) {
                $product = new Product();
                $product->setName($item['name']);
                $product->setDescription($item['description'] ?? '');
                $product->setPrice(intval($item['price'] ?? 0));

                $this->binder->setProduct($product)
                    ->setCategory($category)
                    ->attach();

                if ($uploadImage = $item['img']) {
                    $image = new Image();
                    $file = (new FileSaver($uploadImage))->save();
                    if (is_object($file)) {
                        $image->setName($file->getFilename());
                        $image->setSource('/upload/' . $file->getFilename());
                        $image->setProduct($product);

                        $this->manager->persist($product);
                        $this->manager->persist($image);
                    }
                }
            }

            $this->manager->flush();
        }

        return true;
    }
}
