<?php

namespace App\Repositories;


use App\Models\QrAttendance;

class QRCodeRepository
{

    /**
     * @param array $select
     * @return mixed
     */
    public function getAll($identifier=''): mixed
    {

        if(!empty($identifier)){
            $qrData = QrAttendance::where('identifier',$identifier)->first();
        }else{
            $qrData = QrAttendance::get();
        }
        return $qrData;
    }

    /**
     * @param $validatedData
     * @return mixed
     */
    public function store($validatedData):mixed
    {
        return QrAttendance::create($validatedData)->fresh();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findQr($id):mixed
    {
        return QrAttendance::find($id);
    }

    public function update($qrDetail, $validatedData)
    {
        return $qrDetail->update($validatedData);
    }

    public function delete($qrDetail): ?bool
    {
        return $qrDetail->delete();
    }

}
