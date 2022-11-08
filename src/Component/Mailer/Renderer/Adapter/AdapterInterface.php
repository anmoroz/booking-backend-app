<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\Component\Mailer\Renderer\Adapter;


use App\Component\Mailer\Model\EmailInterface;
use App\Component\Mailer\Renderer\RenderedEmail;

interface AdapterInterface
{
    public function render(EmailInterface $email, array $data = []): RenderedEmail;
}