<?php

namespace App\Tests\Configuration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class SecurityHardeningConfigurationTest extends KernelTestCase
{
    public function testFrameworkEnablesCsrfProtection(): void
    {
        $frameworkConfiguration = (string) file_get_contents(dirname(__DIR__, 2).'/config/packages/framework.yaml');

        self::assertMatchesRegularExpression('/^\s*csrf_protection:\s*true\s*$/m', $frameworkConfiguration);
    }

    public function testStateChangingRoutesOnlyAcceptPostRequests(): void
    {
        self::bootKernel();

        $router = static::getContainer()->get('router');
        $routes = $router->getRouteCollection();

        foreach ([
            'reservation_validate',
            'reservation_remove',
            'payment_charge',
            'user_resend_activation_token',
        ] as $routeName) {
            $route = $routes->get($routeName);

            self::assertNotNull($route, sprintf('La route %s doit être définie.', $routeName));
            self::assertSame(['POST'], $route->getMethods(), sprintf('La route %s doit refuser les GET.', $routeName));
        }
    }

    public function testAccountAndPasswordFormsCarryCsrfTokens(): void
    {
        $projectDir = dirname(__DIR__, 2);

        self::assertStringContainsString("csrf_token('forgotten_password')", (string) file_get_contents($projectDir.'/templates/user/forgotten_password.html.twig'));
        self::assertStringContainsString("csrf_token('payment-' ~ reservation.id)", (string) file_get_contents($projectDir.'/templates/payment/index.html.twig'));
        self::assertStringContainsString("csrf_token('reservation-validate')", (string) file_get_contents($projectDir.'/templates/reservation/summary.html.twig'));
        self::assertStringContainsString("csrf_token('reservation-remove')", (string) file_get_contents($projectDir.'/templates/reservation/summary.html.twig'));
    }
}
