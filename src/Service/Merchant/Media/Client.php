<?php

namespace MuCTS\Laravel\WeChatPayV3\Service\Merchant\Media;

use MuCTS\Laravel\WeChatPayV3\Kernel\BaseClient;

/**
 * Class Client.
 */
class Client extends BaseClient
{
    public static function classUrl()
    {
        return "/v3/merchant/media/upload";
    }

    /**
     * @param $fileName
     * @param $file_path
     * @param $mimeType
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Throwable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($fileName, $file_path, $mimeType, array $options = [])
    {
        $signPayload = json_encode([
            'filename' => $fileName,
            'sha256' => hash_file('sha256', $file_path),
        ]);

        $multipart = [
            [
                'name' => 'meta',
                'contents' => $signPayload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
            [
                'name' => 'file',
                'filename' => $fileName,
                'contents' => fopen($file_path, 'r'),
                'headers' => [
                    'Content-Type' => $mimeType,
                ],
            ],
        ];

        $url = self::classUrl().'upload';
        $opts = $options + ['multipart' => $multipart, 'sign_payload' => $signPayload];

        return $this->request('POST', $url, $opts);
    }

}
