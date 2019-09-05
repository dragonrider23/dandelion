<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Repos;

use Dandelion\Repos\Interfaces;

class CheestoRepo extends BaseRepo implements Interfaces\CheestoRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'cheesto';
    }

    private function fixCheestoFieldTypes(&$record)
    {
        $record['id'] = (int) $record['id'];
        $record['user_id'] = (int) $record['user_id'];
        $record['disabled'] = (bool) $record['user_id'];
    }

    public function getAllStatuses()
    {
        $statuses = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->whereEqual($this->prefix.'user.disabled', 0)
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');

        foreach ($statuses as &$status) {
            $this->fixCheestoFieldTypes($status);
        }

        return $statuses;
    }

    public function getUserStatus($uid)
    {
        $status = $this->database
            ->find($this->table)
            ->belongsTo($this->prefix.'user', 'user_id')
            ->whereEqual($this->prefix.'user.id', $uid)
            ->whereEqual($this->prefix.'user.disabled', 0)
            ->read($this->table.'.*, '.$this->prefix.'user.fullname');
        $this->fixCheestoFieldTypes($status);
        return $status;
    }

    public function updateStatus($uid, $status, $message, $return, $date)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('user_id', $uid)
            ->update([
                'message' => $message,
                'status' => $status,
                'returntime' => $return,
                'modified' => $date
            ]);
    }

    public function createCheesto($uid, $date)
    {
        return $this->database->createItem($this->table, [
            'status' => 'Available',
            'message' => '',
            'returntime' => '00:00:00',
            'modified' => $date,
            'user_id' => $uid
        ]);
    }
}
