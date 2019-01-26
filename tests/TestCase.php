<?php

namespace Tests;

use App\Models\FileGroup;
use App\Models\StoredFile;
use App\Models\User;
use App\Support\Facades\TempFile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Spatie\Snapshots\Drivers\JsonDriver;
use Spatie\Snapshots\MatchesSnapshots;
use Illuminate\Contracts\Console\Kernel;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots, CreatesUploadedFiles, CreatesModels;

    protected $snapshotDirectory = '/';

    public $testFilesStoragePath;

    public function setUp()
    {
        parent::setUp();

        $this->testFilesStoragePath = base_path('tests/Files/');
    }

    protected function getSnapshotDirectory(): string
    {
        $subDirectory = ltrim($this->snapshotDirectory, DIRECTORY_SEPARATOR);

        return base_path('tests/_snapshots_/'.$subDirectory);
    }

    protected function getFileSnapshotDirectory(): string
    {
        return $this->getSnapshotDirectory();
    }

    public function assertMatchesFileSnapshot($file)
    {
        if ($file instanceof StoredFile) {
            $temporaryFilePath = TempFile::makeFilePath().'.txt';

            // Git changes line endings to \n, but we save files with \r\n, so we have to change them
            $lines = preg_split("/\r\n|\n|\r/",
                file_get_contents($file->file_path)
            );

            file_put_contents($temporaryFilePath, implode("\n", $lines));

            $file = $temporaryFilePath;
        }

        $this->doFileSnapshotAssertion($file);
    }

    public function assertMatchesStoredFileSnapshot($storedFileId)
    {
        $storedFile = StoredFile::findOrFail($storedFileId);

        $this->assertMatchesFileSnapshot($storedFile);
    }

    public function assertMatchesJsonSnapshot($actual)
    {
        if ($actual instanceof TestResponse) {
            $actual = $actual->getContent();
        }

        $this->assertMatchesSnapshot($actual, new JsonDriver);
    }

    /**
     * Assert that the file job controller redirected to the file group result page.
     *
     * @param TestResponse $response
     * @param FileGroup $fileGroup
     */
    protected function assertSuccessfulFileJobRedirect(TestResponse $response, FileGroup $fileGroup)
    {
        $response->assertStatus(302)->assertRedirect($fileGroup->result_route);
    }

    public function assertNow($carbon)
    {
        $this->assertSame(
            (string) now(),
            (string) $carbon
        );

        return $this;
    }

    /**
     * @param null $user
     *
     * @return $this
     */
    protected function adminLogin($user = null)
    {
        $user = $user ?: factory(User::class)->create();

        return $this->actingAs($user);
    }

    protected function progressTimeInDays($days)
    {
        Carbon::setTestNow(
            now()->addDays($days)
        );

        return $this;
    }

    protected function progressTimeInHours($hours)
    {
        Carbon::setTestNow(
            now()->addHours($hours)
        );

        return $this;
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
