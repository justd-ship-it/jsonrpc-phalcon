<?php

use Phalcon\Mvc\Controller;
use Phalcon\Validation\Validator\Url;

/**
 * Class ShortLinkController
 * @package ShortUrl\Controller
 */
class ShortLinkController extends Controller
{
    /**
     * @return mixed
     * @throws \ShortUrl\JsonRpc\Exception\InvalidParamsException
     */
    public function createAction()
    {
        $params = $this->dispatcher->getParams();

        $validation = new Phalcon\Validation();
        $validation->add('link', new Url(['message' => 'Link not valid', 'allowEmpty' => false]));
        $messages = $validation->validate($params);

        if (count($messages)) {
            $errs = [];
            foreach ($messages as $message) {
                $errs[] = $message;
            }
            throw new \ShortUrl\JsonRpc\Exception\InvalidParamsException(implode(',', $errs));
        }

        /** @var \ShortUrl\Lib\HashId $hash */
        $hash = $this->getDI()->get(\ShortUrl\Lib\HashId::class);

        $randomKey = $hash->getRandomKey();

        $db = $this->getDI()->get('db');

        $db->insert('short_link',
            [$params['link'], $randomKey],
            ['link', 'key']
        );

        if ($id = $db->lastInsertId()) {
            $hashCode = $hash->createHash($id, $randomKey);
            return $hashCode['hash'];
        }
    }

}
