<?php

namespace App\Controllers\Admin;

use App\Models\CompetitionModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Tävlingar (CONTENT-07, TECH-05). Endast publicerade visas på sajten.
 */
class Competitions extends BaseAdminController
{
    private const FIELDS = [
        'title', 'slug', 'visibility', 'status', 'format', 'summary', 'description',
        'starts_at', 'ends_at', 'registration_info', 'registration_url', 'price_info',
        'terms', 'terms_url', 'results_url',
    ];

    public function index(): string
    {
        $model  = model(CompetitionModel::class);
        $status = (string) $this->request->getGet('status');

        if (array_key_exists($status, CompetitionModel::STATUSES)) {
            $model->where('status', $status);
        }

        return $this->render('competitions/index', [
            'title'        => 'Tävlingar',
            'competitions' => $model->orderBy('starts_at IS NULL', 'DESC', false)->orderBy('starts_at', 'DESC')->findAll(),
            'status'       => $status,
        ], 'tavlingar');
    }

    public function new(): string
    {
        return $this->form(['visibility' => 'public', 'status' => 'draft']);
    }

    public function create(): string|RedirectResponse
    {
        $data  = $this->input();
        $model = model(CompetitionModel::class);

        if (! $model->insert($data)) {
            return $this->form($data, $model->errors());
        }

        $this->clearPageCache();

        return $this->back('tavlingar', "Tävlingen “{$data['title']}” är sparad.");
    }

    public function edit(int $id): string
    {
        return $this->form($this->find($id), [], $id);
    }

    public function update(int $id): string|RedirectResponse
    {
        $this->find($id);
        $data  = $this->input();
        $model = model(CompetitionModel::class);

        // {id} i is_unique-regeln kräver att id finns i datan och har en egen regel.
        $model->setValidationRule('id', 'required|is_natural_no_zero');

        if (! $model->update($id, $data + ['id' => $id])) {
            return $this->form($data, $model->errors(), $id);
        }

        $this->clearPageCache();

        return $this->back('tavlingar', "Tävlingen “{$data['title']}” är uppdaterad.");
    }

    public function delete(int $id): RedirectResponse
    {
        $competition = $this->find($id);
        model(CompetitionModel::class)->delete($id);
        $this->clearPageCache();

        return $this->back('tavlingar', "Tävlingen “{$competition['title']}” är raderad.");
    }

    private function form(array $competition, array $errors = [], ?int $id = null): string
    {
        return $this->render('competitions/form', [
            'title'       => $id === null ? 'Ny tävling' : 'Redigera tävling',
            'competition' => $competition,
            'errors'      => $errors,
            'id'          => $id,
        ], 'tavlingar');
    }

    private function find(int $id): array
    {
        $competition = model(CompetitionModel::class)->find($id);

        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Tävlingen finns inte.');
        }

        return $competition;
    }

    /** Formulärdata → kolumner. Slug föreslås från titeln om fältet är tomt. */
    private function input(): array
    {
        $data = $this->nullify($this->request->getPost(self::FIELDS));

        $data['title'] ??= '';
        $data['slug']  = self::slugify($data['slug'] ?? $data['title']);

        foreach (['starts_at', 'ends_at'] as $field) {
            $data[$field] = self::toDateTime($data[$field]);
        }

        return $data;
    }

    /** "Veckogolfen – Vår 2026" → "veckogolfen-var-2026" (å/ä/ö blir a/a/o). */
    private static function slugify(string $text): string
    {
        helper('text');

        return url_title(convert_accented_characters(strtr(mb_strtolower($text), ['å' => 'a', 'ä' => 'a', 'ö' => 'o'])), '-', true);
    }

    /** datetime-local (2026-01-18T08:00) → 2026-01-18 08:00:00. Ogiltigt lämnas för validering. */
    private static function toDateTime(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i', $value);

        return $date !== false ? $date->format('Y-m-d H:i:s') : $value;
    }
}
