<?php
/**
 * Rights management API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Groups;
use \Dandelion\Controllers\ApiController;

class GroupsAPI extends BaseModule
{
    /**
     * Gets the list of rights groups
     */
    public function getList()
    {
        $permissions = new Groups($this->repo);
        return $permissions->getGroupList();
    }

    /**
     * Gets the rights for a specific group
     */
    public function getGroup()
    {
        $permissions = new Groups($this->repo);
        $gid = $this->up->groupid;
        return $permissions->getGroupList($gid);
    }

    /**
     * Save rights for a group
     */
    public function edit()
    {
        if (!$this->ur->authorized('editgroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'groups'));
        }

        $permissions = new Groups($this->repo);
        $gid = $this->up->groupid;
        $rights = json_decode($this->up->rights, true);

        if ($permissions->editGroup($gid, $rights)) {
            return 'User group saved';
        } else {
            exit(ApiController::makeDAPI(5, 'Error saving user group', 'groups'));
        }
    }

    /**
     * Create new rights group
     */
    public function create()
    {
        if (!$this->ur->authorized('creategroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'groups'));
        }

        $permissions = new Groups($this->repo);
        $name = $this->up->name;
        $rights = $this->up->get('rights', []);

        if ($rights) {
            $rights = json_decode($rights, true);
        }

        if (is_numeric($permissions->createGroup($name, $rights))) {
            return 'User group created successfully';
        } else {
            exit(ApiController::makeDAPI(5, 'Error creating user group', 'groups'));
        }
    }

    /**
     * Delete rights group
     */
    public function delete()
    {
        if (!$this->ur->authorized('deletegroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'groups'));
        }

        $permissions = new Groups($this->repo);
        $gid = $this->up->groupid;
        $users = $permissions->usersExistInGroup($gid);

        if ($users) {
            exit(ApiController::makeDAPI(5, 'This group is assigned to users. Can not delete this group.', 'groups'));
        } else {
            $permissions->deleteGroup($gid);
            return 'Group deleted successfully.';
        }
    }

    /**
     * Gets the rights for the current user
     */
    public function getUserRights()
    {
        return $this->ur->getRightsForUser();
    }
}