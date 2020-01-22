<?php

namespace App\Tests\Domain\Post\Manager;

use App\Domain\Core\Exception\ConflictException;
use App\Domain\Core\Workflow\WorkflowProcessorInterface;
use App\Domain\Post\DTO\PostEdit;
use App\Domain\Post\Entity\Comment;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Manager\AbstractPostManager;
use App\Domain\Post\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbstractPostManagerTest extends TestCase
{
    protected const ARCHIVE_ACTION = 'archive';

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $workflowProcessor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $repository;

    /** @var AbstractPostManager */
    private $abstractPostManager;

    protected function setUp()
    {
        $this->workflowProcessor = $this->createMock(WorkflowProcessorInterface::class);
        $this->repository = $this->createMock(PostRepositoryInterface::class);

        $this->abstractPostManager = $this->getMockForAbstractClass(
            AbstractPostManager::class,
            [
                $this->workflowProcessor,
                $this->repository
            ]
        );
    }

    public function testRetrieve(): void
    {
        $id = 'id';
        $post = $this->createMock(Post::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($post)
        ;

        $this->assertEquals($post, $this->abstractPostManager->retrieve($id));
    }

    public function testRetrieveNotFound(): void
    {
        $id = 'id';

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willThrowException(new NotFoundHttpException())
        ;

        $this->expectException(NotFoundHttpException::class);

        $this->abstractPostManager->retrieve($id);
    }

    public function testRetrieveAll(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('retrieveAll')
            ->willReturn([])
        ;

        $this->assertEquals([], $this->abstractPostManager->retrieveAll());
    }

    public function testSave(): void
    {
        $post = $this->createMock(Post::class);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($post)
            ->willReturn($post)
        ;

        $this->assertEquals($post, $this->abstractPostManager->save($post));
    }

    public function testArchiveCannotBeArchived(): void
    {
        $id = 'id';
        $post = $this->createMock(Post::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($post)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->with($post, self::ARCHIVE_ACTION)
            ->willReturn(false)
        ;

        $this->expectException(ConflictException::class);

        $this->abstractPostManager->archive($id);
    }

    public function testArchive(): void
    {
        $id = 'id';
        $post = $this->createMock(Post::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieve')
            ->with($id)
            ->willReturn($post)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('can')
            ->with($post, self::ARCHIVE_ACTION)
            ->willReturn(true)
        ;

        $this->workflowProcessor
            ->expects($this->once())
            ->method('apply')
            ->with($post, self::ARCHIVE_ACTION)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($post)
            ->willReturn($post)
        ;

        $this->abstractPostManager->archive($id);
    }

    public function testRetrieveBy(): void
    {
        $options = ['site' => 'test'];
        $post = $this->createMock(Post::class);

        $this->repository
            ->expects($this->once())
            ->method('retrieveBy')
            ->with($options)
            ->willReturn([$post])
        ;

        $this->assertEquals([$post], $this->abstractPostManager->retrieveBy($options));
    }

    public function testGetUpdatedEntityFullDto(): void
    {
        $entity = $this->createMock(Post::class);
        $dto = $this->createMock(PostEdit::class);

        $dto
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn('title')
        ;

        $dto
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('description')
        ;

        $entity
            ->expects($this->once())
            ->method('setTitle')
            ->with('title')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setDescription')
            ->with('description')
            ->willReturn($entity)
        ;
        $entity
            ->expects($this->once())
            ->method('setUpdatedAt')
        ;

        $this->abstractPostManager->getUpdatedEntity($dto, $entity);
    }

    public function testGetUpdatedEntityPartDto(): void
    {
        $entity = $this->createMock(Post::class);
        $dto = $this->createMock(PostEdit::class);

        $dto
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn('title')
        ;

        $dto
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn(null)
        ;

        $entity
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('description')
        ;

        $entity
            ->expects($this->once())
            ->method('setTitle')
            ->with('title')
            ->willReturn($entity)
        ;

        $entity
            ->expects($this->once())
            ->method('setDescription')
            ->with('description')
            ->willReturn($entity)
        ;
        $entity
            ->expects($this->once())
            ->method('setUpdatedAt')
        ;

        $this->abstractPostManager->getUpdatedEntity($dto, $entity);
    }

    public function testAddComment(): void
    {
        $post = $this->createMock(Post::class);
        $comment = $this->createMock(Comment::class);

        $post
            ->expects($this->once())
            ->method('addComment')
            ->with($comment)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($post)
        ;

        $this->assertEquals($comment, $this->abstractPostManager->addComment($post, $comment));
    }
}
