<?php
namespace App\Observers;

use App\Models\Service;

class ServiceObserver
{
    public function created(Service $service)
    {
        $service->syncProfessionalsFromServiceList();
    }

    public function updated(Service $service)
    {
        $service->syncProfessionalsFromServiceList();
    }
}