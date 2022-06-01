<?php

namespace MegaBank\Payment\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use MegaBank\Config;
use MegaBank\Payment\Logger\Logger;
use MegaBank\Payment\Model\Config\Environment;

/**
 * Class ApiConfig
 * @package MegaBank\Payment\Helper
 */
class ApiConfig extends AbstractHelper
{
    /**
     * XML PATH API ENVIRONMENT
     */
    const XML_PATH_API_ENVIRONMENT = 'payment/megabank_payment/environment';

    /**
     * XML PATH API SANDBOX URL
     */
    const XML_PATH_API_SANDBOX_URL = 'payment/megabank_payment/api/sandbox_api_url';

    /**
     * XML PATH API URL
     */
    const XML_PATH_API_URL = 'payment/megabank_payment/api/api_url';

    /**
     * XML PATH WIDGET URL
     */
    const XML_PATH_WIDGET_URL = 'payment/megabank_payment/api/widget_url';

    /**
     * XML PATH PASSWORD
     */
    const XML_PATH_PASSWORD = 'payment/megabank_payment/api/password';

    /**
     * XML PATH IP
     */
    const XML_PATH_IP = 'payment/megabank_payment/api/ip';

    /**
     * XML PATH CERTIFICATE
     */
    const XML_PATH_CERTIFICATE = 'payment/megabank_payment/api/certificate';

    /**
     * XML PATH KEY
     */
    const XML_PATH_KEY = 'payment/megabank_payment/api/key';

    /**
     * @var Config
     */
    protected $apiConfig;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var resource[]
     */
    protected $tmpFiles = [];

    /**
     * ApiConfig constructor.
     * @param Logger $logger
     * @param Config $apiConfig
     * @param Context $context
     */
    public function __construct(
        Logger $logger,
        Config $apiConfig,
        Context $context
    ) {
        parent::__construct($context);
        $this->apiConfig = $apiConfig;
        $this->logger = $logger;
    }

    /**
     * @return Config
     */
    public function getApiConfig()
    {
        $config = $this->apiConfig;
        $config->setWidgetUrl($this->getWidgetUrl());
        $config->setUrl($this->getApiUrl());
        $config->setIp($this->getIp());
        $config->setKeyFile($this->getKeyFile());
        $config->setCertificateFile($this->getCertificateFile());
        $config->setLogger($this->logger);
        if ($password = $this->getPassword()) {
            $config->setPassword($password);
        }

        try {
            $config->validate();
        } catch (Exception $e) {
            $this->logger->critical(__('Invalid Mega Bank Config.') . $e->getMessage());
            throw new LocalizedException(__('Can\'t create api config object'));
        }

        return $config;
    }

    /**
     * @return string
     */
    public function getWidgetUrl()
    {
        return trim($this->getConfig(self::XML_PATH_WIDGET_URL));
    }

    /**
     * @param string $path
     * @param string $scope
     * @return mixed
     */
    protected function getConfig($path, $scope = ScopeInterface::SCOPE_WEBSITE)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        $environment = $this->getConfig(self::XML_PATH_API_ENVIRONMENT);
        if ($environment === Environment::ENVIRONMENT_SANDBOX) {
            return trim($this->getConfig(self::XML_PATH_API_SANDBOX_URL));
        }
        return trim($this->getConfig(self::XML_PATH_API_URL));
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return trim($this->getConfig(self::XML_PATH_IP));
    }

    /**
     * @return string
     */
    public function getKeyFile()
    {
        if ($key = $this->getKey()) {
            return $this->createTmpFile($key);
        }
        return false;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return trim($this->getConfig(self::XML_PATH_KEY));
    }

    /**
     * @return string
     */
    public function getCertificateFile()
    {
        if ($certificate = $this->getCertificate()) {
            return $this->createTmpFile($certificate);
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCertificate()
    {
        return trim($this->getConfig(self::XML_PATH_CERTIFICATE));
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return trim($this->getConfig(self::XML_PATH_PASSWORD));
    }

    /**
     * @param $content
     * @return false|mixed
     */
    protected function createTmpFile($content)
    {
        $file = tmpfile();
        if (!$file) {
            return false;
        }
        $this->tmpFiles[] = $file;
        fwrite($file, $content);
        return stream_get_meta_data($file)['uri'];
    }

    /**
     * Destroy Temporary Files
     */
    public function destroyTmpFiles()
    {
        foreach ($this->tmpFiles as $tmpFile) {
            if (is_resource($tmpFile)) {
                fclose($tmpFile);
            }
        }
    }
}
