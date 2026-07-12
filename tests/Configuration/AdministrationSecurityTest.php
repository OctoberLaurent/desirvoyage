<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class AdministrationSecurityTest extends TestCase
{
    public function testAdminCrudNeverDisplaysThePasswordHash(): void
    {
        $crud = (string) file_get_contents(dirname(__DIR__, 2).'/src/Controller/Admin/UserCrudController.php');

        self::assertStringNotContainsString("TextField::new('password'", $crud);
    }

    public function testInvoiceActionsUseTheReservationVoter(): void
    {
        $controller = (string) file_get_contents(dirname(__DIR__, 2).'/src/Controller/InvoiceController.php');

        self::assertSame(2, substr_count($controller, "#[IsGranted(ReservationVoter::VIEW, subject: 'reservation')]"));
    }
}
