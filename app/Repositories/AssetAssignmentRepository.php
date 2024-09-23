<?php

namespace App\Repositories;

use App\Models\AssetAssignment;

class AssetAssignmentRepository
{
    public function getAllAssetAssignments($select, $with)
    {
        return AssetAssignment::select($select)->with($with)->get()->map(function ($item) {
            $item->assign_to = $item->users->name;
            $item->asset_name = $item->assets->name;
            $item->assign_date = date('d-m-Y',strtotime($item->assign_date));
            return $item;
        });
    }
}
