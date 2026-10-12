<?php

namespace App\Controllers\Admin;

use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

/**
 * Lokala orderkopior. swing-order är huvudregistret — här syns om
 * överlämningen lyckades och den kan göras om.
 */
class Orders extends BaseAdminController
{
    public function index(): string
    {
        $model  = model(OrderModel::class);
        $filter = (string) $this->request->getGet('visa');

        if ($filter === 'fel') {
            $model->needsForward();
        }

        return $this->render('orders/index', [
            'title'  => 'Beställningar',
            'orders' => $model->orderBy('created_at', 'DESC')->paginate(30),
            'pager'  => $model->pager,
            'filter' => $filter,
        ], 'bestallningar');
    }

    public function show(int $id): string
    {
        $order = $this->find($id);

        return $this->render('orders/show', [
            'title'          => 'Beställning ' . $order['reference'],
            'order'          => $order,
            'hasPersonalNum' => ! empty($order['personal_number_enc']),
        ], 'bestallningar');
    }

    public function resend(int $id): RedirectResponse
    {
        $order = $this->find($id);

        if (OrderModel::awaitsPayment($order)) {
            return $this->back("bestallningar/{$id}", "Order {$order['reference']} är inte betald och lämnas inte över.", 'error');
        }

        try {
            $ok = service('orderService')->resend($id);
        } catch (Throwable $e) {
            log_message('error', 'Admin: omförsök för order {ref} misslyckades: {msg}', ['ref' => $order['reference'], 'msg' => $e->getMessage()]);
            $ok = false;
        }

        return $ok
            ? $this->back("bestallningar/{$id}", "Order {$order['reference']} är skickad till swing-order.")
            : $this->back("bestallningar/{$id}", "Order {$order['reference']} kunde inte skickas. Se felet nedan.", 'error');
    }

    private function find(int $id): array
    {
        $order = model(OrderModel::class)->find($id);

        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Beställningen finns inte.');
        }

        return $order;
    }
}
