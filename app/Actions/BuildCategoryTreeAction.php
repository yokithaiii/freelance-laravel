<?php

namespace App\Actions;

class BuildCategoryTreeAction
{

    public function buildTree(array $categories, $parentId = null): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }

}