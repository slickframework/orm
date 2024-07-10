<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Persistence;

use Doctrine\ORM\Proxy\ProxyFactory;
use PHPUnit\Framework\Attributes\Test;
use Slick\Orm\Infrastructure\Persistence\ManagerSettings;
use PHPUnit\Framework\TestCase;

class ManagerSettingsTest extends TestCase
{
    private ManagerSettings $managerSettings;

    protected function setUp(): void
    {
        $this->managerSettings = new ManagerSettings([
            'url' => "pdo-mysql://user:password@host/database:3306?charset=utf8mb4&collation=utf8mb4_unicode_ci",
        ]);
    }

    #[Test]
    public function entityPaths(): void
    {
        $this->assertEquals(['/src/Domain'], $this->managerSettings->entityPaths());
    }

    #[Test]
    public function connectionSettings(): void
    {
        $this->assertEquals([
            'dbname' => 'database:3306',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'driver' => 'pdo_mysql',
            'host' => 'host',
            'user' => 'user',
            'password' => 'password'
        ], $this->managerSettings->connectionSettings());
    }

    #[Test]
    public function proxiesDir(): void
    {
        $this->assertEquals('/tmp/Proxies', $this->managerSettings->proxiesDir());
    }

    #[Test]
    public function proxiesNameSpace(): void
    {
        $this->assertEquals('App\Persistence\Proxies', $this->managerSettings->proxiesNameSpace());
    }

    #[Test]
    public function devMode(): void
    {
        $this->assertTrue($this->managerSettings->isInDevMode());
    }

    #[Test]
    public function cacheContainerId(): void
    {
        $this->assertNull($this->managerSettings->cacheContainerId());
    }

    #[Test]
    public function sqlLoggerContainerId(): void
    {
        $this->assertNull($this->managerSettings->sqlLoggerContainerId());
    }

    #[Test]
    public function autoGenerateProxiesMode(): void
    {
        $this->assertEquals(ProxyFactory::AUTOGENERATE_EVAL, $this->managerSettings->proxyGenerationMode());
    }

    #[Test]
    public function autoGenerateProxiesModeProd(): void
    {
        $managerSettings = new ManagerSettings(['devMode' => false]);
        $this->assertEquals(ProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS, $managerSettings->proxyGenerationMode());
    }
}
