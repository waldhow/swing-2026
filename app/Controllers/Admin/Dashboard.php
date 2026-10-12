<?php

namespace App\Controllers\Admin;

use App\Models\CampaignModel;
use App\Models\CompetitionModel;
use App\Models\InquiryModel;
use App\Models\OrderModel;

class Dashboard extends BaseAdminController
{
    public function index(): string
    {
        $inquiries = model(InquiryModel::class);
        $orders    = model(OrderModel::class);

        // Gallring enligt integritetspolicyn sker här och vid nya inskick.
        $inquiries->purgeExpired(config('Inquiries')->retentionMonths);
        $orders->purgeExpired(config('Orders')->retentionDays);

        return $this->render('dashboard', [
            'title'          => 'Översikt',
            'newInquiries'   => $inquiries->where('status', 'new')->countAllResults(),
            'failedInquiry'  => $inquiries->where('mail_error IS NOT NULL', null, false)->where('status', 'new')->countAllResults(),
            'failedOrders'   => $orders->needsForward()->countAllResults(),
            'recentOrders'   => $orders->orderBy('created_at', 'DESC')->findAll(5),
            'recentInquiries' => $inquiries->orderBy('created_at', 'DESC')->findAll(5),
            'competitions'   => count(model(CompetitionModel::class)->current()),
            'campaigns'      => count(model(CampaignModel::class)->activeNow()),
            'ordersReady'    => config('Orders')->isOperational(),
            'orderChannel'   => config('Orders')->channel,
        ], 'oversikt');
    }
}
