<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Helpers\SortHelper;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Lang;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SortHelperTest extends TestCase
{
    private Container $originalContainer;

    /** @var MockInterface<Container> */
    private MockInterface $container;

    protected function setUp(): void
    {
        $this->originalContainer = Container::getInstance();
        $this->container = Mockery::mock(Container::class);
        Container::setInstance($this->container);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Container::setInstance($this->originalContainer);
    }

    public function testGetSortParametersReturnsDefaultValues(): void
    {
        $request = Request::create("/", "GET");

        $helper = new SortHelper($request);
        $result = $helper->getSortParameters();

        $this->assertSame(["id", "asc"], $result);
    }

    public function testGetSortParameters(): void
    {
        $request = Request::create("/?order=desc&sort=name", "GET");

        $helper = new SortHelper($request);
        $result = $helper->getSortParameters();

        $this->assertSame(["name", "desc"], $result);
    }

    public function testSort(): void
    {
        $request = Request::create("/?sort=name&order=asc", "GET");

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive("orderBy")->with("name", "asc")->once()->andReturnSelf();

        $helper = new SortHelper($request);
        $result = $helper->sort($builderMock, ["name", "email"], []);

        $this->assertSame($builderMock, $result);
    }

    public function testSortIgnoresIgnoredFields(): void
    {
        $request = Request::create("/?sort=ignored_field&order=asc", "GET");

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldNotReceive("orderBy");

        $helper = new SortHelper($request);
        $result = $helper->sort($builderMock, ["name", "email"], ["ignored_field"]);

        $this->assertSame($builderMock, $result);
    }

    public function testSortAppliesOrderBy(): void
    {
        $request = Request::create("/?sort=email&order=desc", "GET");

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive("orderBy")->with("email", "desc")->once()->andReturnSelf();

        $helper = new SortHelper($request);
        $result = $helper->sort($builderMock, ["name", "email"], ["ignored_field"]);

        $this->assertSame($builderMock, $result);
    }

    public function testSortThrowsExceptionForUnsupportedField(): void
    {
        $this->container->shouldReceive("abort")->andReturnUsing(fn(int $code, string $message) => throw HttpException::fromStatusCode($code, $message));

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("The field 'invalid_field' is not supported.");

        Lang::shouldReceive("get")
            ->with("validation.sorting.unsupported_field", ["attribute" => "invalid_field"])
            ->andReturn("The field 'invalid_field' is not supported.");

        $request = Request::create("/?sort=invalid_field&order=asc", "GET");
        $builderMock = Mockery::mock(Builder::class);

        $helper = new SortHelper($request);
        $result = $helper->sort($builderMock, ["name", "email"], []);

        $this->assertSame($builderMock, $result);
    }

    public function testSearch(): void
    {
        $request = Request::create("/?search=test", "GET");

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive("where")->with("name", "like", "%test%")->once()->andReturnSelf();

        $helper = new SortHelper($request);
        $result = $helper->search($builderMock, "name");

        $this->assertSame($builderMock, $result);
    }

    public function testSearchDoesNothingIfNoSearchText(): void
    {
        $request = Request::create("/", "GET");

        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldNotReceive("where");

        $helper = new SortHelper($request);
        $result = $helper->search($builderMock, "name");

        $this->assertSame($builderMock, $result);
    }

    public function testPaginateUsesDefaultLimit(): void
    {
        $request = Request::create("/", "GET");

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);
        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive("paginate")->with(50)->once()->andReturn($paginatorMock);

        $helper = new SortHelper($request);
        $result = $helper->paginate($builderMock);

        $this->assertSame($paginatorMock, $result);
    }

    public function testPaginateUsesCustomLimit(): void
    {
        $request = Request::create("/?limit=255", "GET");

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);
        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive("paginate")->with(255)->once()->andReturn($paginatorMock);

        $helper = new SortHelper($request);
        $result = $helper->paginate($builderMock);

        $this->assertSame($paginatorMock, $result);
    }

    public function testPaginateFallbacksToDefaultForInvalidLimit(): void
    {
        $request = Request::create("/?limit=-255", "GET");

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);
        $builderMock = Mockery::mock(Builder::class);
        $builderMock->shouldReceive("paginate")->with(50)->once()->andReturn($paginatorMock);

        $helper = new SortHelper($request);
        $result = $helper->paginate($builderMock);

        $this->assertSame($paginatorMock, $result);
    }
}
