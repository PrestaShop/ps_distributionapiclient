<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace PrestaShop\Module\DistributionApiClient\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\DistributionApiClient\DistributionApi;
use RuntimeException;

class DistributionApiTest extends TestCase
{
    #[DataProvider('provideVersionsOnDisk')]
    public function testDownloadModuleDownloadsOnlyWhenTheFilesAreOlder(?string $versionOnDisk, bool $expectDownload): void
    {
        $distributionApi = $this->getMockBuilder(DistributionApi::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getModuleList', 'getModuleDownloadUrl'])
            ->getMock();
        $distributionApi->method('getModuleList')->willReturn([
            ['name' => 'other_module', 'version_available' => '9.0.0', 'download_url' => 'https://example.com/other.zip'],
            ['name' => 'some_module', 'version_available' => '2.0.0', 'download_url' => 'https://example.com/some.zip'],
        ]);
        $distributionApi
            ->expects($expectDownload ? $this->once() : $this->never())
            ->method('getModuleDownloadUrl')
            ->with($this->callback(static fn (array $module): bool => 'some_module' === $module['name']))
            ->willThrowException(new RuntimeException('download started'));

        if ($expectDownload) {
            $this->expectExceptionMessage('download started');
        }

        $distributionApi->downloadModule('some_module', $versionOnDisk);
    }

    /**
     * @return iterable<string, array{?string, bool}>
     */
    public static function provideVersionsOnDisk(): iterable
    {
        yield 'unknown version on disk' => [null, true];
        yield 'older files on disk' => ['1.9.9', true];
        yield 'same version on disk' => ['2.0.0', false];
        yield 'newer files on disk' => ['2.1.0', false];
    }
}
