<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Translation\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\Deployed\Options;

class Locales implements OptionSourceInterface
{
    private const XML_PATH_LOCALE = 'general/locale/code';

    /**
     * Constuctor
     *
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Locale\Deployed\Options $localeOptions
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private StoreManagerInterface $storeManager,
        private ScopeConfigInterface $scopeConfig,
        private Options $localeOptions
    ) {
    }

    /**
     * Options for select element
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $locale = [];
        $stores = $this->storeManager->getStores($withDefault = false);
        foreach ($stores as $store) {
            $storeLang = $this->scopeConfig->getValue(
                'general/locale/code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getStoreId()
            );

            $locale[] = $storeLang;
        }

        $locale = array_unique($locale);
        sort($locale);

        return $this->filterLocales($locale);
    }

    /**
     * Get locales
     *
     * @param array $availableLocales
     * @return array
     */
    private function filterLocales(array $availableLocales = []): array
    {
        $locales = $this->localeOptions->getOptionLocales();

        return array_filter($locales, function ($localeData) use ($availableLocales) {
            return in_array($localeData['value'], $availableLocales);
        });
    }
}
