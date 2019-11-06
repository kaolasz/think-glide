<?php
/**
 * +----------------------------------------------------------------------
 * | think-glide [thinkphp6]
 * +----------------------------------------------------------------------
 *  .--,       .--,             | FILE: Response.php
 * ( (  \.---./  ) )            | AUTHOR: byron
 *  '.__/o   o\__.'             | EMAIL: xiaobo.sun@qq.com
 *     {=  ^  =}                | QQ: 150093589
 *     /       \                | DATETIME: 2019/11/6 14:33
 *    //       \\               |
 *   //|   .   |\\              |
 *   "'\       /'"_.-~^`'-.     |
 *      \  _  /--'         `    |
 *    ___)( )(___               |-----------------------------------------
 *   (((__) (__)))              | 高山仰止,景行行止.虽不能至,心向往之。
 * +----------------------------------------------------------------------
 * | Copyright (c) 2019 http://www.zzstudio.net All rights reserved.
 * +----------------------------------------------------------------------
 */
declare(strict_types=1);

namespace think\glide;

use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureInterface;

class SignatureFactory implements SignatureInterface
{
    /**
     * Secret key used to generate signature.
     * @var string
     */
    protected $signKey;

    /**
     * Create Signature instance.
     * @param string $signKey Secret key used to generate signature.
     */
    public function __construct($signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Add an HTTP signature to manipulation parameters.
     * @param string $path The resource path.
     * @param array $params The manipulation parameters.
     * @return array  The updated manipulation parameters.
     */
    public function addSignature($path, array $params)
    {
        return array_merge($params, ['sign' => $this->generateSignature($path, $params)]);
    }

    /**
     * Validate a request signature.
     * @param string $path The resource path.
     * @param array $params The manipulation params.
     * @throws SignatureException
     */
    public function validateRequest($path, array $params)
    {
        if (!isset($params['sign'])) {
            throw new SignatureException('Signature is missing.');
        }

        if ($params['sign'] !== $this->generateSignature($path, $params)) {
            throw new SignatureException('Signature is not valid.');
        }
    }

    /**
     * Generate an HTTP signature.
     * @param string $path The resource path.
     * @param array $params The manipulation parameters.
     * @return string The generated HTTP signature.
     */
    public function generateSignature($path, array $params)
    {
        unset($params['sign']);
        ksort($params);

        return md5($this->signKey . ':' . ltrim($path, '/') . '?' . http_build_query($params));
    }

    /**
     * Create HttpSignature instance.
     * @param string $signKey Secret key used to generate signature.
     * @return SignatureFactory The HttpSignature instance.
     */
    public static function create($signKey)
    {
        return new self($signKey);
    }
}