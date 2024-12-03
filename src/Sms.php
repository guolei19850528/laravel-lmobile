<?php
/**
 * 作者:郭磊
 * 邮箱:174000902@qq.com
 * 电话:15210720528
 * Git:https://github.com/guolei19850528/laravel-lmobile
 */

namespace Guolei19850528\Laravel\Lmobile;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Random\RandomException;


/**
 * 微网通联SMS Api Class
 * @see https://www.lmobile.cn/ApiPages/index.html
 */
class Sms
{
    /**
     * @var string
     */
    protected string $baseUrl = '';

    /**
     * @var string
     */
    protected string $accountId = '';

    /**
     * @var string
     */
    protected string $password = '';

    /**
     * @var string|int
     */
    protected string|int $productId = '';

    /**
     * @var string
     */
    protected string $smmsEncryptKey = '';

    public function getBaseUrl(): string
    {
        if (\str($this->baseUrl)->endsWith('/')) {
            return \str($this->baseUrl)->substr(0, -1)->toString();
        }
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): Sms
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): Sms
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): Sms
    {
        $this->password = $password;
        return $this;
    }


    public function getProductId(): int|string
    {
        return $this->productId;
    }

    public function setProductId(int|string $productId): Sms
    {
        $this->productId = $productId;
        return $this;
    }

    public function getSmmsEncryptKey(): string
    {
        return $this->smmsEncryptKey;
    }

    public function setSmmsEncryptKey(string $smmsEncryptKey): Sms
    {
        $this->smmsEncryptKey = $smmsEncryptKey;
        return $this;
    }


    public function __construct(
        string|int $productId = '',
        string     $accountId = '',
        string     $password = '',
        string     $smmsEncryptKey = 'SMmsEncryptKey',
        string     $baseUrl = 'https://api.51welink.com/'
    )
    {
        $this->setProductId($productId);
        $this->setAccountId($accountId);
        $this->setPassword($password);
        $this->setSmmsEncryptKey($smmsEncryptKey);
        $this->setBaseUrl($baseUrl);
    }

    public function signature(array|Collection $data = []): string
    {
        $signatureDataStr = http_build_query([
            'AccountId' => \data_get($data, 'AccountId', ''),
            'PhoneNos' => \data_get($data, 'PhoneNos', ''),
            'Password' => \str(md5($this->getPassword() . $this->getSmmsEncryptKey()))->upper()->toString(),
            'Random' => \data_get($data, 'Random', ''),
            'Timestamp' => \data_get($data, 'Timestamp', ''),
        ]);
        return \hash('sha256', $signatureDataStr);
    }

    /**
     * @see https://www.lmobile.cn/ApiPages/index.html
     * @param string $phoneNos
     * @param string $content
     * @param array|Collection|null $options
     * @param \Closure|null $closure
     * @param string $url
     * @return bool
     * @throws RandomException
     */
    public function sendSms(
        string                $phoneNos = '',
        string                $content = '',
        array|Collection|null $options = [],
        \Closure              $closure = null,
        string                $url = '/EncryptionSubmit/SendSms.ashx'
    ): bool
    {
        $data = [
            'AccountId' => $this->getAccountId(),
            'AccessKey' => '',
            'PhoneNos' => $phoneNos,
            'Timestamp' => \now()->timestamp,
            'Random' => \random_int(1, PHP_INT_MAX),
            'ProductId' => $this->getProductId(),
            'Content' => $content,
        ];
        \data_set($data, 'AccessKey', $this->signature(\collect($data)->toArray()));
        $response = Http::baseUrl($this->getBaseUrl())
            ->asJson()
            ->withOptions(\collect($options)->toArray())
            ->post($url, \collect($data)->toArray());
        if ($closure) {
            return call_user_func($closure, $response);
        }
        if ($response->ok()) {
            $json = $response->json();
            if (\str(\data_get($json, 'Result', ''))->lower()->toString() === 'succ') {
                return true;
            }
        }
        return false;
    }
}
