<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors;

class OrderCreation extends \CreativeStyle\ParcellabIntegration\Model\RequestPreprocessors\Base
{
    public const REQUEST_METHOD = 'post';
    public const API_PATH_ORDER = 'order';
    public const API_PATH_TRACKING = 'track';
    public const ORDER_ARG = 'order';
    public const SHIPMENT_ARG = 'shipment';
    public const TRACKING_ARG = 'tracking';

    public const PAYLOAD_XID = 'xid';
    public const PAYLOAD_ORDER_NO = 'orderNo';
    public const PAYLOAD_RECIPIENT_NOTIFICATION = 'recipient_notification';
    public const PAYLOAD_RECIPIENT = 'recipient';
    public const PAYLOAD_CUSTOMER_NO = 'customerNo';
    public const PAYLOAD_EMAIL = 'email';
    public const PAYLOAD_STREET = 'street';
    public const PAYLOAD_CITY = 'city';
    public const PAYLOAD_PHONE = 'phone';
    public const PAYLOAD_ZIP_CODE = 'zip_code';
    public const PAYLOAD_LANGUAGE = 'language_iso3';
    public const PAYLOAD_ORDER_DATE = 'order_date';
    public const PAYLOAD_CLIENT = 'client';

    public const PAYLOAD_ARTICLES = 'articles';
    public const PAYLOAD_ARTICLE_NO = 'articleNo';
    public const PAYLOAD_ARTICLE_NAME = 'articleName';
    public const PAYLOAD_ARTICLE_CATEGORY = 'articleCategory';
    public const PAYLOAD_ARTICLE_IMAGE_URL = 'articleImageUrl';
    public const PAYLOAD_ARTICLE_QTY = 'quantity';
    public const PAYLOAD_ARTICLE_OPTIONS = 'options';

    public const PAYLOAD_TRACKING_COURIER = 'courier';
    public const PAYLOAD_TRACKING_NUMBER = 'tracking_number';

    public const PAYLOAD_CUSTOM_FIELDS = 'customFields';
    public const PAYLOAD_CUSTOM_FIELD_TEST_MODE = 'testShipment';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $localeResolver;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryListRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \CreativeStyle\ParcellabIntegration\Model\Product\OptionsFormatter
     */
    protected $optionsFormatter;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    protected $storeHelper;

    /**
     * @param \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\CategoryListInterface $categoryListRepository
     * @param \CreativeStyle\ParcellabIntegration\Model\Product\OptionsFormatter $optionsFormatter
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \CreativeStyle\ParcellabIntegration\Helper\Store $storeHelper
     */
    public function __construct(
        \CreativeStyle\ParcellabIntegration\Helper\Configuration $configuration,
        \Magento\Store\Api\Data\StoreInterface $store,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\CategoryListInterface $categoryListRepository,
        \CreativeStyle\ParcellabIntegration\Model\Product\OptionsFormatter $optionsFormatter,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \CreativeStyle\ParcellabIntegration\Helper\Store $storeHelper
    ) {
        parent::__construct($configuration, $store);
        $this->configuration = $configuration;
        $this->imageHelper = $imageHelper;
        $this->localeResolver = $localeResolver;
        $this->categoryListRepository = $categoryListRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->optionsFormatter = $optionsFormatter;
        $this->productRepository = $productRepository;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @param array $args
     * @return array
     * @throws \Magento\Framework\Exception\InvalidArgumentException
     */
    public function preparePayload(array $args = []): array
    {
        $entity = $this->getEntity($args);
        $trackingData = $args[self::TRACKING_ARG] ?? null;
        $shippingAddress = $entity->getShippingAddress();

        $items = $this->getEntityItems($args);
        $payload = [
            self::PAYLOAD_CLIENT => $this->getCountryCode((int) $entity->getStore()->getId()),
            self::PAYLOAD_RECIPIENT_NOTIFICATION => $shippingAddress->getPrefix(),
            self::PAYLOAD_RECIPIENT => $shippingAddress->getName(),
            self::PAYLOAD_CUSTOMER_NO => $shippingAddress->getId(),
            self::PAYLOAD_EMAIL => $shippingAddress->getEmail(),
            self::PAYLOAD_STREET => implode(', ', $shippingAddress->getStreet()),
            self::PAYLOAD_CITY => $shippingAddress->getCity(),
            self::PAYLOAD_PHONE => $shippingAddress->getTelephone(),
            self::PAYLOAD_ZIP_CODE => $shippingAddress->getPostCode(),
            self::PAYLOAD_LANGUAGE => $this->getLanguageCode((int) $entity->getStore()->getId()),
            self::PAYLOAD_ORDER_DATE => $entity->getCreatedAt(),
            self::PAYLOAD_ARTICLES => $this->getItemsPayload($items)
        ];

        if (isset($args[self::ORDER_ARG])) {
            $payload[self::PAYLOAD_XID] = $entity->getIncrementId();
            $payload[self::PAYLOAD_ORDER_NO] = $entity->getIncrementId();
        }

        if (isset($args[self::SHIPMENT_ARG])) {
            $payload[self::PAYLOAD_XID] = $entity->getOrder()->getIncrementId();
            $payload[self::PAYLOAD_ORDER_NO] = $entity->getOrder()->getIncrementId();
        }

        if ($trackingData) {
            $payload = array_merge($payload, $this->getTrackingPayload($trackingData));
        }

        $payload = array_merge($payload, $this->getTestModePayload());

        return [\GuzzleHttp\RequestOptions::JSON => array_filter($payload)];
    }

    /**
     * @param bool $track
     * @return string
     */
    public function getEndPointUrl(bool $track = false): string
    {
        $path = $track ? self::API_PATH_TRACKING : self::API_PATH_ORDER;

        return $this->configuration->getApiUrl() . $path;
    }

    /**
     * @param array $trackingData
     * @return array
     */
    public function getTrackingPayload(array $trackingData): array
    {
        return [
            self::PAYLOAD_TRACKING_COURIER => $this->getCourierName($trackingData),
            self::PAYLOAD_TRACKING_NUMBER => $trackingData['number'] ?? $trackingData['track_number']
        ];
    }

    /**
     * @param array $trackingData
     * @return string
     */
    public function getCourierName(array $trackingData): string
    {
        $courierCodes = $this->configuration->getCourierCodes();

        return $courierCodes[$trackingData['title']] ?? '';
    }

    /**
     * @return array
     */
    public function getTestModePayload(): array
    {
        $testModeFlag = (bool)$this->configuration->isTestModeEnabled();

        return [
            self::PAYLOAD_CUSTOM_FIELDS => [
                self::PAYLOAD_CUSTOM_FIELD_TEST_MODE => $testModeFlag
            ],
        ];
    }

    /**
     * @param array $items
     * @return array
     */
    protected function getItemsPayload(array $items)
    {
        $result = [];
        foreach ($items as $item) {
            try {
                $product = $this->productRepository->getById($item->getProductId());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $product = null;
            }
            $result[] = array_filter([
                self::PAYLOAD_ARTICLE_NO => $item->getSku(),
                self::PAYLOAD_ARTICLE_NAME => $item->getName(),
                self::PAYLOAD_ARTICLE_CATEGORY => $this->getProductCategoryNames($product),
                self::PAYLOAD_ARTICLE_IMAGE_URL => $this->getProductImageUrl($product),
                self::PAYLOAD_ARTICLE_QTY => $this->getArticleQty($item),
                self::PAYLOAD_ARTICLE_OPTIONS => $this->getArticleOptions($item)
            ]);
        }

        return $result;
    }

    /**
     * @param $product
     * @return string
     */
    protected function getProductImageUrl($product): string
    {
        if (!$product) {
            return '';
        }
        return $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
    }

    /**
     * @param int $storeId
     * @return string
     */
    protected function getCountryCode(int $storeId): string
    {
        $clientCountryCodes = $this->configuration->getClientCountryCodes();

        return $clientCountryCodes[$storeId] ?? '';
    }

    /**
     * @param int $storeId
     * @return string
     */
    protected function getLanguageCode(int $storeId): string
    {
        $clientLanguageCodes = $this->configuration->getClientLanguageCodes();

        return $clientLanguageCodes[$storeId] ?? '';
    }

    /**
     * @param $product
     * @return string
     */
    protected function getProductCategoryNames($product): string
    {
        if (!$product) {
            return '';
        }
        $ids = $product->getCategoryIds();
        if (!$ids) {
            return '';
        }
        $this->searchCriteriaBuilder->addFilter('entity_id', $ids, 'in');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $categories = $this->categoryListRepository->getList($searchCriteria);
        $categoryNames = array_map(function ($category) {
            return $category->getName();
        }, $categories->getItems());

        return implode(', ', $categoryNames);
    }

    protected function getEntity($args)
    {
        if (isset($args[self::SHIPMENT_ARG])) {
            return $args[self::SHIPMENT_ARG];
        }

        if (isset($args[self::ORDER_ARG])) {
            return $args[self::ORDER_ARG];
        }

        return null;
    }

    protected function getEntityItems(array $args): ?array
    {
        if (isset($args[self::SHIPMENT_ARG])) {
            return $args[self::SHIPMENT_ARG]->getAllItems();
        }

        if (isset($args[self::ORDER_ARG])) {
            return $args[self::ORDER_ARG]->getAllVisibleItems();
        }

        return [];
    }

    protected function getArticleOptions($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            return $this->optionsFormatter->format($item->getProductOptions());
        }

        return $this->optionsFormatter->format($item->getOrderItem()->getProductOptions());
    }

    protected function getArticleQty($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            return $item->getQtyOrdered();
        }

        return $item->getQty();
    }
}
