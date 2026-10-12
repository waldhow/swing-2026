<?php

namespace App\Controllers\Admin;

use App\Models\InquiryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Inquiries extends BaseAdminController
{
    public function index(): string
    {
        $model  = model(InquiryModel::class);
        $status = (string) $this->request->getGet('status');
        $type   = (string) $this->request->getGet('typ');

        if (array_key_exists($status, InquiryModel::STATUSES)) {
            $model->where('status', $status);
        }
        if (array_key_exists($type, InquiryModel::TYPES)) {
            $model->where('type', $type);
        }

        return $this->render('inquiries/index', [
            'title'     => 'Förfrågningar',
            'inquiries' => $model->orderBy('created_at', 'DESC')->paginate(30),
            'pager'     => $model->pager,
            'status'    => $status,
            'type'      => $type,
        ], 'forfragningar');
    }

    public function show(int $id): string
    {
        return $this->render('inquiries/show', [
            'title'   => 'Förfrågan',
            'inquiry' => $this->find($id),
        ], 'forfragningar');
    }

    public function status(int $id): RedirectResponse
    {
        $inquiry = $this->find($id);
        $status  = $inquiry['status'] === 'new' ? 'handled' : 'new';
        model(InquiryModel::class)->update($id, ['status' => $status]);

        return $this->back("forfragningar/{$id}", $status === 'handled' ? 'Markerad som hanterad.' : 'Markerad som ny.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->find($id);
        model(InquiryModel::class)->delete($id);

        return $this->back('forfragningar', 'Förfrågan är raderad.');
    }

    private function find(int $id): array
    {
        $inquiry = model(InquiryModel::class)->find($id);

        if ($inquiry === null) {
            throw PageNotFoundException::forPageNotFound('Förfrågan finns inte.');
        }

        return $inquiry;
    }
}
