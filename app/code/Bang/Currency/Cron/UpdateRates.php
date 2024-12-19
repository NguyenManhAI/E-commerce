<?php
namespace Bang\Currency\Cron;

use Magento\Directory\Model\Currency\Import\Factory as CurrencyImportFactory;
use Magento\Directory\Model\CurrencyFactory;

class UpdateRates
{
    protected $importFactory;
    protected $currencyFactory;

    public function __construct(
        CurrencyImportFactory $importFactory,
        CurrencyFactory $currencyFactory
    ) {
        $this->importFactory = $importFactory;
        $this->currencyFactory = $currencyFactory;
    }

    public function execute()
    {
        try {
            $importer = $this->importFactory->create('vietcombank');
            $rates = $importer->fetchRates();
            
            if ($rates) {
                $currency = $this->currencyFactory->create();
                foreach ($rates as $currencyCode => $rate) {
                    $currency->saveRates([
                        'VND' => [$currencyCode => $rate]
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error
        }
    }
}
