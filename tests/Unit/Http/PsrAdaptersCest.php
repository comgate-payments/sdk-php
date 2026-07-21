<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Codeception\Attribute\Group;
use Comgate\SDK\Http\PsrResponse;
use Comgate\SDK\Http\PsrStream;
use Tests\Support\UnitTester;

class PsrAdaptersCest
{
	// -----------------------------------------------------------------------
	// PsrStream
	// -----------------------------------------------------------------------

	#[Group('psr-stream')]
	public function psrStreamToStringTest(UnitTester $I): void
	{
		$stream = new PsrStream('hello world');

		$I->assertEquals('hello world', (string) $stream);
	}

	#[Group('psr-stream')]
	public function psrStreamGetSizeTest(UnitTester $I): void
	{
		$stream = new PsrStream('hello');

		$I->assertEquals(5, $stream->getSize());
	}

	#[Group('psr-stream')]
	public function psrStreamGetContentsTest(UnitTester $I): void
	{
		$stream = new PsrStream('some content');
		$stream->rewind();

		$I->assertEquals('some content', $stream->getContents());
	}

	#[Group('psr-stream')]
	public function psrStreamReadTest(UnitTester $I): void
	{
		$stream = new PsrStream('abcdefgh');
		$stream->rewind();

		$I->assertEquals('abc', $stream->read(3));
	}

	#[Group('psr-stream')]
	public function psrStreamWriteTest(UnitTester $I): void
	{
		$stream = new PsrStream('initial');
		$stream->seek(0, SEEK_END);
		$stream->write(' appended');

		$I->assertEquals('initial appended', (string) $stream);
	}

	#[Group('psr-stream')]
	public function psrStreamTellAfterRewindIsZeroTest(UnitTester $I): void
	{
		$stream = new PsrStream('data');
		$stream->rewind();

		$I->assertEquals(0, $stream->tell());
	}

	#[Group('psr-stream')]
	public function psrStreamIsSeekableReadableWritableTest(UnitTester $I): void
	{
		$stream = new PsrStream('x');

		$I->assertTrue($stream->isSeekable());
		$I->assertTrue($stream->isReadable());
		$I->assertTrue($stream->isWritable());
	}

	#[Group('psr-stream')]
	public function psrStreamEofAfterReadingAllTest(UnitTester $I): void
	{
		$stream = new PsrStream('hi');
		$stream->read(1000);

		$I->assertTrue($stream->eof());
	}

	#[Group('psr-stream')]
	public function psrStreamGetMetadataReturnsArrayWithNoKeyTest(UnitTester $I): void
	{
		$stream = new PsrStream('meta');
		$meta = $stream->getMetadata();

		$I->assertIsArray($meta);
		$I->assertArrayHasKey('stream_type', $meta);
	}

	#[Group('psr-stream')]
	public function psrStreamGetMetadataWithKeyReturnsValueTest(UnitTester $I): void
	{
		$stream = new PsrStream('meta');
		$type = $stream->getMetadata('stream_type');

		$I->assertNotNull($type);
	}

	#[Group('psr-stream')]
	public function psrStreamEmptyContentTest(UnitTester $I): void
	{
		$stream = new PsrStream('');

		$I->assertEquals('', (string) $stream);
		$I->assertEquals(0, $stream->getSize());
	}

	#[Group('psr-stream')]
	public function psrStreamDetachReturnsResourceTest(UnitTester $I): void
	{
		$stream = new PsrStream('data');
		$resource = $stream->detach();

		$I->assertIsResource($resource);
	}

	// -----------------------------------------------------------------------
	// PsrResponse
	// -----------------------------------------------------------------------

	#[Group('psr-response')]
	public function psrResponseDefaultProtocolVersionTest(UnitTester $I): void
	{
		$response = new PsrResponse();

		$I->assertEquals('1.1', $response->getProtocolVersion());
	}

	#[Group('psr-response')]
	public function psrResponseWithProtocolVersionTest(UnitTester $I): void
	{
		$response = new PsrResponse();
		$new = $response->withProtocolVersion('2.0');

		$I->assertEquals('2.0', $new->getProtocolVersion());
		$I->assertEquals('1.1', $response->getProtocolVersion()); // immutable
	}

	#[Group('psr-response')]
	public function psrResponseWithHeaderTest(UnitTester $I): void
	{
		$response = new PsrResponse();
		$new = $response->withHeader('Content-Type', 'application/json');

		$I->assertTrue($new->hasHeader('Content-Type'));
		$I->assertEquals(['application/json'], $new->getHeader('Content-Type'));
	}

	#[Group('psr-response')]
	public function psrResponseGetHeaderLineTest(UnitTester $I): void
	{
		$response = new PsrResponse();
		$new = $response->withHeader('Accept', ['text/html', 'application/json']);

		$I->assertEquals('text/html, application/json', $new->getHeaderLine('Accept'));
	}

	#[Group('psr-response')]
	public function psrResponseWithAddedHeaderTest(UnitTester $I): void
	{
		$response = (new PsrResponse())->withHeader('X-Custom', 'first');
		$new = $response->withAddedHeader('X-Custom', 'second');

		$I->assertCount(2, $new->getHeader('X-Custom'));
	}

	#[Group('psr-response')]
	public function psrResponseWithoutHeaderTest(UnitTester $I): void
	{
		$response = (new PsrResponse())->withHeader('X-Remove', 'value');
		$new = $response->withoutHeader('X-Remove');

		$I->assertFalse($new->hasHeader('X-Remove'));
	}

	#[Group('psr-response')]
	public function psrResponseHasHeaderReturnsFalseForMissingTest(UnitTester $I): void
	{
		$response = new PsrResponse();

		$I->assertFalse($response->hasHeader('X-Nonexistent'));
		$I->assertEquals([], $response->getHeader('X-Nonexistent'));
	}

	#[Group('psr-response')]
	public function psrResponseWithBodyTest(UnitTester $I): void
	{
		$stream = new PsrStream('response body');
		$response = (new PsrResponse())->withBody($stream);

		$I->assertEquals('response body', (string) $response->getBody());
	}

	#[Group('psr-response')]
	public function psrResponseImmutableOnWithHeaderTest(UnitTester $I): void
	{
		$original = new PsrResponse();
		$new = $original->withHeader('X-Test', 'value');

		$I->assertFalse($original->hasHeader('X-Test'));
		$I->assertTrue($new->hasHeader('X-Test'));
	}
}
