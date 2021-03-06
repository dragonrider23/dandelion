<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

use Dandelion\Repos\Interfaces\KeyManagerRepo;

class KeyManager
{
    private $repo;

    public function __construct(KeyManagerRepo $repo)
    {
        $this->repo = $repo;
        return;
    }

    public function getKey($uid, $force = false)
    {
        if (!$force) {
            $key = $this->repo->getKeyForUser($uid);

            if ($key) {
                return $key['keystring'];
            }
        }

        // Clear database of old keys for user
        $this->revoke($uid);

        // Generate new key
        $newKey = $this->generateKey(15);
        if (!$newKey) {
            return 'Error generating key.';
        }

        // Insert new key
        $success = $this->repo->saveKeyForUser($uid, $newKey);

        if ($success) {
            return $newKey;
        } else {
            return 'Error generating key.';
        }
    }

    public function revoke($uid)
    {
        return $this->repo->revoke($uid);
    }

    /**
     * Generate a random alphanumeric string
     *
     * @param int $length - Length of generated string
     *
     * @return string
     */
    private function generateKey($length = 10)
    {
        $bin = openssl_random_pseudo_bytes($length, $cstrong);
        if (!$cstrong || !$bin) {
            return '';
        }
        return bin2hex($bin);
    }
}
