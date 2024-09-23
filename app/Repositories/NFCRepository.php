<?php

namespace App\Repositories;

use App\Models\NfcAttendance;

class NFCRepository
{

    /**
     * @param array $select
     * @return mixed
     */
    public function getAll($identifier=''): mixed
    { if(!empty($identifier)){
        $nfcData = NfcAttendance::where('identifier',$identifier)->first();
    }else{
        $nfcData = NfcAttendance::get();
    }
        return $nfcData;

    }

    /**
     * @param $validatedData
     * @return mixed
     */
    public function store($validatedData):mixed
    {
        return NfcAttendance::create($validatedData)->fresh();
    }


    /**
     * @param $id
     * @return mixed
     */
    public function findNFCDetailById($id):mixed
    {
        return NfcAttendance::find($id);
    }


    public function delete($nfcDetail): ?bool
    {
        return $nfcDetail->delete();
    }

}
