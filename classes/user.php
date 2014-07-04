<?php

/**
 * Handle user management functions
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Feb 2014
 */
namespace Dandelion\Users;

use Dandelion\Database\dbManage;
use Dandelion\Permissions;

class User extends dbManage
{

    /**
     * Update user information
     *
     * @param array $userInfoArray - User information in a associative array
     *        realname, sid, role, first, uid, theme
     *       
     * @return string - Success message
     */
    public function editUser($userInfoArray = null) {
        if (!empty($userInfoArray['realname']) && !empty($userInfoArray['theme']) && !empty($userInfoArray['role']) && is_numeric($userInfoArray['first']) && !empty($userInfoArray['uid'])) {
            $stmt = 'UPDATE `' . DB_PREFIX . 'users` SET `realname` = :realname, `role` = :role, `firsttime` = :first, `theme` = :theme WHERE `userid` = :userid';
            $params = array (
                'realname' => $userInfoArray['realname'],
                'role' => $userInfoArray['role'],
                'first' => $userInfoArray['first'],
                'userid' => $userInfoArray['uid'],
                'theme' => $userInfoArray['theme'] 
            );
            
            $this->queryDB($stmt, $params);
            
            $stmt = 'UPDATE `' . DB_PREFIX . 'presence` SET `realname` = :realname WHERE `uid` = :userid';
            $params = array (
                'realname' => $userInfoArray['realname'],
                'userid' => $userInfoArray['uid'] 
            );
            
            $this->queryDB($stmt, $params);
            
            return 'User Updated<br><br>';
        }
        else {
            return 'Error 0x1c2u3e';
        }
    }

    /**
     * Create a new user
     *
     * @param array $userInfoArray - User information in a associative array
     *        username, password, realname, sid, role
     *       
     * @return string - Success message
     */
    public function addUser($userInfoArray = null) {
        if (!empty($userInfoArray['username']) && !empty($userInfoArray['password']) && !empty($userInfoArray['realname']) && !empty($userInfoArray['role'])) {
            $stmt = 'SELECT * FROM `' . DB_PREFIX . 'users` WHERE `username` = :username';
            $params = array (
                'username' => $userInfoArray['username'] 
            );
            $row = $this->queryDB($stmt, $params);
            
            if ($row == NULL) {
                $date = new \DateTime();
                $add_user = $userInfoArray['username'];
                $add_pass = password_hash($userInfoArray['password'], PASSWORD_BCRYPT);
                $add_real = $userInfoArray['realname'];
                $add_role = $userInfoArray['role'];
                
                $stmt = 'INSERT INTO `' . DB_PREFIX . 'users` (username, password, realname, role, datecreated, theme) VALUES (:username, :password, :realname, :role, :datecreated, \'\')';
                $params = array (
                    'username' => $add_user,
                    'password' => $add_pass,
                    'realname' => $add_real,
                    'role' => $add_role,
                    'datecreated' => $date->format('Y-m-d') 
                );
                $this->queryDB($stmt, $params);
                
                if ($add_role != 'guest') {
                    $lastID = $this->lastInsertId();
                    
                    $stmt = 'INSERT INTO `' . DB_PREFIX . 'presence` (`uid`, `realname`, `status`, `message`, `returntime`, `dmodified`) VALUES (:uid, :real, 1, \'\', \'00:00:00\', :date)';
                    $params = array (
                        'uid' => $lastID,
                        'real' => $add_real,
                        'date' => $date->format('Y-m-d H:i:s') 
                    );
                    
                    $this->queryDB($stmt, $params);
                }
                
                return 'User Added<br><br>';
            }
            else {
                return 'Username already exists!';
            }
        }
        else {
            return 'Error 0x1c2u3a';
        }
    }

    /**
     * Reset user password
     *
     * @param string $pass - New password
     * @param int $uid - User's id number
     *       
     * @return string - Success message
     */
    public function resetUserPw($uid = null, $pass = null) {
        if (!empty($uid) && !empty($pass)) {
            if (is_numeric($uid)) {
                $pass = password_hash($pass, PASSWORD_BCRYPT);
                
                $stmt = 'UPDATE `' . DB_PREFIX . 'users` SET `password` = :newpass WHERE `userid` = :myID';
                $params = array (
                    'newpass' => $pass,
                    'myID' => $uid 
                );
                $this->queryDB($stmt, $params);
                
                return 'Password change successful.<br><br>';
            }
            else {
                return 'Error resetting password.<br><br>';
            }
        }
        else {
            return 'Error 0x1c2u3r';
        }
    }

    /**
     * Delete user
     *
     * @param int $uid - User's id number
     *       
     * @return string - Success message
     */
    public function deleteUser($uid = null) {
        // To ensure at least one admin account is available,
        // some checks are performed to verify rights of accounts
        if (!empty($uid) && $uid != $_SESSION['userInfo']['userid']) {
            $delete = false;
            
            $stmt = 'SELECT `role` FROM `' . DB_PREFIX . 'users` WHERE `userid` = :userid';
            $params = array (
                'userid' => $uid 
            );
            $user = $this->queryDB($stmt, $params)[0]['role'];
            
            $perms = new Permissions();
            $isAdmin = (array) $perms->loadRights($user);
            
            if (!$isAdmin['admin']) {
                // If the account being deleted isn't an admin, then there's nothing to worry about
                $delete = true;
            }
            else {
                // If the account IS and admin, check all other users to make sure
                // there's at least one other user with the admin rights flag
                $stmt = 'SELECT `role` FROM `' . DB_PREFIX . 'users` WHERE `userid` != :userid';
                $params = array (
                    'userid' => $uid 
                );
                $otherUsers = $this->queryDB($stmt, $params);
                
                foreach ($otherUsers as $areTheyAdmin) {
                    $isAdmin = (array) $perms->loadRights($areTheyAdmin['role']);
                    
                    if ($isAdmin['admin']) {
                        // If one is found, stop for loop and allow the delete
                        $delete = true;
                        break;
                    }
                }
            }
            
            if ($delete) {
                $stmt = 'DELETE FROM `' . DB_PREFIX . 'users` WHERE `userid` = :userid';
                $stmt2 = 'DELETE FROM `' . DB_PREFIX . 'presence` WHERE `uid` = :userid';
                $params = array (
                    'userid' => $uid 
                );
                
                $this->queryDB($stmt, $params);
                $this->queryDB($stmt2, $params);
                
                return "Action Taken: User Deleted<br><br>";
            }
            else {
                return '<br>There must be at least one account with the \'admin\' rights flag.<br>';
            }
        }
        else {
            return 'Error 0x1c2u3d';
        }
    }

    /**
     * Update user status
     *
     * @param int $uid - User's id number
     * @param int $status_id - # for user status type
     * @param string $message - User's away message
     * @param string $returntime - Return time for away user
     *       
     * @return string - Success message
     */
    public function updateUserStatus($uid = null, $status_id = null, $message = null, $returntime = null) {
        if (!empty($uid) && !empty($status_id)) {
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');
            
            switch ($status_id) {
                case "Available":
                    $status_id = 1;
                    $returntime = '00:00:00';
                    $message = '';
                    break;
                case "Away From Desk":
                    $status_id = 2;
                    break;
                case "At Lunch":
                    $status_id = 3;
                    break;
                case "Out for Day":
                    $status_id = 4;
                    break;
                case "Out":
                    $status_id = 5;
                    break;
                case "Appointment":
                    $status_id = 6;
                    break;
                case "Do Not Disturb":
                    $status_id = 7;
                    break;
                case "Meeting":
                    $status_id = 8;
                    break;
                case "Out Sick":
                    $status_id = 9;
                    break;
                case "Vacation":
                    $status_id = 10;
                    break;
                default:
                    $status_id = 1;
                    $returntime = "00:00:00";
                    break;
            }
            
            $stmt = 'UPDATE `' . DB_PREFIX . 'presence` SET `message` = :message, `status` = :status, `returntime` = :return, `dmodified` = :date WHERE `uid` = :userid';
            $params = array (
                'message' => $message,
                'status' => $status_id,
                'return' => $returntime,
                'date' => $date,
                'userid' => $uid 
            );
            $this->queryDB($stmt, $params);
            
            return 'User Status Updated<br><br>';
        }
        else {
            return 'Error 0x1c2u3c';
        }
    }

    /**
     * Revoke API keys for user
     *
     * @param int $id User ID number
     * @return string - Message
     */
    public function revokeAPIKey($id) {
        $sql = 'DELETE FROM ' . DB_PREFIX . 'apikeys
                WHERE user = :id';
        $params = array (
            "id" => $id 
        );
        
        if ($this->queryDB($sql, $params)) {
            return 'API Key has been revoked<br><br>';
        }
        else {
            return 'Error 0x1c2u3r';
        }
    }
}