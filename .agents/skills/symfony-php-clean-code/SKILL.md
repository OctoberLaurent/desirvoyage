---
name: symfony-php-clean-code
description: >
  Symfony 7 + PHP 8.3+ clean code, design patterns (GoF), SOLID principles, DDD-lite architecture,
  testing (PHPUnit), and best practices. Use BEFORE writing or reviewing Symfony/PHP code,
  generating boilerplate, advising on architecture, or doing code review on a PHP/Symfony project.
  Covers controllers, services, entities, repositories, value objects, DTOs, Messenger, API Platform, Doctrine.
license: MIT
compatibility: Requires PHP 8.3+, Symfony 7.x. PHPUnit 10+ for testing. PHPStan level >= 6 recommended.
---

# Symfony PHP — Clean Code, Design Patterns & Best Practices

## Setup

No installation needed — this is a reference skill. Load it before writing or reviewing Symfony/PHP code.

## Quick reference

| Topic | Section |
|---|---|
| SOLID + GRASP | [Section 1](#1-solid--grasp) |
| GoF Design Patterns | [Section 2](#2-design-patterns-gof) |
| Architecture Symfony | [Section 3](#3-architecture-symfony) |
| Testing | [Section 4](#4-testing) |
| PHP 8.3+ Modern Syntax | [Section 5](#5-php-83-modern-syntax) |
| Naming conventions | [Section 6](#6-naming-conventions) |
| Anti-patterns | [Section 7](#7-anti-patterns-to-avoid) |
| Symfony-specific practices | [Section 8](#8-symfony-specific-best-practices) |
| Code review checklist | [Section 9](#9-code-review-checklist) |

---

## 1. SOLID + GRASP

### S — Single Responsibility (SRP)

One class = one reason to change. In Symfony:

- **Controller**: orchestrates HTTP only — no business logic. Delegate to a service.
- **Service**: one clear business responsibility (e.g. `InvoiceCalculator`, not `InvoiceManager`).
- **Entity**: holds state, not complex business behavior.
- **Repository**: persistence only, no business rules.

```php
// BAD
class OrderController extends AbstractController
{
    public function create(Request $req): Response
    {
        $order = new Order();
        $order->setTotal($req->get('amount') * 1.2); // business logic in controller
        $em->persist($order);
        $em->flush();
        $this->sendEmail($order); // email in controller
    }
}

// GOOD
class OrderController extends AbstractController
{
    public function __construct(private readonly OrderService $orderService) {}

    public function create(Request $req): Response
    {
        $order = $this->orderService->createFromRequest($req);
        return $this->json($order, 201);
    }
}
```

### O — Open/Closed (OCP)

Extensible without modification. Use:
- Interfaces + DI (swappable strategies)
- Event subscribers / dispatchers (react without modifying existing code)
- Decorators (add behavior without touching the class)

### L — Liskov Substitution (LSP)

Subclasses must honor the parent contract:
- Don't throw undocumented exceptions
- Don't weaken preconditions
- Don't strengthen postconditions

### I — Interface Segregation (ISP)

Prefer several small specialized interfaces over one large one.

```php
// BAD
interface DataProvider
{
    public function read(): array;
    public function write(array $data): void;
    public function delete(int $id): void;
}

// GOOD
interface DataReader { public function read(): array; }
interface DataWriter { public function write(array $data): void; }
interface DataDeleter { public function delete(int $id): void; }
```

### D — Dependency Inversion (DIP)

Depend on abstractions, not implementations. Symfony does this natively via autowiring.

```php
// BAD
class PaymentService
{
    public function process(): void
    {
        $gateway = new StripeGateway(); // tight coupling
    }
}

// GOOD
class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway
    ) {}
}
```

---

## 2. Design Patterns (GoF)

### Creational

#### Factory Method

```php
interface NotificationFormatterInterface
{
    public function format(Notification $n): string;
    public function supports(string $channel): bool;
}

class EmailFormatter implements NotificationFormatterInterface
{
    public function supports(string $channel): bool { return $channel === 'email'; }
    public function format(Notification $n): string { return $n->getMessage(); }
}

class SmsFormatter implements NotificationFormatterInterface
{
    public function supports(string $channel): bool { return $channel === 'sms'; }
    public function format(Notification $n): string { return mb_substr($n->getMessage(), 0, 160); }
}

class NotificationFormatterFactory
{
    /** @param iterable<NotificationFormatterInterface> $formatters */
    public function __construct(private readonly iterable $formatters) {}

    public function getFormatter(string $channel): NotificationFormatterInterface
    {
        foreach ($this->formatters as $formatter) {
            if ($formatter->supports($channel)) return $formatter;
        }
        throw new \InvalidArgumentException("No formatter for: $channel");
    }
}
```

#### Builder

```php
class QueryBuilder
{
    private array $criteria = [];
    private ?int $limit = null;

    public function where(string $field, mixed $value): static
    {
        $this->criteria[$field] = $value;
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function build(): array
    {
        return ['criteria' => $this->criteria, 'limit' => $this->limit];
    }
}

// Usage: (new QueryBuilder())->where('status', 'active')->limit(10)->build();
```

### Structural

#### Decorator

```php
interface CacheInterface
{
    public function get(string $key): ?string;
    public function set(string $key, string $value): void;
}

class LoggingCacheDecorator implements CacheInterface
{
    public function __construct(
        private readonly CacheInterface $inner,
        private readonly LoggerInterface $logger
    ) {}

    public function get(string $key): ?string
    {
        $this->logger->info("Cache get: $key");
        return $this->inner->get($key);
    }

    public function set(string $key, string $value): void
    {
        $this->logger->info("Cache set: $key");
        $this->inner->set($key, $value);
    }
}

// config/services.yaml
# App\Service\LoggingCacheDecorator:
#   decorates: App\Service\CacheInterface
#   arguments: ['@.inner', '@logger']
```

#### Adapter

```php
interface PaymentGatewayInterface
{
    public function charge(int $cents, string $currency): bool;
}

class StripeAdapter implements PaymentGatewayInterface
{
    public function __construct(private readonly StripeClient $stripe) {}

    public function charge(int $cents, string $currency): bool
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount' => $cents,
            'currency' => $currency,
        ]);
        return $intent->status === 'succeeded';
    }
}
```

#### Facade

```php
class CheckoutFacade
{
    public function __construct(
        private readonly CartService $cart,
        private readonly InventoryService $inventory,
        private readonly PaymentService $payment,
        private readonly OrderService $order
    ) {}

    public function checkout(User $user, string $paymentMethod): Order
    {
        $items = $this->cart->getItems($user);
        $this->inventory->reserve($items);
        $payment = $this->payment->process($user, $paymentMethod);
        return $this->order->create($user, $items, $payment);
    }
}
```

### Behavioral

#### Strategy

```php
interface PricingStrategyInterface
{
    public function calculate(Order $order): int;
}

class RegularPricing implements PricingStrategyInterface
{
    public function calculate(Order $order): int { return $order->getSubtotal(); }
}

class DiscountPricing implements PricingStrategyInterface
{
    public function calculate(Order $order): int
    {
        return (int)($order->getSubtotal() * 0.9);
    }
}

class OrderProcessor
{
    public function __construct(private readonly PricingStrategyInterface $pricing) {}

    public function process(Order $order): int
    {
        return $this->pricing->calculate($order);
    }
}
```

#### Observer (Symfony EventDispatcher)

```php
final readonly class OrderPlacedEvent
{
    public function __construct(public Order $order) {}
}

// Dispatch
$this->eventDispatcher->dispatch(new OrderPlacedEvent($order));

// Subscribe
class SendOrderConfirmationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [OrderPlacedEvent::class => 'onOrderPlaced'];
    }

    public function onOrderPlaced(OrderPlacedEvent $event): void
    {
        $this->mailer->send($event->order->getUser()->getEmail(), 'Order confirmed');
    }
}
```

#### Command (Messenger)

```php
// Command (immutable DTO)
final readonly class SendInvoiceCommand
{
    public function __construct(public string $orderId, public string $email) {}
}

// Handler
class SendInvoiceHandler
{
    public function __construct(
        private readonly InvoiceGenerator $generator,
        private readonly MailerInterface $mailer
    ) {}

    public function __invoke(SendInvoiceCommand $command): void
    {
        $invoice = $this->generator->generate($command->orderId);
        $this->mailer->send($command->email, $invoice);
    }
}

// Dispatch
$bus->dispatch(new SendInvoiceCommand($orderId, $email));
```

#### State

```php
interface OrderStateInterface
{
    public function canTransitionTo(string $newState): bool;
    public function nextState(): ?OrderStateInterface;
}

class PendingState implements OrderStateInterface
{
    public function canTransitionTo(string $newState): bool
    {
        return in_array($newState, ['paid', 'cancelled']);
    }

    public function nextState(): ?OrderStateInterface
    {
        return new PaidState();
    }
}
```

---

## 3. Architecture Symfony

### DDD-lite folder structure

```
src/
├── Controller/           # HTTP layer only
├── Domain/               # Business logic (framework-agnostic)
│   ├── Model/            # Entities, Value Objects
│   ├── Service/          # Domain services
│   └── Exception/        # Domain exceptions
├── Infrastructure/       # Framework-specific
│   ├── Persistence/      # Doctrine repositories
│   └── Api/              # API Platform resources
├── Application/          # Use cases (CQRS)
│   ├── Command/          # Write-side commands
│   ├── Query/            # Read-side queries
│   └── Handler/          # Command/Query handlers
└── Kernel.php
```

### Controller rules

- Thin controllers: 5-10 lines max per action
- Always type-hint the return (`Response`, `JsonResponse`)
- Use `#[Route]` attributes (not YAML routes)
- Validate input with Validator (`#[Assert\...]`)
- Use DTOs for API form payloads

```php
#[Route('/api/orders', name: 'order_create', methods: ['POST'])]
public function create(
    #[MapRequestPayload] CreateOrderDto $dto
): JsonResponse
{
    $order = $this->orderService->create($dto);
    return $this->json($order, 201);
}
```

### Service rules

- One service = one responsibility (SRP)
- `readonly` on all injected properties
- Stateless by default
- Domain-specific exceptions (not generic `\Exception`)

```php
class InvoiceCalculator
{
    public function __construct(
        private readonly TaxRateProvider $taxProvider,
        private readonly DiscountPolicy $discountPolicy,
    ) {}

    public function calculate(Order $order): Invoice
    {
        $subtotal = $order->getSubtotal();
        $discount = $this->discountPolicy->apply($order);
        $tax = $this->taxProvider->getRate($order->getCountry());
        return new Invoice($subtotal, $discount, $tax);
    }
}
```

### Entity rules

- No business logic in entity — just state + simple invariants
- Constructor with required parameters
- Setters only for mutable fields
- Value Objects for business concepts (Money, Address, Email)

```php
#[Entity]
class Order
{
    #[Id, Column(type: 'uuid')]
    private Uuid $id;

    #[Column]
    private string $status;

    #[Embedded]
    private Money $total;

    public function __construct(Money $total)
    {
        $this->id = Uuid::v7();
        $this->status = 'pending';
        $this->total = $total;
    }

    public function markAsPaid(): void
    {
        if ($this->status !== 'pending') {
            throw new \DomainException('Order is not pending');
        }
        $this->status = 'paid';
    }
}
```

### Value Objects (immutable)

```php
#[Embeddable]
final readonly class Money
{
    public function __construct(
        #[Column] private int $amount,
        #[Column] private string $currency
    ) {}

    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Currency mismatch');
        }
    }
}
```

### Repository rules

- Always define an interface
- Expressive method names (not generic `findByX` when avoidable)
- No business logic, just persistence

```php
interface OrderRepositoryInterface
{
    public function find(Uuid $id): ?Order;
    public function findPendingForUser(User $user): array;
    public function save(Order $order): void;
    public function remove(Order $order): void;
}
```

---

## 4. Testing

### PHPUnit + KernelTestCase

```php
class OrderServiceTest extends KernelTestCase
{
    private OrderService $service;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->service = $container->get(OrderService::class);
    }

    public function testCreateOrderFromValidData(): void
    {
        $dto = new CreateOrderDto(amount: 1000, currency: 'EUR');
        $order = $this->service->create($dto);

        $this->assertSame('pending', $order->getStatus());
        $this->assertSame(1000, $order->getTotal()->getAmount());
    }
}
```

### Test doubles

```php
class InvoiceCalculatorTest extends TestCase
{
    public function testCalculateWithDiscount(): void
    {
        $taxProvider = $this->createMock(TaxRateProvider::class);
        $taxProvider->method('getRate')->willReturn(0.2);

        $discountPolicy = $this->createMock(DiscountPolicy::class);
        $discountPolicy->method('apply')->willReturn(100);

        $calculator = new InvoiceCalculator($taxProvider, $discountPolicy);
        $order = $this->createOrder(1000);

        $invoice = $calculator->calculate($order);

        $this->assertSame(900, $invoice->getTaxableAmount());
        $this->assertSame(180, $invoice->getTax());
    }
}
```

---

## 5. PHP 8.3+ Modern Syntax

### Readonly properties & promotion

```php
class Foo
{
    public function __construct(
        private readonly Bar $bar,
        private readonly Baz $baz,
    ) {}
}
```

### Enums (no magic constants)

```php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Paid, self::Cancelled]),
            self::Paid => false,
            self::Cancelled => false,
        };
    }
}
```

### Match expressions

```php
// BAD
switch ($status) {
    case 'pending': $label = 'En attente'; break;
    case 'paid': $label = 'Payée'; break;
    default: $label = 'Inconnu';
}

// GOOD
$label = match ($status) {
    'pending' => 'En attente',
    'paid' => 'Payée',
    default => 'Inconnu',
};
```

### Named arguments

```php
$this->orderService->create(
    user: $user,
    items: $items,
    paymentMethod: 'stripe',
    notify: true,
);
```

### First-class callable syntax

```php
$callable = $this->orderService->create(...);
$callable($user, $items);
```

### Readonly classes (immutable DTOs)

```php
final readonly class CreateOrderDto
{
    public function __construct(
        public int $amount,
        public string $currency,
        public ?string $coupon = null,
    ) {}
}
```

---

## 6. Naming Conventions

| Concept | Convention | Example |
|---|---|---|
| Class | PascalCase | `OrderService` |
| Method | camelCase | `calculateTotal()` |
| Property | camelCase | `$orderRepository` |
| Interface | suffix `Interface` | `OrderRepositoryInterface` |
| Abstract class | prefix `Abstract` | `AbstractRepository` |
| Exception | suffix `Exception` | `OrderNotFoundException` |
| Event | suffix `Event` | `OrderPlacedEvent` |
| Subscriber | suffix `Subscriber` | `OrderPlacedSubscriber` |
| Command (Messenger) | suffix `Command` | `SendInvoiceCommand` |
| Handler | suffix `Handler` | `SendInvoiceHandler` |
| Value Object | concept name | `Money`, `Address` |
| Enum | singular | `OrderStatus` |
| Test | suffix `Test` | `OrderServiceTest` |

---

## 7. Anti-patterns to Avoid

| Anti-pattern | Why it's bad | Solution |
|---|---|---|
| Fat controller | Business logic in controller | Delegate to service |
| God service | One service does everything | Split by responsibility |
| Anemic entity | Entity without behavior | Add invariants + transitions |
| Repository with logic | Business rules in repo | Domain service |
| Service locator (`$container->get()`) | Hidden coupling | Dependency injection |
| Static methods | Global state, hard to test | Injected service |
| Boolean parameter | Two behaviors in one method | Split into two methods |
| `instanceof` in code | Violates LSP/OCP | Polymorphism |
| Null return | Ambiguity | Null Object or Exception |
| `new` in a service | Tight coupling | Factory or DI |

---

## 8. Symfony-specific Best Practices

### Configuration

- **Services**: autowire + autoconfigure by default; only configure exceptions
- **Routes**: attributes (not YAML/XML)
- **Forms**: use DTOs, not entities directly
- **Security**: `#[IsGranted]` attribute, custom voters
- **Cache**: `#[Cache]` attribute, PSR-6 pools

### Messenger

- Immutable messages (readonly)
- Handlers with `__invoke(MessageClass $command)`
- Async for side-effects (email, API calls, PDF)
- Sync for pure transformations

### API Platform

- Resources via attributes (not YAML)
- DTOs with `#[ApiProperty]`
- Custom providers/state processors for logic
- Pagination by default

### Doctrine

- No DQL string concatenation (SQL injection)
- QueryBuilder with bound parameters
- `#[ORM\...]` attributes (not annotations)
- Versioned migrations, not `schema:update`
- Transaction boundary in services, not controllers

---

## 9. Code Review Checklist

- [ ] SRP respected (one responsibility per class)
- [ ] No business logic in controller
- [ ] Interfaces for external dependencies
- [ ] `readonly` on injected properties
- [ ] Domain-specific exceptions
- [ ] Unit tests for business logic
- [ ] No `new` in service (except Value Objects)
- [ ] Expressive naming (no abbreviations)
- [ ] No comment that explains bad code (code should explain itself)
- [ ] PHPStan level >= 6 passes
- [ ] No `dump()` / `dd()` left in code