<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Center;
use App\Models\District;
use App\Models\GramPanchyat;
use App\Models\Irrigation;
use App\Models\State;
use App\Models\Village;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country_id)->get();
        if (!$states->isEmpty()) {
            return response()->json([
                'data' => $states,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }
    public function getDistricts(Request $request)
    {
        $districts = District::where('state_id', $request->state_id)->get();
        if (!$districts->isEmpty()) {
            return response()->json([
                'data' => $districts,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }
    public function getBlocks(Request $request)
    {
        $blocks = Block::where('district_id', $request->district_id)->get();
        if (!$blocks->isEmpty()) {
            return response()->json([
                'data' => $blocks,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }
    public function getGramPanchyats(Request $request)
    {
        $gram_panchyats = GramPanchyat::where('block_id', $request->block_id)->get();
        if (!$gram_panchyats->isEmpty()) {
            return response()->json([
                'data' => $gram_panchyats,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }
    public function getVillages(Request $request)
    {
        $villages = Village::where('gram_panchyat_id', $request->gram_panchyat_id)->get();
        if (!$villages->isEmpty()) {
            return response()->json([
                'data' => $villages,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }
    public function get_center_and_zones(Request $request)
    {
        $villages = Village::where('id', $request->village_id)
            ->with(['zone', 'center'])
            ->get()->map(function ($item) {
                $item->zone_name = $item->zone->name;
                $item->center_name = $item->center->name;
                unset($item->zone, $item->center);
                return $item;
            });
        if (!$villages->isEmpty()) {
            return response()->json([
                'data' => $villages,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }
    public function getCenters(Request $request)
    {
        $centers = Center::where('zone_id', $request->zone_id)->get();
        if (!$centers->isEmpty()) {
            return response()->json([
                'data' => $centers,
            ]);
        } else {
            return response()->json([
                'message' => "No data found",
            ]);
        }
    }

    public function get_irrigations(Request $request)
    {
        $irregation = Irrigation::where('category', $request->irregation_mode)->get();

        return response()->json($irregation);
    }
}
