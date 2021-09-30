<?php

namespace CreativeStyle\ParcellabIntegration\Test\Integration\Model\RequestPreprocessors;

class OrderCreationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation
     */
    protected $orderCreationRequestPreprocessor;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->orderCreationRequestPreprocessor = $this->objectManager->create(\CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\OrderCreation::class);
        $this->imageHelper = $this->objectManager->create(\Magento\Catalog\Helper\Image::class);
        $this->productRepository = $this->objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture loadShipments
     * @magentoConfigFixture default_store parcellab/general/test_mode_enabled 1
     */
    public function testItPreparesCorrectPayloadForParcellab()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->objectManager->create(\Magento\Sales\Model\Order\Shipment::class);
        $shipment->loadByIncrementId('100000001');

        $payload = $this->orderCreationRequestPreprocessor->preparePayload(
            [
                'shipment' => $shipment
            ]
        );

        $this->assertEquals($this->getExpectedPayload($shipment), $payload);
    }

    public static function loadShipments()
    {
        include __DIR__ . '/../../_files/shipment_list.php';
    }

    private function getExpectedPayload($shipment)
    {
        $productId = current($shipment->getItems())->getProductId();
        $product = $this->productRepository->getById($productId);
        $productImageUrl = $this->getProductImageUrl($product);
        $shippingAddress = $shipment->getShippingAddress();

        return [
            'json' => [
                'client' => 'default',
                'orderNo' => $shipment->getIncrementId(),
                'recipient' => 'firstname lastname',
                'customerNo' => $shippingAddress->getId(),
                'email' => 'customer@null.com',
                'street' => 'street',
                'city' => 'Los Angeles',
                'phone' => '11111111',
                'zip_code' => '11111',
                'language_iso3' => 'en',
                'order_date' => $shipment->getCreatedAt(),
                'articles' => [
                    [
                        'articleNo' => 'simple',
                        'articleName' => 'Simple Product',
                        'articleCategory' => 'Default Category',
                        'articleImageUrl' => $productImageUrl,
                        'quantity' => '2.0000',
                    ]
                ],
                'customFields' => [
                    'testShipment' => true
                ]
            ]
        ];
    }

    protected function getProductImageUrl(\Magento\Catalog\Api\Data\ProductInterface $product): string
    {
        return $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
    }
}
