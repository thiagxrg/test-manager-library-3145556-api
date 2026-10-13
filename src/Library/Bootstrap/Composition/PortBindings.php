<?php

namespace Library\Bootstrap\Composition;

use Illuminate\Contracts\Foundation\Application;
use Library\Adapters\Outbound\Clock\SystemClock;
use Library\Adapters\Outbound\Persistence\DbTransactionManager;
use Library\Adapters\Outbound\Persistence\EloquentBookRepository;
use Library\Adapters\Outbound\Persistence\EloquentCategoryRepository;
use Library\Adapters\Outbound\Persistence\EloquentLoanRepository;
use Library\Adapters\Outbound\Persistence\EloquentMemberRepository;
use Library\Adapters\Outbound\Persistence\EloquentReportReader;
use Library\Adapters\Outbound\Persistence\EloquentUserRepository;
use Library\Adapters\Outbound\Security\BcryptPasswordHasher;
use Library\Adapters\Outbound\Security\SanctumTokenIssuer;
use Library\Adapters\Outbound\Storage\DiskCoverStorage;
use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CategoryRepository;
use Library\Application\Ports\Outbound\Clock;
use Library\Application\Ports\Outbound\CoverStorage;
use Library\Application\Ports\Outbound\LoanRepository;
use Library\Application\Ports\Outbound\MemberRepository;
use Library\Application\Ports\Outbound\PasswordHasher;
use Library\Application\Ports\Outbound\ReportReader;
use Library\Application\Ports\Outbound\TokenIssuer;
use Library\Application\Ports\Outbound\TransactionManager;
use Library\Application\Ports\Outbound\UserRepository;

/**
 * The single place where a port meets its adapter (constitution Article III).
 * No infrastructure class is instantiated anywhere outside of this binding.
 */
final class PortBindings
{
    public static function register(Application $app): void
    {
        $app->bind(Clock::class, SystemClock::class);
        $app->bind(PasswordHasher::class, BcryptPasswordHasher::class);
        $app->bind(TokenIssuer::class, SanctumTokenIssuer::class);
        $app->bind(CoverStorage::class, DiskCoverStorage::class);
        $app->bind(TransactionManager::class, DbTransactionManager::class);

        $app->bind(BookRepository::class, EloquentBookRepository::class);
        $app->bind(MemberRepository::class, EloquentMemberRepository::class);
        $app->bind(UserRepository::class, EloquentUserRepository::class);
        $app->bind(LoanRepository::class, EloquentLoanRepository::class);
        $app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $app->bind(ReportReader::class, EloquentReportReader::class);
    }
}
