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

use Dandelion\Repos\Interfaces\CategoriesRepo;

class Categories
{
    private $repo;

    public function __construct(CategoriesRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get the children of a parent category and return JSON of list at each level
     *
     * @param array $cids - Category IDs of selected starting with 0 at index 0 as root
     *
     * @return string - HTML of category select group
     */
    public function renderChildrenJson($cids)
    {
        $cats = $this->repo->getAllCategories();

        $response = [
            'currentList' => $cids,
            'levels' => []
        ];

        // Cycle through each level
        foreach ($cids as $i => $cid) {
            $alphaList = array();
            // Find children
            foreach ($cats as $isChild) {
                if($isChild['parent'] == $cid) {
                    $child = array(
                        'id' =>  $isChild['id'],
                        'description' => $isChild['description']
                    );
                    array_push($alphaList, $child);
                }
            }

            // Sort children alphabetically
            usort($alphaList, function($a, $b) {
                return strcasecmp(mb_strtolower($a['description']), mb_strtolower($b['description']));
            });

            // Add children to array for the level
            foreach ($alphaList as $children) {
                $selected = (isset($cids[$i+1]) && ($children['id'] == $cids[$i+1]));

                $response['levels'][$i][] = [
                    'id' => (int) $children['id'],
                    'desc' => $children['description'],
                    'selected' => $selected
                ];
            }
        }

        return json_encode($response);
    }

    public function renderFromString($catstring, $json = true)
    {
        $catStrExploded = explode(':', $catstring);
        $idArr = [0];
        $pid = 0;

        for ($i = 0; $i < count($catStrExploded); $i++) {
            $pid = (int) $this->repo->getIdForCategoryWithParent($catStrExploded[$i], $pid);
            if ($pid) {
                array_push($idArr, $pid);
            }
        }

        $mainJson = json_decode($this->renderChildrenJson($idArr), true);
        if ((count($catStrExploded) + 1) > count($idArr)) {
            $mainJson['errorcode'] = 1;
        } else {
            $mainJson['errorcode'] = 0;
        }
        $mainJson['string'] = $catstring;

        return $json ? json_encode($mainJson) : $mainJson;
    }

    /**
     * Create a new category
     *
     * @param int $parent Parent ID (0 if root)
     * @param string $description Name of category
     *
     * @return bool - success
     */
    public function addCategory($parent, $description)
    {
        $description = $this->normalizeCategoryDesc($description);
        return $this->repo->addCategory($description, $parent);
    }

    /**
     * Remove category from database
     *
     * @param int $cid ID of category to be deleted
     *
     * @return bool - success
     */
    public function delCategory($cid)
    {
        // Get the category's current parent to reassign children
        $newParent = $this->repo->getCategoryParent($cid);

        // Delete category from DB
        $deleted = $this->repo->deleteCategory($cid);

        // Reassign children
        $this->repo->adoptChildren($newParent, $cid);

        return $deleted;
    }

    /**
     * Update category description
     *
     * @param int $cid ID of category to update
     * @param string $desc Name of category
     *
     * @return bool - success
     */
    public function editCategory($cid, $desc)
    {
        $desc = $this->normalizeCategoryDesc($desc);
        return is_numeric($this->repo->updateCategory($desc, $cid));
    }

    /**
     * Normalize category descriptions
     *
     * @param string $desc description to normalize
     *
     * @return string
     */
    private function normalizeCategoryDesc($desc)
    {
        // Colons are used to separate categories, so remove them
        return str_replace(':', '_', $desc);
    }
}
