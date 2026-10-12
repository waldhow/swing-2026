<?php

namespace App\Libraries;

use App\Models\InquiryModel;
use Config\Inquiries;
use Config\Swing;
use Throwable;

/**
 * Sparar offert- och kontaktförfrågningar och mejlar dem till Swing (TECH-04).
 * Förfrågan sparas alltid; misslyckas mejlet syns den ändå i admin.
 */
final class InquiryService
{
    public function __construct(
        private readonly InquiryModel $inquiries,
        private readonly Inquiries $config,
        private readonly Swing $swing,
    ) {
    }

    /**
     * @return array<string, mixed> den sparade förfrågan
     */
    public function submit(array $data): array
    {
        $this->inquiries->purgeExpired($this->config->retentionMonths);

        $id      = $this->inquiries->insert($data + ['status' => 'new'], true);
        $inquiry = $this->inquiries->find($id);

        try {
            $email = service('email')->clear(true);
            $email->setTo($this->config->notifyTo);
            $email->setReplyTo($inquiry['email'], $inquiry['name']);
            $email->setSubject(sprintf('%s från %s', InquiryModel::TYPES[$inquiry['type']] ?? 'Förfrågan', $inquiry['name']));
            $email->setMailType('text');
            $email->setMessage(view('emails/inquiry_notify', ['inquiry' => $inquiry, 'swing' => $this->swing], ['debug' => false]));

            if ($email->send(false)) {
                $this->inquiries->update($id, ['mailed_at' => date('Y-m-d H:i:s'), 'mail_error' => null]);
            } else {
                $this->inquiries->update($id, ['mail_error' => 'Mejlet kunde inte skickas.']);
                log_message('error', 'Förfrågan {id} kunde inte mejlas: {debug}', ['id' => $id, 'debug' => strip_tags($email->printDebugger(['headers']))]);
            }
        } catch (Throwable $e) {
            $this->inquiries->update($id, ['mail_error' => mb_substr($e->getMessage(), 0, 255)]);
            log_message('error', 'Förfrågan {id} kunde inte mejlas: {msg}', ['id' => $id, 'msg' => $e->getMessage()]);
        }

        return $this->inquiries->find($id);
    }
}
