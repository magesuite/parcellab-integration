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

    public static function setUpBeforeClass(): void
    {
        require(__DIR__ . '/../../_files/store.php');
        require(__DIR__ . '/../../_files/configuration.php');
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        require(__DIR__ . '/../../_files/store_rollback.php');
        require(__DIR__ . '/../../_files/configuration_rollback.php');
        parent::tearDownAfterClass();
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @magentoDataFixture loadOrder
     * @magentoDataFixture loadShipment
     * @magentoConfigFixture default_store parcellab/general/test_mode_enabled 1
     */
    public function testItPreparesCorrectShipmentPayloadForParcellab()
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->objectManager->create(\Magento\Sales\Model\Order\Shipment::class);
        $shipment->loadByIncrementId('100000001');

        $payload = $this->orderCreationRequestPreprocessor->preparePayload(
            [
                'shipment' => $shipment
            ]
        );

        $this->assertEquals($this->getExpectedShipmentPayload($shipment), $payload);
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @magentoDataFixture loadOrder
     * @magentoDataFixture loadShipment
     * @magentoConfigFixture default_store parcellab/general/test_mode_enabled 1
     */
    public function testItPreparesCorrectOrderPayloadForParcellab()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->create(\Magento\Sales\Model\Order::class);
        $order->loadByIncrementId('100000001');

        $payload = $this->orderCreationRequestPreprocessor->preparePayload(
            [
                'order' => $order
            ]
        );

        $this->assertEquals($this->getExpectedOrderPayload($order), $payload);
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return array[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getExpectedShipmentPayload(\Magento\Sales\Model\Order\Shipment $shipment): array
    {
        $productId = current($shipment->getItems())->getProductId();
        $product = $this->productRepository->getById($productId);
        $productImageUrl = $this->getProductImageUrl($product);
        $shippingAddress = $shipment->getShippingAddress();

        return [
            'json' => [
                'client' => 'BEL',
                'orderNo' => $shipment->getOrder()->getIncrementId(),
                'recipient' => 'firstname lastname',
                'customerNo' => $shippingAddress->getId(),
                'email' => 'customer@null.com',
                'street' => 'street',
                'city' => 'city',
                'phone' => '11111111',
                'zip_code' => '1111',
                'language_iso3' => 'nl',
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
                ],
                'xid' => $shipment->getOrder()->getIncrementId()
            ]
        ];
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getExpectedOrderPayload(\Magento\Sales\Model\Order $order): array
    {
        $productId = current($order->getItems())->getProductId();
        $product = $this->productRepository->getById($productId);
        $productImageUrl = $this->getProductImageUrl($product);
        $shippingAddress = $order->getShippingAddress();

        return [
            'json' => [
                'client' => 'BEL',
                'orderNo' => $order->getIncrementId(),
                'recipient' => 'firstname lastname',
                'customerNo' => $shippingAddress->getId(),
                'email' => 'customer@null.com',
                'street' => 'street',
                'city' => 'city',
                'phone' => '11111111',
                'zip_code' => '1111',
                'language_iso3' => 'nl',
                'order_date' => $order->getCreatedAt(),
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
                ],
                'xid' => $order->getIncrementId()
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    protected function getProductImageUrl(\Magento\Catalog\Api\Data\ProductInterface $product): string
    {
        return $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
    }

    public static function loadOrder()
    {
        include __DIR__ . '/../../_files/order.php';
    }

    public static function loadOrderRollback()
    {
        include __DIR__ . '/../../_files/order_rollback.php';
    }

    public static function loadShipment()
    {
        include __DIR__ . '/../../_files/shipment.php';
    }
}
