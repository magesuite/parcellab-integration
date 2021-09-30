<?php
declare(strict_types=1);

namespace CreativeStyle\ParcellabIntegration\Model\Product;

class OptionsFormatter
{
    public function format($options)
    {
        $result = [];
        if (!$options) {
            return $result;
        }
        if (isset($options['options'])) {
            $result[] = array_map([$this, 'concat'], $options['options']);
        }
        if (isset($options['additional_options'])) {
            $result[] = array_map([$this, 'concat'], $options['additional_options']);
        }
        if (isset($options['attributes_info'])) {
            $result[] = array_map([$this, 'concat'], $options['attributes_info']);
        }
        if (isset($options['bundle_options'])) {
            $result[] = $this->concatBundleProductOptions($options['bundle_options']);
        }
        return $result;
    }

    protected function concat(array $item): string
    {
        if (key_exists('print_value', $item)) {
            $valueKey = 'print_value';
        } else {
            $valueKey = 'value';
        }
        return $item['label'] . ': ' . $item[$valueKey];
    }

    protected function concatBundleProductOptions(array $options): array
    {
        $result = [];
        foreach ($options as $option) {
            foreach ($option['value'] as $details) {
                $result[] = $details['qty'] . ' x ' . $details['title'] . ' - ' . $details['price'];
            }
        }
        return $result;
    }
}
