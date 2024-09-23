<?php

namespace App\Services\Qr;

use App\Repositories\QRCodeRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class QrCodeService
{

    public function __construct(public QRCodeRepository $QRCodeRepository)
    {}

    public function getAllQr()
    {
        return $this->QRCodeRepository->getAll();
    }

    public function verifyQr($identifier)
    {
        return $this->QRCodeRepository->getAll($identifier);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function findQrDetailById($id): mixed
    {
        $qrDetail =  $this->QRCodeRepository->findQr($id);
        if(!$qrDetail){
            throw new \Exception('Qr detail not found',400);
        }
        return $qrDetail;
    }

    /**
     * @throws \Exception
     */
    public function saveQrDetail($validatedData)
    {
        try {

            $validatedData['identifier'] = base64_encode(random_bytes(20));

            DB::beginTransaction();
                $qrDetail = $this->QRCodeRepository->store($validatedData);
            DB::commit();
            return $qrDetail;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function updateQrDetail($validatedData, $id): bool
    {
        try {
            $qrDetail = $this->findQrDetailById($id);
            DB::beginTransaction();
                $updateStatus = $this->QRCodeRepository->update($qrDetail, $validatedData);
            DB::commit();
            return $updateStatus;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * @throws \Exception
     */
    public function deleteQrDetail($id): bool
    {
        try {
            $qrDetail = $this->findQrDetailById($id);
            DB::beginTransaction();
                $this->QRCodeRepository->delete($qrDetail);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


}

