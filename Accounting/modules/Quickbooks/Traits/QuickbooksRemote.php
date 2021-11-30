<?php

namespace Modules\Quickbooks\Traits;

use QuickBooksOnline\API\DataService\DataService;

trait QuickbooksRemote
{
    protected $pageSize = 1000;

    private function getCustomers()
    {
        $contacts = [];

        $count_tax_rates = $this->getCount("Customer");
        for ($count = 0; $count < $count_tax_rates; $count += $this->pageSize) {
            $allTaxRates = $this->getData("Customer", $count);
            if (!empty($allTaxRates)) {
                foreach ($allTaxRates as $taxRate) {
                    $contacts[] = $taxRate;
                }
            }
        }

        return $contacts;
    }

    public function getCustomersCount()
    {
        return $this->getCount("Customer");
    }

    private function getVendors()
    {
        $vendors = [];

        $count_vendors = $this->getCount("Vendor");
        for ($count = 0; $count < $count_vendors; $count += $this->pageSize) {
            $allVendors = $this->getData("Vendor", $count);
            if (!empty($allVendors)) {
                foreach ($allVendors as $vendor) {
                    $vendors[] = $vendor;
                }
            }
        }

        return $vendors;
    }

    public function getVendorsCount()
    {
        return $this->getCount("Vendor");
    }

    public function getSalesTaxes()
    {
        $salesTaxes = [];

        $count_tax_rates = $this->getCount("TaxRate");
        for ($count = 0; $count < $count_tax_rates; $count += $this->pageSize) {
            $allTaxRates = $this->getData("TaxRate", $count);
            if (!empty($allTaxRates)) {
                foreach ($allTaxRates as $taxRate) {
                    $salesTaxes[] = $taxRate;
                }
            }
        }

        return $salesTaxes;
    }

    public function getSalesTaxesCount()
    {
        return $this->getCount("TaxRate");
    }

    public function getProducts()
    {
        $products = [];

        $count_products = $this->getCount("Item");
        for ($count = 0; $count < $count_products; $count += $this->pageSize) {
            $allProducts = $this->getData("Item", $count);
            if (!empty($allProducts)) {
                foreach ($allProducts as $product) {
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    public function getProductsCount()
    {
        return $this->getCount("Item");
    }

    public function getInvoices()
    {
        $invoices = [];

        $count_invoices = $this->getCount("Invoice");
        for ($count = 0; $count < $count_invoices; $count += $this->pageSize) {
            $allInvoices = $this->getData("Invoice", $count);
            if (!empty($allInvoices)) {
                foreach ($allInvoices as $invoice) {
                    $invoices[] = $invoice;
                }
            }
        }

        return $invoices;
    }

    public function getInvoicesCount()
    {
        return $this->getCount("Invoice");
    }

    public function getBills()
    {
        $invoices = [];

        $count_invoices = $this->getCount("Bill");
        for ($count = 0; $count < $count_invoices; $count += $this->pageSize) {
            $allInvoices = $this->getData("Bill", $count);
            if (!empty($allInvoices)) {
                foreach ($allInvoices as $invoice) {
                    $invoices[] = $invoice;
                }
            }
        }

        return $invoices;
    }

    public function getBillsCount()
    {
        return $this->getCount("Bill");
    }

    public function getAccounts()
    {
        $accounts = [];

        $count_accounts = $this->getCount("Account");
        for ($count = 0; $count < $count_accounts; $count += $this->pageSize) {
            $allAccounts = $this->getData("Account", $count);
            if (!empty($allAccounts)) {
                foreach ($allAccounts as $account) {
                    $accounts[] = $account;
                }
            }
        }

        return $accounts;
    }

    public function getAccountsCount()
    {
        return $this->getCount("Account");
    }


    protected function getCount(string $object)
    {
        return $this->getClient()->Query("select count(*) from " . $object);
    }

    protected function getData(string $object, int $startPosition = 0)
    {
        return $this->getClient()->Query("select * from " . $object . " startPosition " . $startPosition . " maxResults " . $this->pageSize);
    }

    protected function getOne(string $object, $id)
    {
        $entities = $this->getClient()->Query("select * from " . $object . " where Id = '" . $id."'");
        return reset($entities);
    }

    protected function getClient()
    {
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => setting('quickbooks.client_id'),
            'ClientSecret' => setting('quickbooks.client_secret'),
            'accessTokenKey' => setting('quickbooks.token'),
            'refreshTokenKey' => setting('quickbooks.refresh_token'),
            'QBORealmID' => setting('quickbooks.realm_id'),
            'baseUrl' => setting('quickbooks.environment')
        ));
//        $dataService->setLogLocation(storage_path('logs'));


        return $dataService;
    }
}
