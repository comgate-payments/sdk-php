# Functional/Integration Coverage Report

Generated from:

```bash
XDEBUG_MODE=coverage vendor/bin/codecept run -v --coverage --coverage-xml coverage.xml --coverage-html
```

## Baseline and validity

The requested command initially stopped because Xdebug coverage mode was disabled. Re-running it with `XDEBUG_MODE=coverage` generated:

- HTML: `tests/_output/coverage/index.html`
- Clover XML: `tests/_output/coverage.xml`
- 65 tests, 1,085 assertions
- 26 passed, 36 errors, 3 failures
- classes: 6.58% (5/76)
- methods: 26.93% (188/698)
- lines: 25.53% (437/1,712)

This is a partial baseline, not the SDK's healthy-suite coverage. The configured API service was unavailable. Most `ClientCest` cases therefore stopped in `Transport`, and `PaymentMethodSyncCest` could not load/parse the remote OpenAPI document. The three assertion failures are consequences of the same outage: tests expected an API exception but received a connection exception.

Before using future percentages for acceptance decisions, restore the service/DNS route and rerun. Do not add tests merely to replace the existing live API cases that failed in this run.

## Recommended test architecture

Add a functional HTTP contract suite backed by a local fixture server (or an existing containerized fake Comgate service). Exercise the public SDK clients with the real `Transport`; return controlled HTTP headers and bodies from the fixture. This remains an integration/functional test because it crosses the public client, request DTO, cURL transport, PSR response/stream, and response DTO boundaries. It also avoids brittle dependence on a remote sandbox for most coverage.

Keep a small, separately tagged live-API smoke suite for genuine Comgate compatibility. Do not replace the OpenAPI synchronization check with coverage-oriented tests; stabilize it by using a versioned OpenAPI fixture for the normal suite and optionally verify the live document in a scheduled contract job.

## Prioritized backlog

### P0 — deterministic payment API journeys

Create table-driven integration scenarios against the fixture server for these public `Client` calls:

1. `createPayment` returns a successful JSON payload; assert the received method, Basic Auth header, JSON body, and hydrated transaction ID/redirect.
2. `getStatus` returns a payload containing every optional status field (fee, card fields, payer fields, error reason); assert representative typed values and `toArray()`.
3. `cancelPayment`, `capturePreauth`, `cancelPreauth`, `refundPayment`, `initRecurringPayment`, and `simulation` each assert the HTTP verb, URN, serialized request, and hydrated success response.
4. For one operation, return an API error payload and assert the SDK's domain exception mapping. Add missing-parameter and payment-not-found payloads if the protocol distinguishes them.
5. For one GET and one POST/PUT, return HTTP 4xx and 5xx responses with a recording logger and assert observable SDK behavior plus error/critical log classification.

Why first: this traverses the central production path and can recover large uncovered blocks in `Client` (16/76 lines currently), `Transport` (28/102), request objects, and response objects. `PaymentStatusResponse` alone has 141 uncovered executable lines; use an all-fields payload rather than tests of individual getters.

### P0 — terminal end-to-end workflows

Add `ClientTerminalCest` using the same HTTP fixture:

1. Payment lifecycle: create, get status, cancel.
2. Refund lifecycle: create, get status, cancel.
3. Closing and terminal-status calls.
4. Assert that status/cancel calls replace `{transId}` and URL-encode or reject unsafe IDs according to the intended contract.
5. Use full status fixtures so nested/optional fields and `toArray()` are observed through public client calls.

Why first: the entire terminal surface is uncovered: `ClientTerminal` 0/26 lines, `TerminalPaymentStatusResponse` 0/60, `TerminalRefundStatusResponse` 0/44, plus the terminal request/entity/response classes. Eight compact journey scenarios should cover most of this family without unit tests.

### P1 — transfer and downloadable-file workflows

Add fixture-backed public-client scenarios for:

1. `transferList` with multiple transfers and `singleTransfer` with multiple payments; assert collection count and representative hydrated fields.
2. CSV and ABO single-transfer responses with successful file content and API-error content.
3. `getCsvDownload` and `getAboDownload` with attachment headers, filename parsing, non-empty binary/text content, ABO type/encoding variants, and write-to-temporary-file behavior where exposed.
4. Apple domain association with and without method/currency filters and a successful file payload.

Why next: this covers related behavior in one coherent workflow, including `Transfer` (0/21), file response variants, download requests, `AppleDomainAssociationRequest` (0/19), and current gaps in `FileResponse`. Use temporary paths and public operations; do not directly test DTO getters in isolation.

### P1 — MOTO encrypted-payment journey

Add public `createMotoPayment` integration scenarios with a fixture endpoint that first returns a generated test RSA public JWK and then captures the payment request:

1. Valid card: decrypt captured fields with the test private key and assert number, expiry, and CVV round-trip.
2. Missing public key response.
3. Missing card number.
4. Missing expiration; optionally verify that CVV is optional if that is the intended API contract.

Why next: one workflow exercises `PaymentCard` (0/23), `MotoPayment` (0/12), public-key request/response (0/26 combined), MOTO request, encryption branches, and `MotoPaymentCreateResponse` (0/34).

### P2 — transport protocol compatibility

Add black-box integration scenarios through `Client`/`ClientTerminal` (or a small public test-only endpoint adapter if necessary):

1. Response with several headers, repeated headers, and a body; verify downstream file/JSON parsing to cover header splitting and PSR adapters.
2. Body-only response without the `\r\n\r\n` separator.
3. Empty body, malformed JSON, and truncated response; assert the documented exception or fallback behavior.
4. Connection refusal/timeout to a deliberately unused local port; assert `ComgateException` and logger output.
5. Run one case with the `sdk-github` environment headers, supplying fake Cloudflare credentials and asserting both authorization header sets reach the fixture.

Why later: `PsrStream` (0/31), `PsrResponse` (0/23), `Response` (0/7), and `Query` (0/7) are completely uncovered, while most transport branches are also uncovered. These tests should validate protocol behavior from an SDK boundary, not become unit tests for each PSR method.

## Suggested implementation order

1. Build the reusable local HTTP fixture and request recorder.
2. Add payment happy paths plus one API error and one connection error.
3. Add terminal workflows.
4. Add transfer/download workflows.
5. Add MOTO encryption.
6. Add the remaining transport edge cases.
7. Rerun the full suite with a healthy configured API service and compare Clover totals after each group.

## Coverage target guidance

Track line gains by workflow, but gate on behavior rather than a raw percentage. A practical first milestone is to cover every public operation in `Client` and `ClientTerminal` through at least one successful integration path and every transport verb through success plus one failure path. The current report shows 1,275 uncovered executable lines; the P0 groups address the largest cohesive production paths and should be implemented before isolated low-line-count classes.

## Exclusions

The following are intentionally not recommended:

- direct tests of individual getters/setters, constructors, enums, or private methods;
- mocks of `ITransport` solely to assert that a client method called it;
- reflection-only coverage tests;
- duplicating live API scenarios solely because the service was unavailable during this run.

Those approaches are unit tests or coverage gaming and do not meet the requested functional/integration scope.
